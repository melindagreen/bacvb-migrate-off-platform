// Increment this version to force cache updates
var version = 3;

// Files to cache (static assets only)
var toCache = [
    // Add files that should be cached
];

// URLs to exclude from caching
var excludeCachePatterns = [
    /https:\/\/px\.adentifi\.com\/Pixels/,
    /\/wp-admin/,
    /\/wp-json/
];

// Cache static files on install:
self.addEventListener('install', function(event) {
    event.waitUntil(
        caches.open('mmmadre-core-v' + version).then(function(cache) {
            return cache.addAll(toCache);
        }).catch(function(error) {
            console.error('Service worker install error:', error);
        })
    );
});

// Intercept fetch requests:
self.addEventListener('fetch', function(event) {
    var requestUrl = event.request.url;

    // Ignore non-GET requests:
    if (event.request.method !== 'GET') {
        return;
    }

    // Bypass cache for excluded URLs (admin, REST API, etc.)
    if (excludeCachePatterns.some(pattern => pattern.test(requestUrl))) {
        event.respondWith(fetch(event.request));
        return;
    }

    event.respondWith(
        caches.open('mmmadre-dynamic-v' + version).then(function(cache) {
            return fetch(event.request).then(function(networkResponse) {
                if (networkResponse.status === 200) {
                    cache.put(event.request, networkResponse.clone());
                }
                return networkResponse;
            }).catch(function() {
                return cache.match(event.request) || fetch(event.request);
            });
        })
    );
});

// Clean old caches on activation:
self.addEventListener('activate', function(event) {
    event.waitUntil(
        caches.keys().then(function(cacheNames) {
            return Promise.all(
                cacheNames.filter(function(cacheName) {
                    return cacheName.startsWith('mmmadre-') && !cacheName.includes(version);
                }).map(function(cacheName) {
                    console.log('Deleting old cache:', cacheName);
                    return caches.delete(cacheName);
                })
            );
        })
    );
});
