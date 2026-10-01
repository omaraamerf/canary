// Share a listing with the system share sheet, or copy its link where that is unavailable.
import { toast } from './ui';

document.addEventListener('click', async (event) => {
    const button = event.target.closest('[data-share]');

    if (! button) {
        return;
    }

    const url = window.location.href.split('#')[0];

    if (navigator.share) {
        try {
            await navigator.share({ title: button.dataset.shareTitle, url });
        } catch {
            // The user closed the share sheet.
        }

        return;
    }

    try {
        await navigator.clipboard.writeText(url);
        toast(button.dataset.shareCopied);
    } catch {
        window.prompt('', url);
    }
});
