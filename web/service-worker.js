// This is the current worker version. Bump if you
// need to update your cache.
var version = 4;

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
	/^chrome-extension:\/\//,
	/\/wp-admin/,
	/\/wp-json/,
	/\/api\//,
	/\.php$/,
	/\?.*=.*/, // URLs with query parameters (dynamic content)
];

// Static asset patterns that CAN be cached
var cacheablePatterns = [
	/\.(jpg|jpeg|png|gif|ico|svg|webp)$/,
	/\.(css|js|woff|woff2|ttf|eot|otf)$/,
	/\.(mp4|webm|ogg|wav|mp3)$/,
];

// Check if URL should be cached
function shouldCache(url) {
	// Don't cache if it matches exclude patterns
	if (excludeCachePatterns.some((pattern) => pattern.test(url))) {
		return false;
	}

	// Only cache static assets
	return cacheablePatterns.some((pattern) => pattern.test(url));
}

// Cache vital files on install:
self.addEventListener("install", function (event) {
	event.waitUntil(
		caches
			.open("mmmadre-core-v" + version)
			.then(function (cache) {
				return cache.addAll(toCache);
			})
			.catch(function (error) {
				console.error("Service worker error: " + error);
			})
	);
});

// Intercept requests and respond from cache if possible:
self.addEventListener("fetch", function (event) {
	var requestUrl = event.request.url;

	// Ignore non-GET requests:
	if (event.request.method !== "GET") {
		return;
	}

	// Check if this URL should be cached
	if (!shouldCache(requestUrl)) {
		// Don't cache this request, just fetch it
		return;
	}

	// Open cache for static assets only:
	event.respondWith(
		caches.open("mmmadre-dynamic-v" + version).then(function (cache) {
			return cache.match(event.request).then(function (response) {
				// Return from cache if available:
				if (response) return response;

				return fetch(event.request)
					.then(function (networkResponse) {
						if (networkResponse.status === 200) {
							cache.put(event.request, networkResponse.clone());
						}
						return networkResponse;
					})
					.catch(function (error) {
						console.error("Service worker error: " + error);
					});
			});
		})
	);
});

// Clean out old versions:
self.addEventListener("activate", function (event) {
	event.waitUntil(
		caches
			.keys()
			.then(function (cacheNames) {
				return Promise.all(
					cacheNames
						.filter(function (cacheName) {
							return (
								cacheName.startsWith("mmmadre-") &&
								cacheName !== "mmmadre-dynamic-v" + version &&
								cacheName !== "mmmadre-core-v" + version
							);
						})
						.map(function (cacheName) {
							console.log(
								"Service worker has cleared outdated caches: " + cacheName
							);
							return caches.delete(cacheName);
						})
				);
			})
			.catch(function (error) {
				console.error("Service worker error: " + error);
			})
	);
});
