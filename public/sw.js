/**
 * Service Worker — Mini ERP Keylock Indonesia
 * Cache Version: keylock-static-v2
 *
 * Security Policy:
 * 1. STRICT NETWORK-ONLY for all dynamic, authenticated, and sensitive routes (/erp/*, /mobile, /api/*, /login, /logout).
 * 2. NO CACHING of business data, financial records, customer info, CRM leads, or dynamic HTML.
 * 3. NO CACHING of non-GET requests (POST, PUT, PATCH, DELETE).
 * 4. Cache-First ONLY for explicitly verified static assets (branding, icons, offline fallback).
 * 5. Informative static offline fallback (/offline.html) with NO background sync or offline mutations.
 * 6. Scoped cache cleanup restricted strictly to 'keylock-*' caches.
 */

const CACHE_NAME = 'keylock-static-v2';

const STATIC_ASSETS = [
    '/offline.html',
    '/manifest.webmanifest',
    '/images/keylock-logo.svg',
    '/images/icons/icon-192x192.png',
    '/images/icons/icon-512x512.png',
    '/images/icons/icon-maskable-512x512.png',
    '/favicon.ico',
];

// Install: Pre-cache strictly static assets
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(STATIC_ASSETS).catch((err) => {
                console.warn('[PWA SW] Pre-caching static assets warning:', err);
            });
        })
    );
    // Note: skipWaiting() is NOT called automatically to prevent disrupting active user sessions
});

// Activate: Clean up only previous keylock-* caches, leaving any other origin caches intact
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.map((key) => {
                    if (key.startsWith('keylock-') && key !== CACHE_NAME) {
                        return caches.delete(key);
                    }
                })
            );
        })
    );
});

// Safe update trigger via message
self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});

// Fetch: Strict Network-Only for dynamic/authenticated, Cache-First for static
self.addEventListener('fetch', (event) => {
    // 1. Only process GET requests; never touch mutations
    if (event.request.method !== 'GET') {
        return;
    }

    const url = new URL(event.request.url);
    const isSameOrigin = url.origin === self.location.origin;

    // 2. Identify dynamic, authenticated, and sensitive routes
    const isDynamicRoute = isSameOrigin && (
        url.pathname === '/erp' ||
        url.pathname.startsWith('/erp/') ||
        url.pathname === '/mobile' ||
        url.pathname.startsWith('/mobile/') ||
        url.pathname.startsWith('/api/') ||
        url.pathname === '/login' ||
        url.pathname === '/logout'
    );

    // 3. Dynamic routes MUST BE STRICTLY NETWORK-ONLY
    if (isDynamicRoute) {
        event.respondWith(
            fetch(event.request).catch((err) => {
                // If offline and navigating to a page, serve static offline notification
                const isNav = event.request.mode === 'navigate' ||
                    (event.request.headers.get('accept') && event.request.headers.get('accept').includes('text/html'));
                
                if (isNav) {
                    return caches.match('/offline.html');
                }

                // For AJAX/JSON or PDF downloads, fail gracefully without exposing stale or cached data
                throw err;
            })
        );
        return;
    }

    // 4. Check for explicitly allowed static assets
    const isStaticAsset = (
        STATIC_ASSETS.includes(url.pathname) ||
        (isSameOrigin && /\.(?:png|jpg|jpeg|svg|webp|ico|woff2|woff|ttf|css|js)$/i.test(url.pathname)) ||
        url.hostname === 'fonts.googleapis.com' ||
        url.hostname === 'fonts.gstatic.com'
    );

    // 5. Static assets: Cache-First strategy
    if (isStaticAsset) {
        event.respondWith(
            caches.match(event.request).then((cachedResponse) => {
                if (cachedResponse) {
                    return cachedResponse;
                }
                return fetch(event.request).then((networkResponse) => {
                    // Only cache valid basic or CORS responses; do not cache errors or partials
                    if (networkResponse && networkResponse.status === 200 && (networkResponse.type === 'basic' || networkResponse.type === 'cors')) {
                        const responseToCache = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(event.request, responseToCache);
                        });
                    }
                    return networkResponse;
                });
            })
        );
        return;
    }

    // 6. Default fallback for any other requests: Network with offline fallback for navigation
    event.respondWith(
        fetch(event.request).catch((err) => {
            const isNav = event.request.mode === 'navigate' ||
                (event.request.headers.get('accept') && event.request.headers.get('accept').includes('text/html'));
            
            if (isNav) {
                return caches.match('/offline.html');
            }
            throw err;
        })
    );
});
