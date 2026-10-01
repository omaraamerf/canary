// Bird detail page: clicking a thumbnail shows it in the main image.
document.addEventListener('DOMContentLoaded', () => {
    const main = document.querySelector('[data-gallery-main]');

    if (! main) {
        return;
    }

    document.querySelectorAll('[data-gallery-image]').forEach((thumb) => {
        thumb.addEventListener('click', () => {
            main.src = thumb.dataset.galleryImage;
        });
    });
});
