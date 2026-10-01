// Offline support (public/sw.js) and the "install the app" button in the menu.

if ('serviceWorker' in navigator && window.isSecureContext) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(() => {
            // Without the worker the site simply needs the network, as before.
        });
    });
}

let installPrompt;

const showInstall = (visible) => {
    document.querySelectorAll('[data-install-app]').forEach((button) => { button.hidden = ! visible; });
};

// Chrome offers installing; keep the offer for our own button instead of its banner.
window.addEventListener('beforeinstallprompt', (event) => {
    event.preventDefault();
    installPrompt = event;
    showInstall(true);
});

document.addEventListener('click', async (event) => {
    if (! event.target.closest('[data-install-app]') || ! installPrompt) {
        return;
    }

    installPrompt.prompt();
    await installPrompt.userChoice;
    installPrompt = null;
    showInstall(false);
});

window.addEventListener('appinstalled', () => showInstall(false));
