// The clicked bird card's photo carries the "bird-photo" transition name into the bird page,
// whose main photo has the same name (see base.css). Only one element may hold a name, so any
// earlier one (a page restored from the back/forward cache) is cleared first.
const NAME = 'bird-photo';

const clearNames = () => {
    document.querySelectorAll('.bird-card-media img').forEach((image) => { image.style.viewTransitionName = ''; });
};

document.addEventListener('click', (event) => {
    const link = event.target.closest('.bird-card-link');

    if (! link || event.metaKey || event.ctrlKey || event.shiftKey) {
        return;
    }

    clearNames();

    const photo = link.closest('.bird-card')?.querySelector('.bird-card-media img');

    if (photo) {
        photo.style.viewTransitionName = NAME;
    }
});

window.addEventListener('pageshow', (event) => {
    if (event.persisted) {
        clearNames();
    }
});
