// Service Worker Version - Change to force updates
var version = 3;

// URLs to exclude from caching
var excludeCachePatterns = [
    /https:\/\/px\.adentifi\.com\/Pixels/
];

// Install event - No static caching
self.addEventListener('install', function(event) {
    self.skipWaiting(); // Immediately activate new worker
});

// Fetch event - Handles requests
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

    // Network-first for HTML pages to ensure fresh content
    if (event.request.headers.get('Accept')?.includes('text/html')) {
        event.respondWith(
            fetch(event.request)  // Try fetching from network first
            .then(response => {
                return caches.open('mmmadre-dynamic-v' + version).then(cache => {
                    cache.put(event.request, response.clone()); // Update cache
                    return response;
                });
            })
            .catch(() => caches.match(event.request)) // If offline, return cache
        );
        return;
    }

    // Cache-first for assets (CSS, JS, images) to improve performance
    event.respondWith(
        caches.open('mmmadre-dynamic-v' + version).then(cache => {
            return cache.match(event.request).then(response => {
                if (response) return response; // Serve from cache if available
                
                return fetch(event.request).then(networkResponse => {
                    if (networkResponse.status === 200) {
                        cache.put(event.request, networkResponse.clone()); // Store in cache
                    }
                    return networkResponse;
                }).catch(() => {
                    console.error('Service worker fetch error:', event.request.url);
                });
            });
        })
    );
});

// Activate event - Cleanup old caches
self.addEventListener('activate', function(event) {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.filter(cacheName => 
                    cacheName.startsWith('mmmadre-') && cacheName !== 'mmmadre-dynamic-v' + version
                ).map(cacheName => {
                    console.log('Service worker cleared outdated cache:', cacheName);
                    return caches.delete(cacheName);
                })
            );
        }).then(() => self.clients.claim()) // Ensure new worker takes control
    );
});