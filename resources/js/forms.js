// Country → region pickers, image pickers and password toggles used across the public site.
document.addEventListener('DOMContentLoaded', () => {
    initLocationPickers();
    document.querySelectorAll('[data-image-picker]').forEach(initImagePicker);
    document.querySelectorAll('[data-password-toggle]').forEach(initPasswordToggle);
});

// The label stays "show password"; aria-pressed tells whether it is shown.
function initPasswordToggle(button) {
    const input = button.previousElementSibling;

    button.hidden = false;
    button.addEventListener('click', () => {
        const show = input.type === 'password';

        input.type = show ? 'text' : 'password';
        button.setAttribute('aria-pressed', String(show));
    });
    // Submitted as a password field again, so browsers do not keep it in text autofill history.
    input.form?.addEventListener('submit', () => { input.type = 'password'; });
}

// Exported so markup swapped in later (catalog filters) can be wired up again.
export function initLocationPickers(root = document) {
    const source = document.getElementById('location-data');
    const countries = source ? JSON.parse(source.textContent || '[]') : [];

    root.querySelectorAll('[data-location-picker]').forEach((picker) => {
        const country = picker.querySelector('[data-location-country]');
        const region = picker.querySelector('[data-location-region]');

        if (! country || ! region) {
            return;
        }

        country.addEventListener('change', () => {
            const selected = countries.find((item) => String(item.id) === country.value);

            region.replaceChildren(new Option(selected ? region.dataset.placeholder : region.dataset.empty, ''));
            (selected?.regions ?? []).forEach((item) => region.add(new Option(item.name, item.id)));
            region.disabled = ! selected;
        });
    });
}

const MAX_DIMENSION = 1920;
const COMPRESS_ABOVE_BYTES = 1.5 * 1024 * 1024;

function initImagePicker(picker) {
    const input = picker.querySelector('[data-image-input]');
    const drop = picker.querySelector('[data-image-drop]');
    const previews = picker.querySelector('[data-image-previews]');
    const status = picker.querySelector('[data-image-status]');
    const form = picker.closest('form');
    const max = Number(picker.dataset.max || 1);
    let files = [];
    let busy = 0;

    const sync = () => {
        const transfer = new DataTransfer();
        files.forEach((file) => transfer.items.add(file));
        input.files = transfer.files;
        render();
    };

    const setStatus = (message) => {
        status.hidden = ! message;
        status.textContent = message || '';
    };

    const render = () => {
        previews.replaceChildren(...files.map((file, index) => {
            const item = document.createElement('figure');
            const image = document.createElement('img');
            const remove = document.createElement('button');

            image.src = URL.createObjectURL(file);
            image.alt = file.name;
            image.onload = () => URL.revokeObjectURL(image.src);
            remove.type = 'button';
            remove.className = 'image-picker-remove';
            remove.setAttribute('aria-label', '×');
            remove.textContent = '×';
            remove.addEventListener('click', () => {
                files.splice(index, 1);
                setStatus('');
                sync();
            });

            item.append(image, remove);

            return item;
        }));
        picker.classList.toggle('has-files', files.length > 0);
        drop.hidden = files.length >= max;
    };

    const add = async (incoming) => {
        const images = [...incoming].filter((file) => file.type.startsWith('image/') || /\.(heic|heif)$/i.test(file.name));
        const room = max - files.length;

        if (images.length > room) {
            setStatus(picker.dataset.maxMessage);
        }

        busy++;
        toggleSubmit(form, busy > 0);

        const prepared = await Promise.all(images.slice(0, Math.max(room, 0)).map(compress));

        files = max === 1 ? prepared.slice(0, 1) : [...files, ...prepared].slice(0, max);
        busy--;
        toggleSubmit(form, busy > 0);
        sync();
    };

    input.addEventListener('change', () => {
        const selected = [...input.files];

        // The input already holds the new selection only; merge it with what was kept.
        const transfer = new DataTransfer();
        files.forEach((file) => transfer.items.add(file));
        input.files = transfer.files;
        add(selected);
    });

    ['dragenter', 'dragover'].forEach((type) => drop.addEventListener(type, (event) => {
        event.preventDefault();
        picker.classList.add('is-dragging');
    }));
    ['dragleave', 'drop'].forEach((type) => drop.addEventListener(type, (event) => {
        event.preventDefault();
        picker.classList.remove('is-dragging');
    }));
    drop.addEventListener('drop', (event) => add(event.dataTransfer?.files ?? []));
}

function toggleSubmit(form, disabled) {
    form?.querySelectorAll('button[type="submit"]').forEach((button) => {
        button.disabled = disabled;
    });
}

// Downscale large photos (typical phone pictures) before upload so they stay under the server limits.
async function compress(file) {
    if (! file.type.startsWith('image/') || file.type === 'image/gif' || file.size < COMPRESS_ABOVE_BYTES) {
        return file;
    }

    try {
        const bitmap = await createImageBitmap(file);
        const scale = Math.min(1, MAX_DIMENSION / Math.max(bitmap.width, bitmap.height));
        const canvas = document.createElement('canvas');

        canvas.width = Math.round(bitmap.width * scale);
        canvas.height = Math.round(bitmap.height * scale);
        canvas.getContext('2d').drawImage(bitmap, 0, 0, canvas.width, canvas.height);
        bitmap.close?.();

        const blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/jpeg', 0.85));

        if (! blob || blob.size >= file.size) {
            return file;
        }

        return new File([blob], file.name.replace(/\.[^.]+$/, '') + '.jpg', { type: 'image/jpeg', lastModified: Date.now() });
    } catch {
        // Formats the browser cannot decode (e.g. HEIC on some devices) are uploaded as-is.
        return file;
    }
}
