import cookie;
import std;
import vsthrottle;

sub vcl_recv {
    # Setup backend and standard request handling
    set req.backend_hint = application.backend();

    # Ignore search
    if (req.url ~ "\?s=") {
        return (pass);
    }

    # Remove common tracking parameters from the URL for better cache hit rate
    if (req.url ~ "(\?|&)(utm_source|utm_medium|utm_campaign|gclid|cx|ie|cof|siteurl)=") {
        set req.url = regsuball(req.url, "&(utm_source|utm_medium|utm_campaign|gclid|cx|ie|cof|siteurl)=([A-z0-9_\-\.%25]+)", "");
        set req.url = regsuball(req.url, "\?(utm_source|utm_medium|utm_campaign|gclid|cx|ie|cof|siteurl)=([A-z0-9_\-\.%25]+)", "?");
        set req.url = regsub(req.url, "\?&", "?");
        set req.url = regsub(req.url, "\?$", "");
    }

    # # Cookie parsing and handling
    # if (req.http.cookie) {
    #     cookie.parse(req.http.cookie);
    #     # set req.http.x-mmpersona = cookie.get("mmpersona"); #IGNORE FOR NOW
    #     //pass if cookie is set
    #     if(cookie.get("mmpersona")){
    #         return(pass);
    #     }
    # }

    # Handle "utm_term" or "utm_content" parameters
    if (req.url ~ "utm_term=|utm_content=") {
        set req.http.x-utm-parameters = regsuball(req.url, "([&?]utm_(term|content)=[^&]+)", "\1");
    }

    # Full cache purge logic for specific admin actions
    if (req.method == "PURGE") {
        if (req.url ~ "^/wp-admin/admin-post.php") {
            if (req.url ~ "action=purge_cache") {
            ban("req.http.host ~ .");
            return(synth(200, "Purged"));
            }
        }else{
            ban("req.http.host ~ .");
            return(synth(200, "Purged"));
        }
    }

    if (req.url ~ "vhp_flush_do=all") {
        # Logic to perform a full cache purge (if applicable)
        # You can include ban statements here to clear the entire cache
        ban("req.http.host ~ .");
    }
    
    if (req.url ~ "vhp_flush_do=req\.url") {
        # Logic to perform a cache purge for the specific URL (if applicable)
        # You can include ban statements here to clear cache for the given URL
        ban("obj.http.x-url == " + req.url + " && obj.http.x-host == " + req.http.host);
    }

    # Special handling for AJAX calls and non-GET/HEAD requests
    if (req.url ~ "^/admin-ajax.php" || req.http.X-Requested-With == "XMLHttpRequest") {
        set req.http.X-Cacheable = "NO:Ajax";
        return(pass);
    }

    if (req.method != "GET" && req.method != "HEAD") {
        set req.http.X-Cacheable = "NO:REQUEST-METHOD";
        return(pass);
    }

    # Static files handling: mark them for caching and strip cookies
    # X-Static-File is also used in vcl_backend_response to identify static files
    if (req.url ~ "^[^?]*\.(7z|avi|bmp|bz2|css|csv|doc|docx|eot|flac|flv|gif|gz|ico|jpeg|jpg|js|less|mka|mkv|mov|mp3|mpeg|mpe?g|mpg|odt|ogg|ogm|opus|otf|pdf|png|ppt|pptx|rar|rtf|svg|svgz|swf|tar|tbz|tgz|ttf|txt|txz|wav|webm|webp|woff|woff2|xls|xlsx|xml|xz|zip)(\?.*)?$") {
        set req.http.X-Static-File = "true";
        unset req.http.Cookie;
        return(hash);
    }

    # Bypass cache for logged-in users or specific URLs indicating dynamic content
    if (
        req.http.Cookie ~ "wordpress_(?!test_)[a-zA-Z0-9_]+|wp-postpass|comment_author_[a-zA-Z0-9_]+|woocommerce_cart_hash|woocommerce_items_in_cart|wp_woocommerce_session_[a-zA-Z0-9]+|wordpress_logged_in_|comment_author|PHPSESSID" ||
        req.http.Authorization ||
        req.url ~ "add_to_cart" ||
        req.url ~ "edd_action" ||
        req.url ~ "nocache" ||
        req.url ~ "^/addons" ||
        req.url ~ "^/admin-ajax.php" ||
        req.url ~ "^/bb-admin" ||
        req.url ~ "^/bb-login.php" ||
        req.url ~ "^/bb-reset-password.php" ||
        req.url ~ "^/cart" ||
        req.url ~ "^/checkout" ||
        req.url ~ "^/control.php" ||
        req.url ~ "^/login" ||
        req.url ~ "^/logout" ||
        req.url ~ "^/lost-password" ||
        req.url ~ "^/my-account" ||
        req.url ~ "^/product" ||
        req.url ~ "^/register" ||
        req.url ~ "^/register.php" ||
        req.url ~ "^/server-status" ||
        req.url ~ "^/signin" ||
        req.url ~ "^/signup" ||
        req.url ~ "^/stats" ||
        req.url ~ "^/wc-api" ||
        req.url ~ "^/wp-admin" ||
        req.url ~ "^/wp-comments-post.php" ||
        req.url ~ "^/wp-cron.php" ||
        req.url ~ "^/wp-login.php" ||
        req.url ~ "^/wp-activate.php" ||
        req.url ~ "^/wp-mail.php" ||
        req.url ~ "^/wp-login.php" ||
        req.url ~ "^\?add-to-cart=" ||
        req.url ~ "^\?wc-api=" ||
        req.url ~ "^/preview=" ||
        req.url ~ "^/\.well-known/acme-challenge/" ||
        req.url ~ "^/events/" ||
        req.url ~ "^/calendar/" ||
        req.url ~ "^/listings/" ||
        req.url ~ "^/shop/" ||
        req.url ~ "^/shopping-cart/" ||
        req.url ~ "^/map/explore/"
    ) {
	     set req.http.X-Cacheable = "NO";
	     if(req.http.X-Requested-With == "XMLHttpRequest") {
		     set req.http.X-Cacheable = "NO:Ajax";
	     }
        return(pass);
    }

    # Special handling for script-loader.php (optimization for WordPress)
    if (req.url ~ ".*script-loader\.php.*" ||
        req.url ~ "^/script-loader.php" || 
        req.url ~ ".*script-loader\.php" || 
        req.url ~ "script-loader\.php") {
        set req.http.X-Static-File = "true";
        set req.http.Cache-Control = "max-age=32940800";
        unset req.http.Cookie;
        return(hash); //hash >> cache
        //return(pass); //pass >> no cache
    }

    # The Platform.sh router provides the real client IP as X-Client-IP
    # This replaces client.identity in other implementations
    if (vsthrottle.is_denied(req.http.X-Client-IP, 20, 5s, 120s)) {
        # Client has exceeded 20 requests in 10 seconds.
        # When this happens, block that IP for the next 120 seconds.
        return (synth(429, "Too Many Requests"));
    }
    
    # Set the standard backend for handling requests that aren't limited
    set req.backend_hint = application.backend();
    
    # Block bad User-Agents
    if (req.http.User-Agent ~ "bytespider" ||
         req.http.User-Agent ~ "bytedance"
    ) {
        return (synth(403, "Forbidden - Blocked User Agent"));
    } 
    # Continue with normal processing for other requests

    # Final cookie cleanup
    unset req.http.Cookie;
    return(hash);

}

