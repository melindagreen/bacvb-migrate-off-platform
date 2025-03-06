import std;
import vsthrottle;

sub vcl_hit {
    if (req.method == "PURGE") {
        return (synth(200, "OK"));
    }
    if (std.healthy(req.backend_hint)) {
        if (obj.ttl + 300s > 0s) {
            set req.http.grace = "normal (healthy server)";
            return (deliver);
        } else {
            return (restart);
        }
    } else {
        set req.http.grace = "unlimited (unhealthy server)";
        return (deliver);
    }
}

sub vcl_miss {
    if (req.method == "PURGE") {
        return (synth(404, "Not cached"));
    }
}

sub vcl_recv {
    # Setup backend and standard request handling
    set req.backend_hint = application.backend();

    # Ignore search
    if (req.url ~ "\?s=") {
        return (pass);
    }
    if (req.restarts == 0) {
        if (req.http.X-Forwarded-For) {
            set req.http.X-Forwarded-For = req.http.X-Real-IP;
        }
    }
    if (req.http.X-Application ~ "(?i)varnishpass") {
        return (pipe);
    }

    set req.http.Host = regsub(req.http.Host, ":[0-9]+", "");

    if (req.http.Authorization || req.method == "POST") {
        return (pipe);
    }

    # Block bad User-Agents
    if (req.http.User-Agent ~ "bytespider" || req.http.User-Agent ~ "bytedance") {
        return (synth(403, "Forbidden - Blocked User Agent"));
    }

    if (req.url ~ "/feed" && req.method != "URLPURGE") {
        return (pipe);
    }

    if (req.url ~ "/mu-.*") {
        return (pipe);
    }

    if (req.url ~ "/(wp-login|wp-admin|wp-json|wp-cron|membership-account|membership-checkout)" && req.method != "URLPURGE") {
        return (pipe);
    }

    if (req.url ~ "/(cart|my-account|checkout|wc-api|addons|\\?add-to-cart=|add-to-cart|logout|lost-password|administrator|\\?wc-ajax=get_refreshed_fragments)") {
        return (pipe);
    }

    if (req.http.Cookie ~ "wordpress_logged_in|resetpass|wp-postpass|wordpress_|comment_") {
        return (pipe);
    }

    if (req.http.cookie ~ "woocommerce_(cart|session)|wp_woocommerce_session") {
        return (pipe);
    }

    if (req.url ~ "/wp-(login|admin|comments-post.php|cron)" || req.url ~ "preview=true" || req.url ~ "xmlrpc.php") {
        return (pipe);
    }

    if (req.url ~ "edd_action") {
        return (pipe);
    }

    if (req.http.cookie ~ "(^|;\\s*)edd") {
        return (pipe);
    }

    if (!req.url ~ "/wp-(login|admin|cron)|logout|lost-password|wc-api|cart|my-account|checkout|addons|administrator|accounts|bookings|members|member|course|resetpass") {
        unset req.http.cookie;
    }

    if (req.http.Accept-Encoding) {
        if (req.url ~ "\\.(jpg|png|gif|gz|tgz|bz2|tbz|mp3|ogg)$") {
            unset req.http.Accept-Encoding;
        } elseif (req.http.Accept-Encoding ~ "gzip") {
            set req.http.Accept-Encoding = "gzip";
        } elseif (req.http.Accept-Encoding ~ "deflate") {
            set req.http.Accept-Encoding = "deflate";
        } else {
            unset req.http.Accept-Encoding;
        }
    }

    if (!req.http.Cookie) {
        unset req.http.Cookie;
    }

    if (req.http.Authorization || req.http.Cookie) {
        return (pipe);
    }

    if (req.method == "URLPURGE") {
        return (purge);
    }

    if (req.method == "PURGE") {
        ban("req.http.host ~ " + req.http.host);
        return (purge);
    }

    if (req.method == "BAN") {
        ban("req.http.host == " + req.http.host + "&& req.url == " + req.url);
        return (synth(200, "Ban added"));
    }

    if (req.http.Accept-Encoding) {
        if (req.url ~ "\\.(gif|jpg|jpeg|swf|flv|mp3|mp4|pdf|ico|png|gz|tgz|bz2)(\\?.*|)$") {
            unset req.http.Accept-Encoding;
        } elseif (req.http.Accept-Encoding ~ "gzip") {
            set req.http.Accept-Encoding = "gzip";
        } elseif (req.http.Accept-Encoding ~ "deflate") {
            set req.http.Accept-Encoding = "deflate";
        } else {
            unset req.http.Accept-Encoding;
        }
    }

    if (req.url ~ "\\.(gif|jpg|jpeg|swf|css|js|flv|mp3|mp4|pdf|ico|png)(\\?.*|)$") {
        unset req.http.Cookie;
        set req.url = regsub(req.url, "\\?.*$", "");
    }

    if (req.http.Cookie) {
        if (req.http.Cookie ~ "(wordpress_|wp-settings-)") {
            return (pipe);
        } else {
            unset req.http.Cookie;
        }
    }
}

sub vcl_backend_response {
    if (beresp.status == 500 || beresp.status == 502 || beresp.status == 503 || beresp.status == 504 || beresp.status == 400 || beresp.status == 404 || beresp.status == 403) {
        set beresp.uncacheable = true;
    }

    if (bereq.url ~ "wp-(login|admin)" || bereq.url ~ "preview=true" || bereq.url ~ "xmlrpc.php") {
        set beresp.uncacheable = true;
        return (deliver);
    }

    if (beresp.http.set-cookie ~ "(wordpress_|wp-settings-)") {
        set beresp.uncacheable = true;
        return (deliver);
    }

    if (!(bereq.url ~ "(wp-(login|admin)|login)") || bereq.method == "GET") {
        unset beresp.http.set-cookie;
        set beresp.ttl = 4h;
    }

    if (bereq.url ~ "\\.(gif|jpg|jpeg|swf|css|js|flv|mp3|mp4|pdf|ico|png)(\\?.*|)$") {
        set beresp.ttl = 1d;
    }

    if (beresp.http.set-cookie ~ "wp-resetpass-") {
        set beresp.uncacheable = true;
        return (deliver);
    }
}

sub vcl_deliver {
    unset resp.http.Via;
    unset resp.http.X-Powered-By;

    if (obj.hits > 0) {
        set resp.http.X-Cache = "HIT";
    } else {
        set resp.http.X-Cache = "MISS";
    }
}

sub vcl_hash {
    if (req.http.X-Forwarded-Proto) {
        hash_data(req.http.X-Forwarded-Proto);
    }
    if (req.http.X-Forwarded-Country) {
        hash_data(req.http.X-Forwarded-Country);
    }
    if (req.http.X-Forwarded-Continent) {
        hash_data(req.http.X-Forwarded-Continent);
    }
}

sub vcl_synth {
    set resp.http.Content-Type = "text/html; charset=utf-8";
    set resp.http.Retry-After = "5";
    return (deliver);
}
