// Headless Chrome over the DevTools protocol, using Node's built-in WebSocket (Node 22+).
// No dependencies: the only requirement is a Chrome or Chromium on the machine.
import { spawn } from 'node:child_process';
import { existsSync, mkdtempSync, rmSync } from 'node:fs';
import { tmpdir } from 'node:os';
import { join } from 'node:path';

export const sleep = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

const CHROME_PATHS = [
    process.env.CHROME_PATH,
    'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
    'C:\\Program Files (x86)\\Google\\Chrome\\Application\\chrome.exe',
    '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
    '/usr/bin/google-chrome',
    '/usr/bin/chromium',
    '/usr/bin/chromium-browser',
];

class Client {
    constructor(url) {
        this.socket = new WebSocket(url);
        this.nextId = 0;
        this.pending = new Map();
        this.listeners = new Set();
    }

    open() {
        return new Promise((resolve, reject) => {
            this.socket.onopen = resolve;
            this.socket.onerror = reject;
            this.socket.onmessage = ({ data }) => {
                const message = JSON.parse(data);

                if (message.id && this.pending.has(message.id)) {
                    const { resolve: done, reject: fail } = this.pending.get(message.id);
                    this.pending.delete(message.id);
                    message.error ? fail(new Error(message.error.message)) : done(message.result);
                } else {
                    this.listeners.forEach((listener) => listener(message));
                }
            };
        });
    }

    send(method, params = {}, sessionId) {
        const id = ++this.nextId;
        this.socket.send(JSON.stringify({ id, method, params, sessionId }));

        return new Promise((resolve, reject) => this.pending.set(id, { resolve, reject }));
    }

    once(method, sessionId, timeout = 30000) {
        return new Promise((resolve) => {
            const listener = (message) => {
                if (message.method === method && message.sessionId === sessionId) {
                    finish(message.params);
                }
            };
            const finish = (value) => {
                clearTimeout(timer);
                this.listeners.delete(listener);
                resolve(value);
            };
            const timer = setTimeout(() => finish(null), timeout);
            this.listeners.add(listener);
        });
    }
}

export async function launchChrome() {
    const binary = CHROME_PATHS.find((path) => path && existsSync(path));

    if (! binary) {
        throw new Error('Chrome was not found. Set CHROME_PATH to its executable.');
    }

    const profile = mkdtempSync(join(tmpdir(), 'ui-check-'));
    const port = 9400 + Math.floor(Math.random() * 500);
    const chrome = spawn(binary, [
        '--headless=new', `--remote-debugging-port=${port}`, `--user-data-dir=${profile}`,
        '--no-first-run', '--no-default-browser-check', '--hide-scrollbars', '--force-color-profile=srgb',
        // Extensions would add their own scripts to every page and skew the measurements.
        '--disable-extensions', '--disable-component-extensions-with-background-pages',
        'about:blank',
    ], { stdio: 'ignore' });

    let url;

    for (let attempt = 0; attempt < 80 && ! url; attempt++) {
        try {
            url = (await (await fetch(`http://127.0.0.1:${port}/json/version`)).json()).webSocketDebuggerUrl;
        } catch {
            await sleep(250);
        }
    }

    if (! url) {
        chrome.kill();
        throw new Error('Chrome did not start.');
    }

    const client = new Client(url);
    await client.open();

    return {
        client,
        async close() {
            const exited = new Promise((resolve) => chrome.once('exit', resolve));
            chrome.kill();
            await Promise.race([exited, sleep(3000)]);
            rmSync(profile, { recursive: true, force: true, maxRetries: 5, retryDelay: 200 });
        },
    };
}

/**
 * A tab in its own browser context (separate cookies and storage), at a fixed size, with
 * reduced motion so screenshots never catch an element halfway through an animation.
 */
