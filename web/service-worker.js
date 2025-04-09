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


// Cache vital files on install:
self.addEventListener('install', function(event) {

    event.waitUntil(caches.open('mmmadre-core-v' + version)
    .then(function(cache) {
        return cache.addAll(toCache);
    })
    .catch(function(error) {
        console.error('Service worker error: ' + error);
    }));
});

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

    // Open cache:
    event.respondWith(
        caches.open('mmmadre-dynamic-v' + version).then(function(cache) {
            return cache.match(event.request).then(function(response) {
                // Return from cache if available:
                if (response) return response;
                
                return fetch(event.request).then(function(networkResponse) {
                    if (networkResponse.status === 200) {
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

// Clean out old versions:
self.addEventListener('activate', function(event) {
    event.waitUntil(caches.keys()
    .then(function(cacheNames) {
        return Promise.all(
            cacheNames.filter(function(cacheName) {
                return cacheName.startsWith('mmmadre-') && cacheName !== 'mmmadre-dynamic-v' + version;
            }).map(function(cacheName) {
                console.log('Service worker has cleared outdated caches: ' + cacheName);
                return caches.delete(cacheName);
            })
        );
    })
    .catch(function(error) {
        console.error('Service worker error: ' + error);
    }));
});
