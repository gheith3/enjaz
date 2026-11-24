// مراجعة المنجز لصفحته («ملف توثيق»): «تعديل» on a point, rows added and removed (stations, achievements, interviews),
// photos uploaded one by one as soon as they are chosen (resized in the browser first: the server is far from Oman),
// word counters (≥ 120 words for an achievement, ≤ 8 for a station). No framework.

const form = document.getElementById('f');

if (form) {
    const uploadUrl = form.dataset.uploadUrl;
    const csrf = form.querySelector('input[name=_token]').value;
    const arabic = (n) => String(n).replace(/\d/g, (d) => '٠١٢٣٤٥٦٧٨٩'[d]);
    const words = (t) => (t.trim().match(/\S+/g) || []).length;
    let uid = 0;
    let dirty = false;
    let leaving = false;

    function count(field) {
        const c = field.parentElement.querySelector('.count');
        if (!c) return;
        const w = words(field.value);
        const min = Number(c.dataset.min || 0);
        const max = Number(c.dataset.max || 0);
        if (min) {
            c.textContent = `${arabic(w)} كلمة${w < min ? ` · بقي ${arabic(min - w)} للوصول إلى ${arabic(min)}` : ' ✓'}`;
            c.className = `count ${w < min ? 'is-bad' : 'is-good'}`;
        }
        if (max) {
            c.textContent = `${arabic(w)} من ${arabic(max)} كلمات`;
            c.className = `count${w > max ? ' is-bad' : ''}`;
        }
    }

    function limits() {
        form.querySelectorAll('[data-list][data-max]').forEach((list) => {
            const full = list.children.length >= Number(list.dataset.max);
            const card = list.closest('.card');
            const add = card.querySelector('[data-add]');
            const limit = card.querySelector('[data-limit]');
            if (add) add.disabled = full;
            if (limit) limit.hidden = !full;
        });
    }

    function addRow(kind) {
        const list = form.querySelector(`[data-list="${kind}"]`);
        const max = Number(list.dataset.max || 0);
        if (max && list.children.length >= max) return null;
        uid += 1;
        list.insertAdjacentHTML('beforeend', document.getElementById(`tpl-${kind}`).innerHTML.split('__UID__').join(`n${Date.now().toString(36)}${uid}`));
        const row = list.lastElementChild;
        row.querySelectorAll('[data-words]').forEach(count);
        limits();
        dirty = true;
        return row;
    }

    /** Large photos are re-encoded to JPEG at 2400px at most; PNGs keep their transparency. */
    async function shrink(file) {
        if (file.type === 'image/png' || file.size < 1.5 * 1024 * 1024 || !window.createImageBitmap) return file;
        try {
            const bitmap = await createImageBitmap(file);
            const scale = Math.min(1, 2400 / Math.max(bitmap.width, bitmap.height));
            const canvas = document.createElement('canvas');
            canvas.width = Math.round(bitmap.width * scale);
            canvas.height = Math.round(bitmap.height * scale);
            canvas.getContext('2d').drawImage(bitmap, 0, 0, canvas.width, canvas.height);
            const blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/jpeg', 0.88));
            return blob && blob.size < file.size ? new File([blob], `${file.name.replace(/\.\w+$/, '')}.jpg`, { type: 'image/jpeg' }) : file;
        } catch {
            return file;
        }
    }

    /** Uploads one photo; resolves with its stored path (or null). onProgress gets 0..1. */
    async function upload(file, onProgress) {
        const ready = await shrink(file);
        return new Promise((resolve) => {
            const data = new FormData();
            data.append('image', ready);
            const xhr = new XMLHttpRequest();
            xhr.open('POST', uploadUrl);
            xhr.setRequestHeader('X-CSRF-TOKEN', csrf);
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.upload.onprogress = (e) => { if (e.lengthComputable && onProgress) onProgress(e.loaded / e.total); };
            xhr.onload = () => {
                try { resolve(xhr.status === 200 ? JSON.parse(xhr.responseText).path || null : null); } catch { resolve(null); }
            };
            xhr.onerror = () => resolve(null);
            xhr.send(data);
        });
    }

    form.addEventListener('click', (ev) => {
        const t = ev.target;
        const pt = t.closest('[data-pt]');
        if (t.closest('[data-edit]')) {
            pt.classList.add('is-editing', 'is-edited');
            pt.querySelector('[data-decision]').value = 'edit';
            pt.querySelectorAll('[data-words]').forEach(count);
            pt.querySelector('.pt-edit input[type=text], .pt-edit textarea, .pt-edit select')?.focus();
            dirty = true;
        }
        if (t.closest('[data-cancel]') && pt) {
            pt.classList.remove('is-editing', 'is-edited');
            pt.querySelector('[data-decision]').value = 'ok';
        }
        const add = t.closest('[data-add]');
        if (add) addRow(add.dataset.add)?.querySelector('input[type=text], select')?.focus();
        if (t.closest('[data-remove-row]')) {
            t.closest('[data-row]').remove();
            limits();
            dirty = true;
        }
    });

    form.addEventListener('input', (ev) => {
        dirty = true;
        if (ev.target.matches('[data-words]')) count(ev.target);
    });

    form.addEventListener('change', async (ev) => {
        const t = ev.target;
        // One photo (main photo, achievement photo)
        if (t.matches('[data-file]') && t.files[0]) {
            const pick = t.closest('[data-pick]');
            const label = pick.querySelector('[data-pick-label]');
            const hidden = pick.nextElementSibling;
            const file = t.files[0];
            pick.querySelector('img')?.remove();
            const img = document.createElement('img');
            img.alt = '';
            img.src = URL.createObjectURL(file);
            pick.prepend(img);
            pick.classList.add('is-busy');
            pick.classList.remove('is-failed');
            label.textContent = label.dataset.busy;
            const path = await upload(file, (p) => { label.textContent = `${label.dataset.busy} ${arabic(Math.round(p * 100))}٪`; });
            pick.classList.remove('is-busy');
            if (path) {
                hidden.value = path;
                label.textContent = label.dataset.done;
            } else {
                pick.classList.add('is-failed');
                label.textContent = label.dataset.failed;
            }
            t.value = '';
            dirty = true;
        }
        // Several photos for «المكتبة المرئية»: a row each (photo right, title left), uploaded one after the other
        if (t.matches('[data-gallery]') && t.files.length) {
            const files = Array.from(t.files);
            t.value = '';
            for (const file of files) {
                const row = addRow('photo');
                const img = row.querySelector('[data-photo]');
                img.src = URL.createObjectURL(file);
                row.classList.add('is-busy');
                // eslint-disable-next-line no-await-in-loop
                const path = await upload(file);
                row.classList.remove('is-busy');
                if (path) row.querySelector('[data-path]').value = path;
                else row.remove();
            }
        }
    });

    form.addEventListener('submit', (ev) => {
        if (form.querySelector('.is-busy')) {
            ev.preventDefault();
            return;
        }
        leaving = true;
    });

    window.addEventListener('beforeunload', (ev) => {
        if (dirty && !leaving) {
            ev.preventDefault();
            ev.returnValue = '';
        }
    });

    form.querySelectorAll('[data-words]').forEach(count);
    limits();
}
