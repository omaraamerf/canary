#!/usr/bin/env node
/*
 * UI checks against a running copy of the site (Docker: http://localhost:8085).
 *
 *   npm run ui:audit      every page, phone and desktop, light, dark and English: contrast, text and
 *                         control sizes, one h1, labels, names, structure, sideways scroll, JS errors.
 *   npm run ui:baseline   screenshots of every page into storage/ui-check/baseline (run on main).
 *   npm run ui:compare    the same screenshots compared with the baseline (run on your branch);
 *                         storage/ui-check/report.html shows each changed page before, after and diff.
 *   npm run ui:perf       loading speed on a throttled phone (slow 4G, 4x CPU): LCP and CLS against
 *                         the budget, blocking time and page weight for tracking.
 *
 * Each command exits with 1 when it finds something, so it can gate a merge.
 * Settings: UI_CHECK_URL (site address), CHROME_PATH, UI_CHECK_ADMIN_EMAIL, UI_CHECK_PASSWORD,
 * UI_CHECK_RUNS (loads per page for ui:perf, default 3).
 */
import { existsSync, mkdirSync, readdirSync, readFileSync, rmSync, writeFileSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';
import { launchChrome, openPage } from './chrome.mjs';
import { AUDIT } from './audit.mjs';
import { ADMIN_PAGES, GUEST_PAGES, MASKED, THEME_PAGES, VIEWPORTS } from './pages.mjs';

const ROOT = join(dirname(fileURLToPath(import.meta.url)), '..', '..');
const OUT = join(ROOT, 'storage', 'ui-check');
const BASE_URL = (process.env.UI_CHECK_URL ?? 'http://localhost:8085').replace(/\/$/, '');
const ADMIN_EMAIL = process.env.UI_CHECK_ADMIN_EMAIL ?? 'admin@canary.local';
// The development seed password (database/seeders/MarketplaceSeeder.php); never a real one.
const PASSWORD = process.env.UI_CHECK_PASSWORD ?? 'password';

const command = process.argv[2];
const commands = { audit, baseline: () => capture('baseline'), compare, perf };

if (! commands[command]) {
    console.log('Usage: node scripts/ui-check/run.mjs <audit|baseline|compare|perf>');
    process.exit(2);
}

const chrome = await launchChrome();
let failed = false;

try {
    failed = await commands[command]();
} catch (error) {
    console.error(error);
    failed = true;
} finally {
    await chrome.close();
}

process.exit(failed ? 1 : 0);

// Resolves a page entry to a path: fixed, or the first matching link on its list page.
async function resolvePath(page, target) {
    if (typeof target === 'string') {
        return target;
    }

    await page.goto(target.from, { settle: 200 });

    return page.evaluate(`document.querySelector(${JSON.stringify(target.link)})?.getAttribute('href') ?? null`);
}

async function signIn(page) {
    await page.goto('/login', { settle: 200 });
    await page.navigateBy(`(() => { const f = document.querySelector('form[action$="/login"]'); f.email.value = ${JSON.stringify(ADMIN_EMAIL)}; f.password.value = ${JSON.stringify(PASSWORD)}; f.submit(); })()`);
}

/** Every page in every pass; yields [name, page, path] with the page loaded. */
async function* visit({ withThemes = true } = {}) {
    const passes = VIEWPORTS.map((viewport) => ({ ...viewport, label: viewport.name }));

    if (withThemes) {
        passes.push({ ...VIEWPORTS[0], label: 'desktop-dark', dark: true, only: THEME_PAGES });
        passes.push({ ...VIEWPORTS[0], label: 'desktop-english', locale: 'en', only: THEME_PAGES });
    }

    for (const pass of passes) {
        for (const [group, entries] of [['guest', GUEST_PAGES], ['admin', ADMIN_PAGES]]) {
            const list = entries.filter(([name]) => ! pass.only || pass.only.includes(name));

            if (! list.length) {
                continue;
            }

            const page = await openPage(chrome.client, { baseUrl: BASE_URL, ...pass });

            if (pass.locale) {
                await page.goto(`/locale/${pass.locale}`, { settle: 100 });
            }

            if (group === 'admin') {
                await signIn(page);
            }

            for (const [name, target, expectedStatus = 200] of list) {
                const path = await resolvePath(page, target);

                if (! path) {
                    console.log(`  skipped ${pass.label}/${name}: nothing to link to yet`);
                    continue;
                }

                const status = await page.goto(path);

                yield { name: `${pass.label}-${name}`, page, path, status, expectedStatus };
            }

            await page.close();
        }
    }
}

async function audit() {
    let problems = 0;

    for await (const { name, page, path, status, expectedStatus } of visit()) {
        const result = await page.evaluate(AUDIT);
        const issues = [...result.issues, ...page.problems()];

        if (status !== expectedStatus) {
            issues.unshift(`HTTP ${status}, expected ${expectedStatus}`);
        }

        problems += issues.length;
        console.log(`${issues.length ? '✗' : '✓'} ${name} ${path}`);
        issues.forEach((issue) => console.log(`    ${issue}`));
    }

    console.log(problems ? `\n${problems} problems found.` : '\nNo problems found.');

    return problems > 0;
}

async function capture(folder) {
    const dir = join(OUT, folder);
    rmSync(dir, { recursive: true, force: true });
    mkdirSync(dir, { recursive: true });
    const names = [];

    for await (const { name, page } of visit()) {
        writeFileSync(join(dir, `${name}.jpg`), await page.screenshot({ masked: MASKED }));
        names.push(name);
        console.log(`  ${folder}: ${name}`);
    }

    console.log(`\n${names.length} screenshots in ${dir}`);

    return false;
}

async function compare() {
    if (! existsSync(join(OUT, 'baseline'))) {
        console.error('No baseline yet: run "npm run ui:baseline" on the main branch first.');

        return true;
    }

    await capture('current');
    const diffDir = join(OUT, 'diff');
    rmSync(diffDir, { recursive: true, force: true });
    mkdirSync(diffDir, { recursive: true });

    // The pixels are compared in the browser itself, on two canvases.
    const page = await openPage(chrome.client, { baseUrl: BASE_URL, ...VIEWPORTS[0] });
    const rows = [];

    for (const file of readdir(join(OUT, 'current'))) {
        const name = file.replace(/\.jpg$/, '');
        const baselineFile = join(OUT, 'baseline', file);

        if (! existsSync(baselineFile)) {
            rows.push({ name, changed: 1, note: 'new page' });
            continue;
        }

        const result = await page.evaluate(`(${diffImages})(${JSON.stringify(dataUrl(baselineFile))}, ${JSON.stringify(dataUrl(join(OUT, 'current', file)))})`);

        if (result.changed > 0.001) {
            writeFileSync(join(diffDir, `${name}.png`), Buffer.from(result.diff.split(',')[1], 'base64'));
        }

        rows.push({ name, changed: result.changed, note: result.sizeChanged ? 'height changed' : '' });
    }

    await page.close();

    const changed = rows.filter((row) => row.changed > 0.001);
    changed.forEach((row) => console.log(`✗ ${row.name}: ${(row.changed * 100).toFixed(2)}% of pixels ${row.note}`));
    writeFileSync(join(OUT, 'report.html'), report(changed));
    console.log(changed.length ? `\n${changed.length} pages changed. See storage/ui-check/report.html` : '\nNo visual changes.');

    return changed.length > 0;
}

// Runs in the browser: share of differing pixels between two screenshots, and a diff image
// (changed pixels in red over a faded copy of the new screenshot).
function diffImages(beforeUrl, afterUrl) {
    const load = (src) => new Promise((resolve) => { const image = new Image(); image.onload = () => resolve(image); image.src = src; });

    return Promise.all([load(beforeUrl), load(afterUrl)]).then(([before, after]) => {
        const width = Math.max(before.width, after.width);
        const height = Math.max(before.height, after.height);
        const pixels = (image) => { const c = document.createElement('canvas'); c.width = width; c.height = height; const x = c.getContext('2d'); x.fillStyle = '#ff00ff'; x.fillRect(0, 0, width, height); x.drawImage(image, 0, 0); return x.getImageData(0, 0, width, height); };
        const a = pixels(before);
        const b = pixels(after);
        const out = new ImageData(width, height);
        let changed = 0;

        for (let i = 0; i < a.data.length; i += 4) {
            // JPEG noise stays below this per-channel tolerance.
            const differs = Math.abs(a.data[i] - b.data[i]) > 40 || Math.abs(a.data[i + 1] - b.data[i + 1]) > 40 || Math.abs(a.data[i + 2] - b.data[i + 2]) > 40;
            changed += differs ? 1 : 0;
            out.data[i] = differs ? 230 : 255 - (255 - b.data[i]) * 0.25;
            out.data[i + 1] = differs ? 30 : 255 - (255 - b.data[i + 1]) * 0.25;
            out.data[i + 2] = differs ? 30 : 255 - (255 - b.data[i + 2]) * 0.25;
            out.data[i + 3] = 255;
        }

        const canvas = document.createElement('canvas');
        canvas.width = width;
        canvas.height = height;
        canvas.getContext('2d').putImageData(out, 0, 0);

        return { changed: changed / (width * height), sizeChanged: before.height !== after.height, diff: canvas.toDataURL('image/png') };
    });
}

async function perf() {
    // LCP and CLS gate (the plan's acceptance criteria); blocking time is shown for tracking
    // only: on a throttled CPU it moves with whatever else the machine is doing.
    const BUDGET = { lcp: 2500, cls: 0.1 };
    const RUNS = Number(process.env.UI_CHECK_RUNS ?? 3);
    const targets = GUEST_PAGES.filter(([name]) => ['home', 'birds', 'bird', 'guide', 'guide-article', 'community'].includes(name));
    const finder = await openPage(chrome.client, { baseUrl: BASE_URL, ...VIEWPORTS[1] });
    const paths = [];

    for (const [name, target] of targets) {
        const path = await resolvePath(finder, target);

        if (path) {
            paths.push([name, path.replace(BASE_URL, '')]);
        }
    }

    await finder.close();
    let over = false;
    console.log(`Median of ${RUNS} cold loads per page on a throttled phone (slow 4G, 4x CPU).
`);
    console.log('page            LCP      CLS     blocking  transfer  requests');

    for (const [name, path] of paths) {
        const runs = [];

        for (let run = 0; run < RUNS; run++) {
            runs.push(await measure(path));
        }

        const median = (key) => runs.map((r) => r[key]).sort((a, b) => a - b)[Math.floor(runs.length / 2)];
        const v = { lcp: median('lcp'), cls: median('cls'), tbt: median('tbt'), bytes: median('bytes'), requests: median('requests') };
        const bad = v.lcp > BUDGET.lcp || v.cls > BUDGET.cls;
        over ||= bad;
        console.log(`${bad ? '✗' : '✓'} ${name.padEnd(14)} ${(v.lcp / 1000).toFixed(2).padStart(5)}s  ${v.cls.toFixed(3).padStart(6)}  ${String(Math.round(v.tbt)).padStart(5)}ms  ${String(Math.round(v.bytes / 1024)).padStart(6)}KB  ${String(v.requests).padStart(5)}`);
    }

    console.log(`
Budget: LCP ≤ ${BUDGET.lcp / 1000}s and CLS ≤ ${BUDGET.cls}.`);

    return over;
}

// One cold load (a fresh context has an empty cache) with the Web Vitals observers in place.
async function measure(path) {
    const page = await openPage(chrome.client, { baseUrl: BASE_URL, ...VIEWPORTS[1] });
    await page.send('Network.emulateNetworkConditions', { offline: false, latency: 150, downloadThroughput: 1.6 * 1024 * 1024 / 8, uploadThroughput: 750 * 1024 / 8 });
    await page.send('Emulation.setCPUThrottlingRate', { rate: 4 });
    await page.send('Page.addScriptToEvaluateOnNewDocument', { source: `
        window.__vitals = { lcp: 0, cls: 0, tbt: 0 };
        new PerformanceObserver((list) => { const last = list.getEntries().at(-1); if (last) __vitals.lcp = last.startTime; }).observe({ type: 'largest-contentful-paint', buffered: true });
        new PerformanceObserver((list) => { for (const e of list.getEntries()) if (! e.hadRecentInput) __vitals.cls += e.value; }).observe({ type: 'layout-shift', buffered: true });
        new PerformanceObserver((list) => { for (const e of list.getEntries()) __vitals.tbt += Math.max(0, e.duration - 50); }).observe({ type: 'longtask', buffered: true });
    ` });
    await page.goto(path, { settle: 3000 });
    const result = await page.evaluate(`(() => { const entries = [...performance.getEntriesByType('navigation'), ...performance.getEntriesByType('resource')]; return { ...window.__vitals, bytes: entries.reduce((sum, e) => sum + (e.transferSize || 0), 0), requests: entries.length }; })()`);
    await page.close();

    return result;
}

function readdir(dir) {
    return existsSync(dir) ? readdirSync(dir).filter((file) => file.endsWith('.jpg')).sort() : [];
}

function dataUrl(file) {
    return `data:image/jpeg;base64,${readFileSync(file).toString('base64')}`;
}

function report(rows) {
    const cards = rows.map(({ name, changed }) => `
        <section><h2>${name} <small>${(changed * 100).toFixed(2)}%</small></h2>
        <div class="row"><figure><figcaption>baseline</figcaption><img src="baseline/${name}.jpg"></figure>
        <figure><figcaption>current</figcaption><img src="current/${name}.jpg"></figure>
        <figure><figcaption>diff</figcaption><img src="diff/${name}.png"></figure></div></section>`).join('');

    return `<!doctype html><meta charset="utf-8"><title>ui-check</title>
        <style>body{font:14px system-ui;margin:24px;background:#f6f7f2}section{margin-bottom:32px}.row{display:grid;gap:12px;grid-template-columns:repeat(3,1fr)}
        figure{margin:0}img{width:100%;border:1px solid #ccc}small{color:#a00}</style>
        <h1>Visual changes: ${rows.length}</h1>${cards || '<p>No changes.</p>'}`;
}