export async function openPage(client, { baseUrl, width, height, mobile = false, dark = false }) {
    const { browserContextId } = await client.send('Target.createBrowserContext');
    const { targetId } = await client.send('Target.createTarget', { url: 'about:blank', browserContextId });
    const { sessionId } = await client.send('Target.attachToTarget', { targetId, flatten: true });
    const send = (method, params) => client.send(method, params, sessionId);

    for (const domain of ['Page', 'Runtime', 'Network']) {
        await send(`${domain}.enable`);
    }

    await send('Network.setBypassServiceWorker', { bypass: true });
    await send('Emulation.setDeviceMetricsOverride', { width, height, deviceScaleFactor: 1, mobile });
    await send('Emulation.setTouchEmulationEnabled', { enabled: mobile });
    await send('Emulation.setEmulatedMedia', { features: [
        { name: 'prefers-color-scheme', value: dark ? 'dark' : 'light' },
        { name: 'prefers-reduced-motion', value: 'reduce' },
    ] });

    let problems = [];

    const listener = (message) => {
        if (message.sessionId !== sessionId) {
            return;
        }

        const { params } = message;

        if (message.method === 'Runtime.exceptionThrown') {
            problems.push(`JavaScript error: ${String(params.exceptionDetails.exception?.description ?? params.exceptionDetails.text).split('\n')[0]}`);
        } else if (message.method === 'Network.responseReceived' && params.response.status >= 400 && params.type !== 'Document') {
            problems.push(`HTTP ${params.response.status} for ${params.response.url}`);
        } else if (message.method === 'Network.loadingFailed' && ! params.canceled && params.type !== 'Ping') {
            problems.push(`Failed to load (${params.errorText}) ${params.type}`);
        }
    };
    client.listeners.add(listener);

    const page = {
        send,
        width,
        height,

        async evaluate(expression) {
            const { result, exceptionDetails } = await send('Runtime.evaluate', { expression, awaitPromise: true, returnByValue: true });

            if (exceptionDetails) {
                throw new Error(exceptionDetails.exception?.description ?? exceptionDetails.text);
            }

            return result.value;
        },

        // Loads a path and waits until the page has settled. Returns the document's HTTP status.
        async goto(path, { settle = 900 } = {}) {
            problems = [];
            const response = client.once('Network.responseReceived', sessionId);
            const loaded = client.once('Page.loadEventFired', sessionId);
            await send('Page.navigate', { url: path.startsWith('http') ? path : baseUrl + path });
            const document = await response;
            await loaded;
            await page.evaluate('document.fonts.ready.then(() => true)');
            await sleep(settle);

            return document?.response?.status ?? 0;
        },

        // Runs script that navigates (a form submit) and waits for the next page.
        async navigateBy(expression) {
            problems = [];
            const loaded = client.once('Page.loadEventFired', sessionId);
            await page.evaluate(expression);
            await loaded;
            await sleep(500);
        },

        problems: () => [...new Set(problems)],

        // Up to `maxScreens` viewport heights of the page, as JPEG (deterministic for equal pixels).
        // `masked` selectors are hidden but keep their space.
        async screenshot({ maxScreens = 3, masked = [] } = {}) {
            // Lay out off-screen content (content-visibility) so the capture has no placeholders.
            const css = `* { content-visibility: visible !important; } ${masked.length ? masked.join(', ') + ' { visibility: hidden !important; }' : ''}`;
            await page.evaluate(`window.scrollTo(0, 0); document.head.insertAdjacentHTML('beforeend', ${JSON.stringify(`<style data-ui-check>${css}</style>`)}); new Promise((resolve) => requestAnimationFrame(() => requestAnimationFrame(() => resolve(true))))`);
            const { cssContentSize } = await send('Page.getLayoutMetrics');
            const clipHeight = Math.min(Math.ceil(cssContentSize.height), height * maxScreens);
            const { data } = await send('Page.captureScreenshot', {
                format: 'jpeg', quality: 85, captureBeyondViewport: true,
                clip: { x: 0, y: 0, width, height: clipHeight, scale: 1 },
            });

            return Buffer.from(data, 'base64');
        },

        async close() {
            client.listeners.delete(listener);
            await client.send('Target.disposeBrowserContext', { browserContextId });
        },
    };

    return page;
}
