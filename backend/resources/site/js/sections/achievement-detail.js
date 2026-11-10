// Achievement page behaviour (design js/achievement-enhance.js): reading progress bar, compact header with the
// achiever's photo, the share sheet, and the "بطاقة الإنجاز" image card (canvas -> PNG / PDF / share).
// The career path, read time and share links are rendered by the server.
import { label, reducedMotion } from '../core/labels.js';

const $ = (s, r = document) => r.querySelector(s);
const article = $('[data-article]');
const title = ($('#achievement-title')?.textContent || document.title).trim();
const showDialog = (d) => (d.showModal ? d.showModal() : d.setAttribute('open', ''));

// Reading progress
const bar = $('[data-read-progress]');
if (bar && article) {
    const tick = () => {
        const r = article.getBoundingClientRect();
        const vh = window.innerHeight;
        const p = Math.min(1, Math.max(0, (vh * 0.35 - r.top) / (r.height - vh * 0.35)));
        bar.style.transform = `scaleX(${p.toFixed(3)})`;
    };
    addEventListener('scroll', tick, { passive: true });
    addEventListener('resize', tick);
    tick();
}

// Compact header with the achiever's photo once the hero scrolls away
const header = $('.site-header');
const hero = $('.story-hero');
const crumb = $('.crumb');
const author = $('.story-author[data-avatar]');
if (header && hero && crumb && author?.dataset.avatar && 'IntersectionObserver' in window) {
    const avatar = document.createElement('span');
    avatar.className = 'crumb-avatar';
    avatar.setAttribute('aria-hidden', 'true');
    const img = document.createElement('img');
    img.src = author.dataset.avatar;
    img.alt = '';
    avatar.appendChild(img);
    crumb.parentNode.insertBefore(avatar, crumb);
    new IntersectionObserver(([e]) => header.classList.toggle('is-compact', !e.isIntersecting), { rootMargin: '-64px 0px 0px 0px' }).observe(hero);
}

// Share sheet
const sheet = $('#share-sheet');
const fab = $('[data-share-open]');
if (sheet && fab) {
    fab.addEventListener('click', () => showDialog(sheet));
    $('[data-share-close]', sheet)?.addEventListener('click', () => sheet.close());
    sheet.addEventListener('click', (e) => { if (e.target === sheet) sheet.close(); });
    const copyLabel = $('[data-copy-label]', sheet);
    $('[data-copy-link]', sheet)?.addEventListener('click', () => {
        const url = location.href.split('#')[0];
        const done = () => {
            copyLabel.textContent = label('copied');
            setTimeout(() => { copyLabel.textContent = label('copy_link'); }, 1600);
        };
        if (navigator.clipboard) navigator.clipboard.writeText(url).then(done, () => prompt(label('copy_prompt'), url));
        else prompt(label('copy_prompt'), url);
    });
    const footer = $('.site-footer');
    if (footer && 'IntersectionObserver' in window) {
        new IntersectionObserver(([e]) => fab.classList.toggle('away', e.isIntersecting)).observe(footer);
    }
}

