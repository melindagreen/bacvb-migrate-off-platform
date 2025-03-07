// This is the current worker version. Bump if you
// need to update your cache.
var version = 3;

// These files will be added to the static cache
// ONLY places files here that are very unlikely to
// change. A missing file may throw errors and negate
// the performance benefits of a service worker.
var toCache = [
    // Images:
    // JS:
    // CSS:
    // Plugin files:
];

// URLs to exclude from caching
var excludeCachePatterns = [
    /https:\/\/px\.adentifi\.com\/Pixels/,
    /^chrome-extension:\/\//
];


// Intercept requests and respond from cache if possible:
self.addEventListener('fetch', function(event) {
    var requestUrl = event.request.url;
    
    // Ignore non-GET requests:
    if (event.request.method !== 'GET') {
        return;
    }

    // Exclude specific URLs from caching:
    if (excludeCachePatterns.some(pattern => pattern.test(requestUrl))) {
        return;
    }

    // Open cache and apply dynamic caching with expiration control:
    event.respondWith(
        caches.open('mmmadre-dynamic-v' + version).then(function(cache) {
            return cache.match(event.request).then(function(response) {
                if (response) {
                    const cacheTime = response.headers.get('date');
                    const currentTime = new Date().getTime();
                    const cacheAge = (currentTime - new Date(cacheTime).getTime()) / 1000;  // Cache age in seconds

                    // Set your expiration time (e.g., 10 minutes = 600 seconds)
                    if (cacheAge < 600) {
                        return response;  // Return from cache if not expired
                    } else {
                        // Remove expired content
                        cache.delete(event.request);
                    }
                }
                
                return fetch(event.request).then(function(networkResponse) {
                    if (networkResponse.status === 200) {
                        // Cache the response for future use
                        cache.put(event.request, networkResponse.clone());
                    }
                    return networkResponse;
                }).catch(function(error) {
                    console.error('Service worker error: ' + error);
                });
            });
        })
    );
});
