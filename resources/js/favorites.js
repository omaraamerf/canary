// Favourite birds, kept in this browser only so they work without an account:
// localStorage "favorites" holds bird ids, most recently saved last.
import { toast } from './ui';

const KEY = 'favorites';
const MAX = 60;

const read = () => {
    try {
        const ids = JSON.parse(localStorage.getItem(KEY) || '[]');

        return Array.isArray(ids) ? ids.filter(Number.isInteger) : [];
    } catch {
        return [];
    }
};

const write = (ids) => {
    try {
        localStorage.setItem(KEY, JSON.stringify(ids.slice(-MAX)));
    } catch {
        // Blocked storage: the heart still toggles for this page.
    }
};

// Hearts and counters reflect the saved list; exported for markup swapped in later (catalog).
export function syncFavorites(root = document) {
    const ids = read();

    root.querySelectorAll('[data-favorite]').forEach((button) => {
        button.hidden = false;
        button.setAttribute('aria-pressed', String(ids.includes(Number(button.dataset.favorite))));
    });

    document.querySelectorAll('[data-favorites-count]').forEach((badge) => {
        badge.textContent = ids.length;
        badge.hidden = ids.length === 0;
    });
}

document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-favorite]');

    if (! button) {
        return;
    }

    // The heart sits on a card whose link covers it; it must not open the bird.
    event.preventDefault();

    const id = Number(button.dataset.favorite);
    const ids = read();
    const saving = ! ids.includes(id);

    write(saving ? [...ids, id] : ids.filter((saved) => saved !== id));
    syncFavorites();
    toast(saving ? button.dataset.addedMessage : button.dataset.removedMessage);

    const list = button.closest('[data-favorites-list]');

    if (list && ! saving) {
        button.closest('.bird-card')?.remove();
        showEmpty(list);
    }
});

// Another tab changed the list.
window.addEventListener('storage', (event) => {
    if (event.key === KEY) {
        syncFavorites();
    }
});

const showEmpty = (list) => {
    const empty = ! list.querySelector('.bird-card');

    list.hidden = empty;
    document.querySelector('[data-favorites-empty]').hidden = ! empty;
};

// The favourites page: fetch the saved birds' cards; ids no longer listed on the site are dropped.
const list = document.querySelector('[data-favorites-list]');

if (list) {
    const ids = read();

    (async () => {
        if (ids.length) {
            try {
                const response = await fetch(`${list.dataset.source}?ids=${ids.join(',')}`, { headers: { Accept: 'text/html' } });

                list.innerHTML = await response.text();

                const found = new Set([...list.querySelectorAll('[data-favorite]')].map((button) => Number(button.dataset.favorite)));
                write(ids.filter((id) => found.has(id)));
            } catch {
                // Offline or failed: the empty state explains what favourites are.
            }
        }

        list.removeAttribute('aria-busy');
        syncFavorites();
        showEmpty(list);
    })();
}

syncFavorites();