// Achievement card
const dialog = $('#acard');
const card = $('[data-acard-sheet]');
if (dialog && card) {
    const note = $('[data-acard-note]', dialog);
    const say = (text) => { note.hidden = false; note.textContent = text; };
    const data = card.dataset;
    let canvas = null;

    const loadImage = (src) => new Promise((resolve, reject) => {
        if (!src) return reject(new Error('no image'));
        const image = new Image();
        image.crossOrigin = 'anonymous'; // OneBox images: CORS so the canvas can be exported
        image.onload = () => resolve(image);
        image.onerror = reject;
        image.src = src;
    });
    const roundRect = (ctx, x, y, w, h, r) => {
        ctx.beginPath(); ctx.moveTo(x + r, y);
        ctx.arcTo(x + w, y, x + w, y + h, r); ctx.arcTo(x + w, y + h, x, y + h, r);
        ctx.arcTo(x, y + h, x, y, r); ctx.arcTo(x, y, x + w, y, r); ctx.closePath();
    };
    const wrap = (ctx, text, max) => {
        const out = [];
        let line = '';
        text.split(' ').forEach((word) => {
            const next = line ? `${line} ${word}` : word;
            if (ctx.measureText(next).width > max && line) { out.push(line); line = word; } else line = next;
        });
        if (line) out.push(line);
        return out;
    };

    async function draw() {
        await document.fonts.ready;
        const W = 1080, H = 1350, F = '"Cairo", Tahoma, sans-serif';
        const [photo, avatar, logo, mark] = await Promise.all([
            loadImage(data.photo),
            loadImage(data.avatar).catch(() => null),
            loadImage(data.logo).catch(() => null),
            loadImage(data.mark).catch(() => null),
        ]);
        const c = document.createElement('canvas'); c.width = W; c.height = H;
        const ctx = c.getContext('2d');
        roundRect(ctx, 0, 0, W, H, 56); ctx.clip();
        const bg = ctx.createLinearGradient(0, 0, W * 0.6, H);
        bg.addColorStop(0, '#0C7F89'); bg.addColorStop(0.55, '#075A62'); bg.addColorStop(1, '#063135');
        ctx.fillStyle = bg; ctx.fillRect(0, 0, W, H);

        // Photo fading into the background
        const ph = 760, sc = Math.max(W / photo.width, ph / photo.height);
        const pc = document.createElement('canvas'); pc.width = W; pc.height = ph;
        const px = pc.getContext('2d');
        px.drawImage(photo, (W - photo.width * sc) / 2, (ph - photo.height * sc) * 0.3, photo.width * sc, photo.height * sc);
        px.globalCompositeOperation = 'destination-in';
        const mask = px.createLinearGradient(0, 0, 0, ph);
        mask.addColorStop(0, 'rgba(0,0,0,1)'); mask.addColorStop(0.55, 'rgba(0,0,0,1)'); mask.addColorStop(1, 'rgba(0,0,0,0)');
        px.fillStyle = mask; px.fillRect(0, 0, W, ph);
        ctx.drawImage(pc, 0, 0);
        const top = ctx.createLinearGradient(0, 0, 0, 220);
        top.addColorStop(0, 'rgba(4,38,42,.75)'); top.addColorStop(1, 'rgba(4,38,42,0)');
        ctx.fillStyle = top; ctx.fillRect(0, 0, W, 220);
        if (mark) { ctx.globalAlpha = 0.06; const mw = W * 1.1, mh = mw * mark.height / mark.width; ctx.drawImage(mark, (W - mw) / 2, H - mh * 0.8, mw, mh); ctx.globalAlpha = 1; }

        // Header
        if (logo) { const lh = 120, lw = lh * logo.width / logo.height; ctx.drawImage(logo, W - 60 - lw, 44, lw, lh); }
        ctx.direction = 'rtl'; ctx.textAlign = 'left';
        ctx.fillStyle = '#F0D9A5'; ctx.font = `700 36px ${F}`; ctx.fillText(label('card_kicker'), 60, 100);
        ctx.direction = 'ltr'; ctx.fillStyle = 'rgba(255,255,255,.6)'; ctx.font = `400 22px ${F}`; ctx.letterSpacing = '6px';
        ctx.fillText(label('card_kicker_en'), 60, 138); ctx.letterSpacing = '0px';

        // Scope and year
        ctx.direction = 'rtl'; ctx.textAlign = 'right';
        let y = 760; ctx.font = `700 32px ${F}`;
        const pill = [data.scope, data.year].filter(Boolean).join('  ·  ');
        const pw = ctx.measureText(pill).width + 64;
        const gold = ctx.createLinearGradient(W - 60 - pw, 0, W - 60, 0); gold.addColorStop(0, '#C9A35C'); gold.addColorStop(1, '#F4DFAE');
        roundRect(ctx, W - 60 - pw, y, pw, 60, 30); ctx.fillStyle = gold; ctx.fill();
        ctx.fillStyle = '#2F230C'; ctx.fillText(pill, W - 92, y + 42);

        // Title
        ctx.fillStyle = '#fff'; ctx.font = `800 66px ${F}`;
        const lines = wrap(ctx, (data.title || title).trim(), W - 120).slice(0, 3);
        lines.forEach((l, i) => ctx.fillText(l, W - 60, 910 + i * 92));
        y = 910 + lines.length * 92;

        // Achiever
        const cy = Math.min(H - 190, y + 40), r = 46, cx = W - 60 - r;
        ctx.save(); ctx.beginPath(); ctx.arc(cx, cy, r, 0, Math.PI * 2);
        const cg = ctx.createRadialGradient(cx, cy - 15, 5, cx, cy, r); cg.addColorStop(0, 'rgba(240,217,165,.5)'); cg.addColorStop(1, 'rgba(6,49,53,.7)');
        ctx.fillStyle = cg; ctx.fill(); ctx.restore();
        if (avatar) {
            const aw = r * 4.84, ah = aw * avatar.height / avatar.width;
            ctx.save(); ctx.beginPath();
            ctx.rect(cx - r, cy - r * 2.2, r * 2, r * 2.2); ctx.moveTo(cx + r, cy); ctx.arc(cx, cy, r, 0, Math.PI);
            ctx.clip();
            ctx.drawImage(avatar, cx - aw * 0.617, cy + r - ah * 0.53, aw, ah);
            ctx.restore();
        }
        ctx.beginPath(); ctx.arc(cx, cy, r, 0, Math.PI * 2); ctx.strokeStyle = '#D9B977'; ctx.lineWidth = 3; ctx.stroke();
        ctx.textAlign = 'right'; ctx.fillStyle = '#F0D9A5'; ctx.font = `700 40px ${F}`; ctx.fillText(data.name || '', cx - r - 24, cy - 2);
        ctx.fillStyle = '#CFE3E1'; ctx.font = `400 28px ${F}`; ctx.fillText(data.field || '', cx - r - 24, cy + 40);

        // Footer
        ctx.textAlign = 'center'; ctx.fillStyle = 'rgba(255,255,255,.75)'; ctx.font = `400 28px ${F}`;
        ctx.fillText(`${data.site}   ✦   ${label('card_motto')}`, W / 2, H - 52);
        const stripe = ctx.createLinearGradient(0, 0, W, 0);
        ['#9C7638', '#F4DFAE', '#C9A35C', '#F4DFAE', '#9C7638'].forEach((col, i) => stripe.addColorStop(i / 4, col));
        ctx.fillStyle = stripe; ctx.fillRect(0, H - 14, W, 14);
        return c;
    }

    const ensure = async () => {
        if (canvas) return canvas;
        canvas = await draw();
        const img = document.createElement('img');
        img.src = canvas.toDataURL('image/png');
        img.alt = label('card_alt', { title });
        const shine = document.createElement('i');
        shine.className = 'idc-shine';
        shine.setAttribute('aria-hidden', 'true');
        card.replaceChildren(img, shine);
        return canvas;
    };
    const closeCard = () => {
        if (!dialog.open) return;
        if (reducedMotion()) { dialog.close(); return; }
        dialog.classList.add('is-closing');
        setTimeout(() => { dialog.close(); dialog.classList.remove('is-closing'); }, 220);
    };

    document.querySelectorAll('[data-acard-open]').forEach((button) => button.addEventListener('click', async () => {
        if (sheet?.open) sheet.close();
        note.hidden = true;
        try { await ensure(); } catch { say(label('card_failed')); }
        dialog.classList.remove('is-closing');
        showDialog(dialog);
    }));
    $('[data-acard-close]', dialog).addEventListener('click', closeCard);
    dialog.addEventListener('cancel', (e) => { e.preventDefault(); closeCard(); });
    dialog.addEventListener('click', (e) => { if (e.target === dialog) closeCard(); });

    $('[data-acard-image]', dialog).addEventListener('click', async () => {
        try {
            const c = await ensure();
            const a = document.createElement('a');
            a.href = c.toDataURL('image/png'); a.download = 'injazat-achievement.png'; a.click();
            say(label('image_saved'));
        } catch { say(label('image_failed')); }
    });
    $('[data-acard-pdf]', dialog).addEventListener('click', () => {
        const page = document.createElement('style');
        page.textContent = '@page { size: 120mm 150mm; margin: 0; }';
        document.head.appendChild(page);
        document.body.classList.add('printing-acard');
        const done = () => { document.body.classList.remove('printing-acard'); page.remove(); };
        addEventListener('afterprint', done, { once: true });
        print();
        setTimeout(done, 1500);
    });
    $('[data-acard-share]', dialog).addEventListener('click', async () => {
        const url = location.href.split('#')[0];
        try {
            const c = await ensure();
            const blob = await new Promise((resolve) => c.toBlob(resolve, 'image/png'));
            const file = new File([blob], 'injazat-achievement.png', { type: 'image/png' });
            if (navigator.canShare && navigator.canShare({ files: [file] })) await navigator.share({ files: [file], title, text: `${title}\n${url}` });
            else if (navigator.share) await navigator.share({ title, url });
            else { await navigator.clipboard.writeText(`${title}\n${url}`); say(label('link_copied')); }
        } catch (e) {
            if (!e || e.name !== 'AbortError') say(label('share_failed'));
        }
    });
}
