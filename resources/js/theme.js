// Dark mode toggle. Without a saved choice the OS preference applies; partials/theme-script
// re-applies a saved choice before first paint on the next page.

const root = document.documentElement;
const prefersDark = window.matchMedia('(prefers-color-scheme: dark)');
const isDark = () => (root.dataset.theme ?? (prefersDark.matches ? 'dark' : 'light')) === 'dark';

const sync = () => {
    document.querySelectorAll('[data-theme-toggle]').forEach((toggle) => {
        toggle.setAttribute('aria-pressed', String(isDark()));
    });
};

document.addEventListener('click', (event) => {
    if (! event.target.closest('[data-theme-toggle]')) {
        return;
    }

    const theme = isDark() ? 'light' : 'dark';
    root.dataset.theme = theme;

    try {
        localStorage.setItem('theme', theme);
    } catch {
        // Private mode or blocked storage: the choice lasts for this page only.
    }

    sync();
});

prefersDark.addEventListener('change', sync);
sync();
