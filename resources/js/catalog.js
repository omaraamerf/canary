// Catalog filters refresh the results in place. On wide screens every change applies at once;
// on phones the filter sheet applies everything together with its submit button.
import { syncFavorites } from './favorites';
import { initLocationPickers } from './forms';

const FORM_ID = 'filters-form';
const desktop = window.matchMedia('(min-width: 1024px)');
let controller;
let typing;

const form = () => document.getElementById(FORM_ID);
const panel = () => document.getElementById('filters-panel');

const syncPriceRange = () => {
    const filters = form();
    const currency = filters.querySelector('input[name="currency"]:checked')?.value ?? '';

    filters.querySelectorAll('[data-price-range] input').forEach((input) => { input.disabled = currency === ''; });
    filters.querySelector('[data-price-hint]').hidden = currency !== '';
};

const urlFromForm = () => {
    const filters = form();
    const params = new URLSearchParams(new FormData(filters));

    [...params.keys()].forEach((key) => { if (params.get(key) === '') params.delete(key); });
    params.delete('page');

    if (params.get('sort') === 'newest') {
        params.delete('sort');
    }

    const query = params.toString();

    return filters.action + (query ? `?${query}` : '');
};

// Replace the results (and optionally the filter form) with the same parts of the page at `url`.
const load = async (url, { replaceForm = false } = {}) => {
    controller?.abort();
    controller = new AbortController();
    document.querySelector('[data-results]')?.setAttribute('aria-busy', 'true');

    try {
        const response = await fetch(url, { signal: controller.signal, headers: { Accept: 'text/html' } });
        const page = new DOMParser().parseFromString(await response.text(), 'text/html');
        const parts = ['[data-results]', '[data-results-count]', '[data-filters-count]'];

        if (replaceForm) {
            parts.push(`#${FORM_ID}`);
        }

        const swap = () => parts.forEach((selector) => {
            const fresh = page.querySelector(selector);

            if (fresh) {
                document.querySelector(selector)?.replaceWith(fresh);
            }
        });

        document.startViewTransition ? await document.startViewTransition(swap).updateCallbackDone : swap();
        syncFavorites();

        if (replaceForm) {
            initLocationPickers(form());
        }

        history.pushState({ catalog: true }, '', url);
    } catch (error) {
        if (error.name !== 'AbortError') {
            window.location.assign(url);
        }
    }
};

if (form()) {
    document.addEventListener('submit', (event) => {
        if (event.target.id !== FORM_ID) {
            return;
        }

        event.preventDefault();
        load(urlFromForm());

        if (panel()?.matches(':popover-open')) {
            panel().hidePopover();
        }
    });

    // The sort menu lives outside the form (form="filters-form"), so listen on the document.
    document.addEventListener('change', (event) => {
        if (event.target.form?.id !== FORM_ID) {
            return;
        }

        if (event.target.name === 'currency') {
            syncPriceRange();
        }

        if (desktop.matches || ! panel()?.contains(event.target)) {
            load(urlFromForm());
        }
    });

    document.addEventListener('input', (event) => {
        if (event.target.form?.id !== FORM_ID || ! desktop.matches || ! ['q', 'color', 'min_price', 'max_price'].includes(event.target.name)) {
            return;
        }

        clearTimeout(typing);
        typing = setTimeout(() => load(urlFromForm()), 450);
    });

    // Filter chips and pagination point at this same page: load them in place too.
    document.addEventListener('click', (event) => {
        const link = event.target.closest('[data-results] a[href], .filter-title a[href]');

        if (! link || event.metaKey || event.ctrlKey || new URL(link.href).pathname !== new URL(form().action).pathname) {
            return;
        }

        event.preventDefault();
        load(link.href, { replaceForm: true });
        document.querySelector('.catalog')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    window.addEventListener('popstate', () => window.location.reload());
}
