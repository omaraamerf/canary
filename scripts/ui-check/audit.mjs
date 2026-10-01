// Runs inside the page and returns what breaks the site's own rules: WCAG contrast, the 13px
// text and 40px control minimums, one h1, names for every control, valid structure, no
// sideways scroll. Filament panels keep their own control sizes, so those two size rules
// apply to the public site only.
export const AUDIT = `(async () => {
    // Off-screen content is laid out lazily (content-visibility): show it all, and wait two frames,
    // since skipped content only gets its styles when the browser next renders.
    const showAll = document.createElement('style');
    showAll.textContent = '* { content-visibility: visible !important; }';
    document.head.append(showAll);
    await new Promise((resolve) => requestAnimationFrame(() => requestAnimationFrame(resolve)));
    const issues = [];
    const add = (rule, detail) => issues.push(rule + (detail ? ': ' + detail : ''));
    const visible = (el) => { const r = el.getBoundingClientRect(); const cs = getComputedStyle(el); return r.width > 0 && r.height > 0 && cs.visibility !== 'hidden' && cs.display !== 'none' && ! el.closest('dialog:not([open]), [popover]:not(:popover-open), [hidden]'); };
    const label = (el) => (el.className && typeof el.className === 'string' ? el.className.split(' ')[0] : el.tagName.toLowerCase()) + ' «' + (el.textContent || '').trim().slice(0, 24) + '»';
    const isPanel = document.body.classList.contains('fi-body');

    const root = document.documentElement;
    if (root.scrollWidth > root.clientWidth + 1) add('Sideways scroll', root.scrollWidth + 'px wide in a ' + root.clientWidth + 'px viewport');
    if (! root.lang || ! root.dir) add('Missing lang or dir on <html>');
    const h1s = document.querySelectorAll('h1');
    if (h1s.length !== 1) add('Expected one h1', h1s.length + ' found');

    // Headings may go down one level at a time (h2 then h3, not h2 then h4).
    let previous = 0;
    [...document.querySelectorAll('h1, h2, h3, h4, h5, h6')].filter(visible).forEach((h) => {
        const level = Number(h.tagName[1]);
        if (previous && level > previous + 1) add('Heading level skipped', 'h' + previous + ' → ' + label(h));
        previous = level;
    });

    const ids = [...document.querySelectorAll('[id]')].map((el) => el.id).filter(Boolean);
    [...new Set(ids.filter((id, i) => ids.indexOf(id) !== i))].slice(0, 5).forEach((id) => add('Duplicate id', id));

    [...document.images].forEach((img) => {
        if (! img.hasAttribute('alt')) add('Image without alt', img.getAttribute('src')?.slice(-40));
        if (img.complete && img.naturalWidth === 0 && img.getAttribute('src')) add('Broken image', img.getAttribute('src').slice(-40));
    });

    const named = (el) => Boolean(el.labels?.length || el.getAttribute('aria-label') || el.getAttribute('aria-labelledby') || el.getAttribute('title') || el.textContent.trim() || [...el.querySelectorAll('img[alt]')].some((i) => i.alt.trim()));
    [...document.querySelectorAll('a[href], button, [role=button]')].filter(visible).forEach((el) => { if (! named(el)) add('Control without a name', el.outerHTML.slice(0, 80)); });
    [...document.querySelectorAll('input:not([type=hidden]), select, textarea')].filter(visible).forEach((el) => {
        if (! el.labels?.length && ! el.getAttribute('aria-label') && ! el.getAttribute('aria-labelledby') && ! el.getAttribute('title')) add('Field without a label', el.name || el.type);
    });

    const texts = [...document.querySelectorAll('body *')].filter((el) => [...el.childNodes].some((n) => n.nodeType === 3 && n.textContent.trim().length > 1) && visible(el));

    if (! isPanel) {
        texts.filter((el) => parseFloat(getComputedStyle(el).fontSize) < 13).slice(0, 5).forEach((el) => add('Text under 13px', label(el) + ' ' + getComputedStyle(el).fontSize));
        const controls = [...document.querySelectorAll('button, select, input:not([type=checkbox]):not([type=radio]):not([type=hidden]):not([type=file]), .btn, .icon-btn, .chip, .menu-item, .bottom-nav a, .nav-link')].filter(visible);
        controls.filter((el) => el.getBoundingClientRect().height < 40).slice(0, 5).forEach((el) => add('Control under 40px', label(el) + ' ' + Math.round(el.getBoundingClientRect().height) + 'px'));
    }

    // Contrast of text against the first opaque background behind it (text over photos is skipped).
    const canvas = document.createElement('canvas').getContext('2d', { willReadFrequently: true });
    const rgba = (colour) => { canvas.clearRect(0, 0, 1, 1); canvas.fillStyle = '#000'; canvas.fillStyle = colour; canvas.fillRect(0, 0, 1, 1); const d = canvas.getImageData(0, 0, 1, 1).data; return [d[0], d[1], d[2], d[3] / 255]; };
    const luminance = ([r, g, b]) => { const f = (v) => { v /= 255; return v <= 0.03928 ? v / 12.92 : ((v + 0.055) / 1.055) ** 2.4; }; return 0.2126 * f(r) + 0.7152 * f(g) + 0.0722 * f(b); };
    const backgroundOf = (el) => {
        for (let node = el; node; node = node.parentElement) {
            const cs = getComputedStyle(node);
            if (cs.backgroundImage !== 'none' && node !== document.body) return null;
            const colour = rgba(cs.backgroundColor);
            if (colour[3] > 0.9) return colour;
        }
        return rgba(getComputedStyle(document.body).backgroundColor);
    };
    const lowContrast = new Set();
    texts.forEach((el) => {
        if (el.closest('.bird-card-media, .gallery-stage, .mosaic-tile, .auth-aside, .static-hero-media')) return;
        const cs = getComputedStyle(el);
        const bg = backgroundOf(el);
        if (! bg) return;
        const a = luminance(rgba(cs.color)), b = luminance(bg);
        const ratio = (Math.max(a, b) + 0.05) / (Math.min(a, b) + 0.05);
        const size = parseFloat(cs.fontSize);
        const large = size >= 24 || (size >= 18.66 && Number(cs.fontWeight) >= 700);
        if (ratio < (large ? 3 : 4.5)) lowContrast.add(label(el) + ' ' + ratio.toFixed(2) + ':1');
    });
    [...lowContrast].slice(0, 6).forEach((detail) => add('Low contrast', detail));

    showAll.remove();

    return { title: document.title, height: root.scrollHeight, issues };
})()`;
