var staticCacheName = "pwa-v" + new Date().getTime();
var filesToCache = [
    '/offline',
    '/images/icons/icon-72x72.png',
    '/images/icons/icon-96x96.png',
    '/images/icons/icon-128x128.png',
    '/images/icons/icon-144x144.png',
    '/images/icons/icon-152x152.png',
    '/images/icons/icon-192x192.png',
    '/images/icons/icon-384x384.png',
    '/images/icons/icon-512x512.png',
];

// Cache on install
self.addEventListener("install", event => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(staticCacheName)
            .then(cache => {
                // Cache each file individually so one failure doesn't break all
                return Promise.allSettled(
                    filesToCache.map(url =>
                        cache.add(url).catch(() => {
                            // Silently skip files that fail to cache
                        })
                    )
                );
            })
    );
});

// Clear cache on activate
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames
                    .filter(cacheName => (cacheName.startsWith("pwa-")))
                    .filter(cacheName => (cacheName !== staticCacheName))
                    .map(cacheName => caches.delete(cacheName))
            );
        })
    );
});

// NETWORK-FIRST strategy for HTML/JS/CSS, cache-only for icons/offline
// This ensures code updates are always loaded from the server.
self.addEventListener("fetch", event => {
    // Never intercept non-GET requests (POST, PUT, DELETE, etc.)
    if (event.request.method !== 'GET') {
        return;
    }

    const url = new URL(event.request.url);

    // For navigation requests (HTML pages) and JS/CSS assets: NETWORK FIRST
    // This ensures fresh code is always loaded after deployments.
    if (event.request.mode === 'navigate' ||
        url.pathname.startsWith('/build/') ||
        url.pathname.endsWith('.js') ||
        url.pathname.endsWith('.css')) {
        event.respondWith(
            fetch(event.request)
                .then(response => {
                    // Cache the fresh response for offline use
                    const clone = response.clone();
                    caches.open(staticCacheName).then(cache => cache.put(event.request, clone));
                    return response;
                })
                .catch(() => {
                    // Network failed — try cache fallback
                    return caches.match(event.request)
                        .then(cached => cached || caches.match('offline') || new Response('Offline', {
                            status: 503,
                            headers: { 'Content-Type': 'text/plain' },
                        }));
                })
        );
        return;
    }

    // For static assets (icons, images): CACHE FIRST (they rarely change)
    event.respondWith(
        caches.match(event.request)
            .then(response => {
                return response || fetch(event.request);
            })
            .catch(() => {
                return caches.match('offline') || new Response('Offline', {
                    status: 503,
                    headers: { 'Content-Type': 'text/plain' },
                });
            })
    );
});