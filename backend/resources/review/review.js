// مراجعة المنجز لصفحته: progress, «كل النقاط صحيحة», image uploads (resized in the browser first: the
// server is far from Oman), achievements added by the achiever. No framework, no motion.

const form = document.getElementById('rv-form');

if (form) {
    const items = () => [...form.querySelectorAll('[data-item]')];
    let dirty = false;
    let submitting = false;

    const decisionOf = (item) => item.querySelector('input[type=radio]:checked')?.value || '';

    function refresh() {
        const all = items();
        const done = all.filter((item) => decisionOf(item)).length;

        all.forEach((item) => { item.dataset.state = decisionOf(item); });

        form.querySelectorAll('[data-section]').forEach((section) => {
            const own = [...section.querySelectorAll('[data-item]')];
            const answered = own.filter((item) => decisionOf(item)).length;
            const label = `${answered}/${own.length}`;
            section.querySelector('[data-section-count]').textContent = label;
            section.classList.toggle('is-done', answered === own.length);

            const link = document.querySelector(`[data-toc="${section.dataset.section}"]`);
            if (link) {
                link.querySelector('[data-toc-count]').textContent = label;
                link.classList.toggle('is-done', answered === own.length);
                link.classList.toggle('is-partial', answered > 0 && answered < own.length);
            }
        });

        const text = form.querySelector('[data-progress-text]');
        text.textContent = text.dataset.template.replace('__D__', done).replace('__T__', all.length);
        form.querySelector('[data-progress-bar]').style.width = `${all.length ? Math.round((done / all.length) * 100) : 100}%`;

        const ready = done === all.length;
        const submit = form.querySelector('[data-submit]');
        submit.setAttribute('aria-disabled', ready ? 'false' : 'true');
        submit.title = ready ? '' : submit.dataset.remaining.replace('__N__', all.length - done);
        form.querySelector('[data-dock]').classList.toggle('is-ready', ready);
    }

    form.addEventListener('change', (event) => {
        dirty = true;
        if (event.target.matches('input[type=radio]')) {
            const item = event.target.closest('[data-item]');
            refresh();
            if (event.target.value === 'edit') {
                item.querySelector('.rv-edit .rv-input, .rv-edit [data-upload-file]')?.focus({ preventScroll: true });
            }
        }
    });
    form.addEventListener('input', () => { dirty = true; });

    // «كل نقاط هذا القسم صحيحة»: only the points not answered yet.
    form.addEventListener('click', (event) => {
        const button = event.target.closest('[data-confirm-section]');
        if (!button) return;
        button.closest('[data-section]').querySelectorAll('[data-item]').forEach((item) => {
            if (!decisionOf(item)) item.querySelector('input[value=ok]').checked = true;
        });
        dirty = true;
        refresh();
        const next = button.closest('[data-section]').nextElementSibling;
        const target = next?.matches('[data-section], .rv-sec') ? next : next?.nextElementSibling;
        target?.scrollIntoView({ block: 'start' });
    });

    // Submit only when every point is answered; otherwise take the achiever to the first one left.
    form.addEventListener('submit', (event) => {
        const action = event.submitter?.value;
        if (action === 'submit') {
            const missing = items().find((item) => !decisionOf(item));
            if (missing) {
                event.preventDefault();
                missing.scrollIntoView({ block: 'center' });
                missing.querySelector('input[type=radio]').focus({ preventScroll: true });
                return;
            }
            if (form.querySelector('[data-upload].is-busy')) {
                event.preventDefault();
                return;
            }
        }
        submitting = true;
    });

    window.addEventListener('beforeunload', (event) => {
        if (dirty && !submitting) {
            event.preventDefault();
            event.returnValue = '';
        }
    });

    // Textareas grow with their text.
    const autosize = (area) => {
        area.style.height = 'auto';
        area.style.height = `${Math.min(area.scrollHeight + 4, 900)}px`;
    };
    form.addEventListener('input', (event) => {
        if (event.target.matches('[data-autosize]')) autosize(event.target);
    });
    const sizeAll = (root) => root.querySelectorAll('[data-autosize]').forEach((area) => { if (area.offsetParent) autosize(area); });
    form.addEventListener('change', (event) => {
        const item = event.target.closest('[data-item]');
        if (item) sizeAll(item);
    });
    sizeAll(form);

    // ---------- images ----------
    const MAX_SIDE = 2400;
    const csrf = form.querySelector('input[name=_token]').value;

    /** Large photos are re-encoded to JPEG at 2400px at most; PNGs keep their transparency (cut-outs). */
    async function shrink(file) {
        if (file.type === 'image/png' || file.size < 1.5 * 1024 * 1024 || !window.createImageBitmap) return file;
        try {
            const bitmap = await createImageBitmap(file);
            const scale = Math.min(1, MAX_SIDE / Math.max(bitmap.width, bitmap.height));
            const canvas = document.createElement('canvas');
            canvas.width = Math.round(bitmap.width * scale);
            canvas.height = Math.round(bitmap.height * scale);
            canvas.getContext('2d').drawImage(bitmap, 0, 0, canvas.width, canvas.height);
            const blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/jpeg', 0.88));
            return blob && blob.size < file.size ? new File([blob], file.name.replace(/\.\w+$/, '') + '.jpg', { type: 'image/jpeg' }) : file;
        } catch {
            return file;
        }
    }

    function upload(box, file) {
        const status = box.querySelector('[data-upload-status]');
        const bar = box.querySelector('[data-upload-bar]');
        const fill = bar.querySelector('span');
        const preview = box.querySelector('[data-upload-preview]');
        const label = box.querySelector('[data-upload-label]');

        preview.innerHTML = '';
        const img = document.createElement('img');
        img.alt = '';
        img.src = URL.createObjectURL(file);
        preview.append(img);

        box.classList.add('is-busy');
        box.classList.remove('is-failed');
        status.textContent = status.dataset.uploading;
        bar.hidden = false;
        fill.style.width = '0%';

        shrink(file).then((ready) => {
            const data = new FormData();
            data.append('image', ready);
            const xhr = new XMLHttpRequest();
            xhr.open('POST', form.dataset.uploadUrl);
            xhr.setRequestHeader('X-CSRF-TOKEN', csrf);
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.upload.onprogress = (e) => { if (e.lengthComputable) fill.style.width = `${Math.round((e.loaded / e.total) * 100)}%`; };
            xhr.onload = () => {
                box.classList.remove('is-busy');
                bar.hidden = true;
                let path = '';
                try { path = JSON.parse(xhr.responseText).path || ''; } catch { path = ''; }
                if (xhr.status === 200 && path) {
                    box.querySelector('[data-upload-path]').value = path;
                    box.classList.add('has-file');
                    status.textContent = status.dataset.done;
                    label.textContent = label.dataset.replace;
                    dirty = true;
                } else {
                    box.classList.add('is-failed');
                    status.textContent = status.dataset.failed;
                }
            };
            xhr.onerror = () => {
                box.classList.remove('is-busy');
                box.classList.add('is-failed');
                bar.hidden = true;
                status.textContent = status.dataset.failed;
            };
            xhr.send(data);
        });
    }

    form.addEventListener('change', (event) => {
        if (!event.target.matches('[data-upload-file]')) return;
        const file = event.target.files?.[0];
        if (file) upload(event.target.closest('[data-upload]'), file);
        event.target.value = '';
    });

    // ---------- achievements added by the achiever ----------
    const list = form.querySelector('[data-additions]');
    const template = document.getElementById('rv-addition-template');

    form.querySelector('[data-addition-add]')?.addEventListener('click', () => {
        const uid = `n${Date.now().toString(36)}`;
        const html = template.innerHTML.replaceAll('__UID__', uid);
        list.insertAdjacentHTML('beforeend', html);
        const added = list.lastElementChild;
        added.querySelector('[data-addition-title]')?.focus();
        dirty = true;
    });

    list?.addEventListener('click', (event) => {
        const remove = event.target.closest('[data-addition-remove]');
        if (remove) {
            remove.closest('[data-addition]').remove();
            dirty = true;
        }
    });

    refresh();
}
