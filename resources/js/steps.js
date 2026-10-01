// Multi-step forms ([data-steps]): one fieldset at a time, and "next" checks the current
// step first. Without JavaScript every step stays visible and the form submits as usual.
document.querySelectorAll('[data-steps]').forEach((form) => {
    const steps = [...form.querySelectorAll('[data-step]')];
    const indicator = [...document.querySelectorAll('[data-step-indicator] li')];
    const prev = form.querySelector('[data-step-prev]');
    const next = form.querySelector('[data-step-next]');
    const submit = form.querySelector('[data-step-submit]');
    const count = form.querySelector('[data-step-count]');
    let current = 0;

    const show = (index, focus = true) => {
        current = index;
        steps.forEach((step, i) => { step.hidden = i !== index; });
        indicator.forEach((item, i) => {
            item.classList.toggle('is-current', i === index);
            item.classList.toggle('is-done', i < index);
        });
        prev.hidden = index === 0;
        next.hidden = index === steps.length - 1;
        submit.hidden = index !== steps.length - 1;

        if (count) {
            count.textContent = count.dataset.template?.replace(':current', index + 1) ?? `${index + 1} / ${steps.length}`;
        }

        if (focus) {
            steps[index].querySelector('input:not([type="hidden"]), select, textarea')?.focus({ preventScroll: true });
            form.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    };

    next.addEventListener('click', () => {
        const invalid = [...steps[current].querySelectorAll('input, select, textarea')].find((field) => ! field.checkValidity());

        if (invalid) {
            invalid.reportValidity();

            return;
        }

        show(current + 1);
    });

    prev.addEventListener('click', () => show(current - 1));

    // After a failed submit, start on the step that holds the field the server rejected.
    const errorField = form.dataset.errorField?.split('.')[0];
    const start = errorField ? steps.findIndex((step) => step.querySelector(`[name="${errorField}"], [name="${errorField}[]"]`)) : 0;

    show(Math.max(start, 0), false);
});

// Live preview: [data-preview-source="title|body|category"] fields fill [data-preview="…"].
document.addEventListener('input', (event) => {
    const source = event.target.dataset?.previewSource;

    if (! source) {
        return;
    }

    const target = document.querySelector(`[data-preview="${source}"]`);

    if (! target) {
        return;
    }

    if (source === 'category') {
        target.textContent = event.target.dataset.previewLabel;
        target.className = `post-category ${event.target.dataset.previewClass}`;

        return;
    }

    target.textContent = event.target.value.trim() || target.dataset.placeholder;
});
