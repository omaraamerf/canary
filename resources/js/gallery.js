// Bird detail gallery: a scroll-snap track with thumbnails, arrows, a counter and a lightbox.
document.querySelectorAll('[data-gallery]').forEach((gallery) => {
    const track = gallery.querySelector('[data-gallery-track]');
    const slides = [...track.children];
    const thumbs = [...gallery.querySelectorAll('[data-gallery-thumb]')];
    const counter = gallery.querySelector('[data-gallery-counter]');
    const lightbox = document.getElementById('gallery-lightbox');
    let current = 0;

    const show = (index) => {
        const target = slides[Math.max(0, Math.min(index, slides.length - 1))];
        // scrollIntoView handles RTL scroll offsets; "nearest" keeps the page itself still.
        target.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'start' });
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (! entry.isIntersecting) {
                return;
            }

            current = slides.indexOf(entry.target);
            thumbs.forEach((thumb, index) => thumb.toggleAttribute('aria-current', index === current));
            thumbs[current]?.setAttribute('aria-current', 'true');

            if (counter) {
                counter.textContent = `${current + 1} / ${slides.length}`;
            }
        });
    }, { root: track, threshold: 0.6 });

    slides.forEach((slide) => observer.observe(slide));
    thumbs.forEach((thumb) => thumb.addEventListener('click', () => show(Number(thumb.dataset.galleryThumb))));
    gallery.querySelector('[data-gallery-prev]')?.addEventListener('click', () => show(current - 1));
    gallery.querySelector('[data-gallery-next]')?.addEventListener('click', () => show(current + 1));

    const enlarge = () => {
        if (! lightbox) {
            return;
        }

        const image = slides[current].querySelector('img');
        const target = lightbox.querySelector('[data-lightbox-image]');
        // Full screen deserves the largest file in the srcset, not the one sized for the slide.
        const largest = (image.getAttribute('srcset') ?? '').split(',').map((candidate) => candidate.trim().split(/\s+/))
            .filter(([url, width]) => url && width).sort((a, b) => parseInt(b[1], 10) - parseInt(a[1], 10))[0]?.[0];
        target.src = largest ?? image.currentSrc ?? image.src;
        target.alt = image.alt;
        lightbox.showModal();
    };

    gallery.querySelector('[data-gallery-enlarge]')?.addEventListener('click', enlarge);
    track.addEventListener('click', (event) => {
        if (event.target.closest('img')) {
            enlarge();
        }
    });
});
