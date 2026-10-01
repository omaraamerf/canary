document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.querySelector('[data-menu-toggle]');
    const menu = document.querySelector('[data-mobile-menu]');

    if (! toggle || ! menu) {
        return;
    }

    const setOpen = (open) => {
        menu.classList.toggle('hidden', ! open);
        menu.classList.toggle('is-open', open);
        toggle.setAttribute('aria-expanded', String(open));
        toggle.setAttribute('aria-label', open ? 'إغلاق القائمة' : 'فتح القائمة');
    };

    toggle.addEventListener('click', () => {
        setOpen(toggle.getAttribute('aria-expanded') !== 'true');
    });

    menu.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => setOpen(false));
    });

    document.addEventListener('click', (event) => {
        if (
            toggle.getAttribute('aria-expanded') === 'true'
            && ! menu.contains(event.target)
            && ! toggle.contains(event.target)
        ) {
            setOpen(false);
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            setOpen(false);
            toggle.focus();
        }
    });

    const desktop = window.matchMedia('(min-width: 1101px)');
    desktop.addEventListener('change', (event) => {
        if (event.matches) {
            setOpen(false);
        }
    });
});

// Close header dropdowns (region, account) when clicking outside or pressing Escape.
document.addEventListener('DOMContentLoaded', () => {
    const dropdowns = document.querySelectorAll('.site-header details');

    document.addEventListener('click', (event) => {
        dropdowns.forEach((dropdown) => {
            if (dropdown.open && ! dropdown.contains(event.target)) {
                dropdown.open = false;
            }
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            dropdowns.forEach((dropdown) => { dropdown.open = false; });
        }
    });
});
