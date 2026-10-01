// Sheets, toasts and submit feedback for the components in resources/views/components/ui.

export function toast(message) {
    let region = document.querySelector('.toast-region');

    if (! region) {
        region = document.createElement('div');
        region.className = 'toast-region';
        region.setAttribute('role', 'status');
        region.setAttribute('aria-live', 'polite');
        document.body.append(region);
    }

    const item = document.createElement('div');
    item.className = 'toast';
    item.dataset.toast = '';
    item.textContent = message;
    region.append(item);
    setTimeout(() => item.remove(), 4000);
}

document.addEventListener('click', (event) => {
    const opener = event.target.closest('[data-sheet-open]');

    if (opener) {
        const sheet = document.getElementById(opener.dataset.sheetOpen);

        if (sheet && ! sheet.open) {
            sheet.showModal();
            opener.setAttribute('aria-expanded', 'true');
            sheet.addEventListener('close', () => opener.setAttribute('aria-expanded', 'false'), { once: true });
        }

        return;
    }

    // A modal dialog only receives clicks on its own box when the backdrop is clicked.
    if (event.target instanceof HTMLDialogElement && event.target.matches('.sheet, .lightbox')) {
        event.target.close();
    }

    const toastClose = event.target.closest('[data-toast-close]');

    if (toastClose) {
        toastClose.closest('[data-toast]').hidden = true;
    }
});

document.querySelectorAll('[data-toast]').forEach((item) => {
    setTimeout(() => { item.hidden = true; }, 6000);
});

// Show the clicked submit button as busy and ignore repeat submits (a double tap would send twice).
// Live filter forms submit many times without leaving the page, so they are left alone.
document.addEventListener('submit', (event) => {
    const form = event.target;

    if (event.defaultPrevented || form.method === 'dialog' || form.hasAttribute('data-live-filter')) {
        return;
    }

    if (form.dataset.submitting) {
        event.preventDefault();

        return;
    }

    form.dataset.submitting = 'true';
    event.submitter?.setAttribute('aria-busy', 'true');
});

// Pages restored from the back/forward cache must be submittable again.
window.addEventListener('pageshow', () => {
    document.querySelectorAll('form[data-submitting]').forEach((form) => {
        delete form.dataset.submitting;
        form.querySelectorAll('[aria-busy]').forEach((button) => button.removeAttribute('aria-busy'));
    });
});