sub vcl_backend_response {
    # Set default grace period for backend responses
     set beresp.grace = 24h;
     set beresp.keep = 8m;

    # Store request URL and host in the response for future reference
    set beresp.http.x-url = bereq.url;
    set beresp.http.x-host = bereq.http.host;

    # Set Vary header to separate cache based on User-Agent
    set beresp.http.Vary = "User-Agent";

    # Do not cache specific server errors
    if (beresp.status == 500 || beresp.status == 502 || beresp.status == 503) {
        set beresp.ttl = 0s;
        return(abandon);
    }

    # Special handling for streaming MP4 files
    if (bereq.url ~ "^[^?]*\.mp4(\?.*)?$") {
        # Remove any Set-Cookie headers to ensure proper caching
        unset beresp.http.Set-Cookie;

        # Set headers to indicate that the response is streaming
        set beresp.http.X-Streaming = "Y-Stream";
        set beresp.do_stream = true;
    }

    # Default cache control setup for backend responses
    if (!beresp.http.Cache-Control) {
        set beresp.ttl = 1d;
        set beresp.http.X-Cacheable = "Y-1d";
    }

    # Additional cache control for static files
    if (bereq.http.X-Static-File == "true") {
        unset beresp.http.Set-Cookie;
        set beresp.http.X-Cacheable = "Y-52w";
        set beresp.ttl = 52w;
    }

    # Specific cookie handling for Wordfence plugin
    if (beresp.http.Set-Cookie ~ "wfvt_|wordfence_verifiedHuman") {
	    unset beresp.http.Set-Cookie;
	}

    # Avoid caching AJAX responses and handle Set-Cookie header
    if (bereq.url ~ "^/admin-ajax.php" || bereq.http.X-Requested-With == "XMLHttpRequest") {
        set beresp.http.X-Cacheable = "N-Ajax";
    }
	
    if (beresp.http.Set-Cookie) {
        set beresp.http.X-Cacheable = "NO:Got Cookies";
    } elseif(beresp.http.Cache-Control ~ "private") {
        set beresp.http.X-Cacheable = "NO:Cache-Control=private";
    }	

}

sub vcl_deliver {

    # Remove Content-Encoding header if it's "identity" (uncompressed).
    if (resp.http.Content-Encoding == "identity") {
        unset resp.http.Content-Encoding;
    }

    # Debug header setup to understand cache behavior
    if(req.http.X-Cacheable) {
        set resp.http.X-Cacheable = req.http.X-Cacheable;    
    }

    # Additional response handling for streaming MP4 files
    if (resp.http.X-Streaming == "Y-Stream") {
        # Optionally, you can set additional headers for streaming MP4
        # For example, you can set the Content-Type header to "video/mp4"
        set resp.http.Content-Type = "video/mp4";
    }

    # Cleanup headers before delivering the response to the client
    unset resp.http.x-url;
    unset resp.http.x-host;
    
}

sub vcl_synth {
    if (resp.status == 403) {
        set resp.http.Content-Type = "text/plain";
        set resp.http.Content-Length = "22";
        synthetic("Forbidden - Blocked User Agent");
        return (deliver);
    }
}
