/*
 * Service worker: the site keeps working on a weak or lost connection.
 *
 * - Public pages (birds, guide, community, …) are fetched from the network first and saved;
 *   when the network fails, or takes longer than 4s, the saved copy is shown instead.
 * - Built assets and local images (icons included) are served from the cache once fetched
 *   (built files carry a content hash, so a cached copy is never stale).
 * - Private pages (account, orders, sign-in, panels) are never saved, and neither is any page
 *   the server rendered for a signed-in visitor or with a flash message (X-Personalized).
 * - With nothing saved, /offline is shown.
 *
 * Bump VERSION to drop every saved copy.
 */
const VERSION = 'v2';
const PAGES = `pages-${VERSION}`;
const ASSETS = `assets-${VERSION}`;
const OFFLINE_URL = '/offline';
const PUBLIC_PAGES = [/^\/$/, /^\/birds(\/|$)/, /^\/guide(\/|$)/, /^\/community(\/posts\/|\/?$)/, /^\/sellers\/\d+$/, /^\/(about|policy|start-selling|favorites)$/];
const ASSET_PATHS = [/^\/build\//, /^\/images\//];
const LIMITS = { [PAGES]: 40, [ASSETS]: 120 };
const SLOW_NETWORK_MS = 4000;

self.addEventListener('install', (event) => {
    event.waitUntil((async () => {
        // The offline page and the styles and fonts it links to.
        const response = await fetch(OFFLINE_URL, { cache: 'reload' });
        const html = await response.clone().text();
        await (await caches.open(PAGES)).put(OFFLINE_URL, response);

        const assets = [...html.matchAll(/(?:href|src)="([^"]+\/build\/[^"]+)"/g)].map((match) => new URL(match[1], self.location).pathname);
        await (await caches.open(ASSETS)).addAll([...new Set(assets)]);

        await self.skipWaiting();
    })());
});

self.addEventListener('activate', (event) => {
    event.waitUntil((async () => {
        const keep = [PAGES, ASSETS];
        await Promise.all((await caches.keys()).filter((key) => ! keep.includes(key)).map((key) => caches.delete(key)));
        await self.clients.claim();
    })());
});

self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    if (request.method !== 'GET' || url.origin !== self.location.origin) {
        return;
    }

    if (request.mode === 'navigate') {
        event.respondWith(page(event, url));
    } else if (ASSET_PATHS.some((pattern) => pattern.test(url.pathname))) {
        event.respondWith(asset(event));
    }
});

async function page(event, url) {
    const { request } = event;
    const storable = PUBLIC_PAGES.some((pattern) => pattern.test(url.pathname));
    const saved = storable ? await caches.match(request, { cacheName: PAGES }) : undefined;

    const network = fetch(request).then((response) => {
        if (storable && response.ok && ! response.headers.has('X-Personalized')) {
            event.waitUntil(store(PAGES, request, response.clone()));
        }

        return response;
    });

    try {
        if (! saved) {
            return await network;
        }

        // A saved copy exists: use it if the network is slow; the fresh page still gets saved.
        event.waitUntil(network.catch(() => {}));

        return await Promise.race([network, new Promise((resolve) => setTimeout(() => resolve(saved), SLOW_NETWORK_MS))]);
    } catch {
        return saved ?? (await caches.match(OFFLINE_URL, { cacheName: PAGES })) ?? Response.error();
    }
}

async function asset(event) {
    const { request } = event;
    const cached = await caches.match(request, { cacheName: ASSETS });

    if (cached) {
        return cached;
    }

    const response = await fetch(request);

    if (response.ok) {
        event.waitUntil(store(ASSETS, request, response.clone()));
    }

    return response;
}

async function store(name, request, response) {
    const cache = await caches.open(name);
    await cache.put(request, response);

    // Oldest entries go first once the cache is over its limit.
    const keys = await cache.keys();
    await Promise.all(keys.slice(0, Math.max(0, keys.length - LIMITS[name])).map((key) => cache.delete(key)));
}
