// Ported from design/js/profile-idcard.js (kept as in the design; data now comes from the server-rendered page).
/* البطاقة التعريفية — بطاقة منجز عمودية تنبثق عند الضغط،
   مع حفظ PDF، وحفظ صورة (تُرسم بنفس التصميم)، ومشاركة، وإغلاق.
   البيانات من #member-card-data. */
(function () {
  const dialog = document.getElementById('idcard');
  const openBtn = document.querySelector('[data-idcard-open]');
  const sheet = document.getElementById('idcard-sheet');
  const note = dialog && dialog.querySelector('[data-idcard-note]');
  const dataNode = document.getElementById('member-card-data');
  if (!dialog || !openBtn || !sheet || !dataNode) return;

  const data = JSON.parse(dataNode.textContent);
  const name = data.name;
  const bio = data.bio;
  const SLOGAN = 'نحن إنجازات هذا الوطن';
  const ar = (v) => String(v).replace(/\d/g, (d) => '٠١٢٣٤٥٦٧٨٩'[d]);
  const esc = (v) => String(v || '').replace(/[&<>"]/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));
  const BADGE = '<svg class="idc-badge" viewBox="0 0 64 64" aria-label="منجز موثّق"><path fill="#C6A15A" stroke="#F4DFAE" stroke-width="2" d="M32 6L40.4 11.7 50.4 13.6 52.3 23.6 58 32 52.3 40.4 50.4 50.4 40.4 52.3 32 58 23.6 52.3 13.6 50.4 11.7 40.4 6 32 11.7 23.6 13.6 13.6 23.6 11.7Z"/><path d="M23 33.2 29.2 40 42 25" fill="none" stroke="#fff" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/></svg>';

  sheet.dataset.tone = data.tone || 'culture';
  sheet.innerHTML = ''
    + '<div class="idc-pattern" aria-hidden="true"></div>'
    + '<header class="idc-top"><img class="idc-logo" src="' + esc(data.logo) + '" alt="' + esc(data.site) + '">'
    + '<span class="idc-kind"><b>بطاقة منجز</b><small>ACHIEVER ID</small></span></header>'
    + '<div class="idc-body"><figure class="idc-photo"><img src="' + esc(data.photo) + '" alt="' + esc(name) + '">' + BADGE + '</figure>'
    + '<div class="idc-info"><div class="idc-id"><h2 id="idcard-name">' + esc(name) + '</h2>'
    + (data.name_en ? '<p class="idc-en" lang="en">' + esc(data.name_en) + '</p>' : '')
    + '<span class="idc-field">' + esc(data.field) + '</span></div>'
    + '<p class="idc-bio">' + esc(bio) + '</p></div></div>'
    + '<ul class="idc-stats">' + data.stats.map((s) => '<li><strong>' + ar(s.value) + '</strong><span>' + esc(s.label) + '</span></li>').join('') + '</ul>'
    + '<footer class="idc-foot"><span>' + esc(data.site) + '</span><b>✦</b><span>' + SLOGAN + '</span></footer>'
    + '<i class="idc-shine" aria-hidden="true"></i>';

  function say(text) {
    note.hidden = false;
    note.textContent = text;
  }

  async function share(withNote) {
    const payload = { title: name, text: name + ' — ' + bio, url: location.href };
    try {
      if (navigator.share) await navigator.share(payload);
      else {
        await navigator.clipboard.writeText(payload.text + '\n' + payload.url);
        if (withNote) say('نُسخ الرابط والنبذة. الصقه حيث تريد المشاركة.');
      }
    } catch (error) {
      if (error && error.name === 'AbortError') return;
      if (withNote) say('تعذرت المشاركة من هذا المتصفح.');
    }
  }

  const shareBtn = document.querySelector('[data-profile-share]');
  if (shareBtn) shareBtn.addEventListener('click', () => share(false));

  // فتح وإغلاق مع حركة الانبثاق
  openBtn.addEventListener('click', () => {
    note.hidden = true;
    dialog.classList.remove('is-closing');
    if (typeof dialog.showModal === 'function') dialog.showModal();
    else dialog.setAttribute('open', '');
  });
  function closeCard() {
    if (!dialog.open) return;
    const reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (reduce) { dialog.close(); return; }
    dialog.classList.add('is-closing');
    setTimeout(() => { dialog.close(); dialog.classList.remove('is-closing'); }, 220);
  }
  dialog.querySelector('[data-idcard-close]').addEventListener('click', closeCard);
  dialog.addEventListener('cancel', (event) => { event.preventDefault(); closeCard(); });
  dialog.addEventListener('click', (event) => {
    if (event.target === dialog) closeCard();
  });

  // PDF: طباعة البطاقة وحدها بمقاس البطاقة
  dialog.querySelector('[data-idcard-pdf]').addEventListener('click', () => {
    const page = document.createElement('style');
    page.textContent = '@page { size: 160mm 112mm; margin: 5mm; }';
    document.head.appendChild(page);
    document.body.classList.add('printing-idcard');
    const done = () => { document.body.classList.remove('printing-idcard'); page.remove(); };
    window.addEventListener('afterprint', done, { once: true });
    window.print();
    setTimeout(done, 1500);
  });

  dialog.querySelector('[data-idcard-share]').addEventListener('click', () => share(true));

  // صورة: تُرسم البطاقة على لوحة بنفس التصميم
  dialog.querySelector('[data-idcard-image]').addEventListener('click', async () => {
    try {
      const canvas = await drawCard();
      const link = document.createElement('a');
      link.href = canvas.toDataURL('image/png');
      link.download = 'injazat-card.png';
      link.click();
      say('حُفظت صورة البطاقة.');
    } catch (error) {
      say('تعذر حفظ الصورة.');
    }
  });

  async function drawCard() {
    await document.fonts.ready;
    // نفس تخطيط البطاقة: الوحدة u = ١٪ من العرض (مثل cqw في CSS)
    const W = 1600, u = W / 100, F = '"Cairo", Tahoma, sans-serif';
    const [photo, logo, mark] = await Promise.all([
      loadImage(data.photo), loadImage(data.logo).catch(() => null), loadImage(data.mark).catch(() => null)
    ]);
    // الارتفاع بنفس نسبة البطاقة (offset لا يتأثر بحركة الانبثاق)
    const H = Math.round(W * ((sheet.offsetHeight / sheet.offsetWidth) || .64));
    const canvas = document.createElement('canvas');
    canvas.width = W; canvas.height = H;
    const ctx = canvas.getContext('2d');
    const pad = 4.5 * u;
    // مواضع العناصر كما هي في البطاقة المعروضة
    const k = W / sheet.offsetWidth;
    const at = (sel) => { const n = sheet.querySelector(sel); return n ? n.offsetTop * k : 0; };

    // الخلفية
    roundRect(ctx, 0, 0, W, H, 5.6 * u); ctx.clip();
    const bg = ctx.createLinearGradient(0, 0, W, H);
    bg.addColorStop(0, '#0C7F89'); bg.addColorStop(.55, '#075A62'); bg.addColorStop(1, '#063135');
    ctx.fillStyle = bg; ctx.fillRect(0, 0, W, H);
    const glow = ctx.createRadialGradient(W * .84, H * .45, 0, W * .84, H * .45, W * .45);
    glow.addColorStop(0, 'rgba(217,185,119,.30)'); glow.addColorStop(1, 'rgba(217,185,119,0)');
    ctx.fillStyle = glow; ctx.fillRect(0, 0, W, H);
    if (mark) { ctx.globalAlpha = .07; const mw = W * .62, mh = mw * mark.height / mark.width; ctx.drawImage(mark, -W * .05, (H - mh) / 2, mw, mh); ctx.globalAlpha = 1; }

    // الرأس
    const topY = 3.4 * u;
    if (logo) { const lh = 8.5 * u, lw = lh * logo.width / logo.height; ctx.drawImage(logo, W - pad - lw, topY, lw, lh); }
    ctx.direction = 'rtl'; ctx.textAlign = 'left';
    ctx.fillStyle = '#F0D9A5'; ctx.font = '700 ' + 3.2 * u + 'px ' + F; ctx.fillText('بطاقة منجز', pad, topY + 4 * u);
    ctx.direction = 'ltr'; ctx.fillStyle = 'rgba(255,255,255,.55)'; ctx.font = '400 ' + 2.1 * u + 'px ' + F;
    ctx.letterSpacing = 0.5 * u + 'px'; ctx.fillText('ACHIEVER ID', pad, topY + 7.2 * u); ctx.letterSpacing = '0px';

    // الصورة داخل قوس (يمينًا)
    const pw = 27 * u, ph = 31 * u, px = W - pad - pw, py = at('.idc-photo'), br = 3.5 * u;
    archPath(ctx, px - 1.4 * u, py - 1.4 * u, pw + 2.8 * u, ph + 2.8 * u, pw / 2 + 1.4 * u, br + 1.4 * u);
    ctx.fillStyle = 'rgba(240,217,165,.10)'; ctx.fill();
    archPath(ctx, px, py, pw, ph, pw / 2, br);
    const pg = ctx.createRadialGradient(px + pw / 2, py + ph * .4, 10, px + pw / 2, py + ph * .4, pw * .72);
    pg.addColorStop(0, '#2c8a86'); pg.addColorStop(1, '#0a4c50');
    ctx.fillStyle = pg; ctx.fill();
    ctx.save(); archPath(ctx, px, py, pw, ph, pw / 2, br); ctx.clip();
    const sc = Math.max(pw / photo.width, ph / photo.height), dw = photo.width * sc, dh = photo.height * sc;
    ctx.drawImage(photo, px + (pw - dw) / 2, py + (ph - dh) * .08, dw, dh); ctx.restore();
    archPath(ctx, px, py, pw, ph, pw / 2, br); ctx.strokeStyle = 'rgba(240,217,165,.8)'; ctx.lineWidth = .4 * u; ctx.stroke();
    drawStar(ctx, px + pw / 2, py + ph, 3.3 * u);

    // الاسم والمجال والنبذة (يسار الصورة)
    const tx = px - 4.5 * u, tw = tx - pad;
    ctx.direction = 'rtl'; ctx.textAlign = 'right';
    let y = at('#idcard-name') + 5.4 * u;
    ctx.fillStyle = '#fff'; ctx.font = '800 ' + 5.4 * u + 'px ' + F; ctx.fillText(name, tx, y);
    if (data.name_en) { ctx.direction = 'ltr'; ctx.fillStyle = 'rgba(255,255,255,.7)'; ctx.font = '400 ' + 2.8 * u + 'px ' + F; ctx.fillText(data.name_en, tx, (y += 4.4 * u)); ctx.direction = 'rtl'; }
    ctx.font = '700 ' + 2.9 * u + 'px ' + F;
    const fw = ctx.measureText(data.field).width + 6.8 * u, fh = 5 * u, fy = y + 1.8 * u;
    const fg = ctx.createLinearGradient(tx - fw, 0, tx, 0);
    fg.addColorStop(0, '#C9A35C'); fg.addColorStop(1, '#F4DFAE');
    roundRect(ctx, tx - fw, fy, fw, fh, fh / 2); ctx.fillStyle = fg; ctx.fill();
    ctx.fillStyle = '#2F230C'; ctx.textAlign = 'center'; ctx.fillText(data.field, tx - fw / 2, fy + fh * .7);
    ctx.textAlign = 'right'; ctx.fillStyle = '#D7EAE7'; ctx.font = '400 ' + 2.75 * u + 'px ' + F;
    wrapText(ctx, bio, tx, fy + fh + 5 * u, tw, 5 * u, 3);

    // شريط الأرقام
    const sy = at('.idc-stats'), sh = sheet.querySelector('.idc-stats').offsetHeight * k, sw = W - pad * 2;
    roundRect(ctx, pad, sy, sw, sh, 3 * u); ctx.fillStyle = 'rgba(255,255,255,.08)'; ctx.fill();
    ctx.strokeStyle = 'rgba(255,255,255,.12)'; ctx.lineWidth = 2; ctx.stroke();
    const cw = sw / 3;
    data.stats.forEach((s, i) => {
      const cx = W - pad - cw * i - cw / 2;
      if (i) { ctx.fillStyle = 'rgba(255,255,255,.12)'; ctx.fillRect(W - pad - cw * i, sy + 1.6 * u, 2, sh - 3.2 * u); }
      ctx.font = '800 ' + 5.4 * u + 'px ' + F; const nw = ctx.measureText(ar(s.value)).width;
      ctx.font = '400 ' + 2.8 * u + 'px ' + F; const lw = ctx.measureText(s.label).width;
      const total = nw + 1.6 * u + lw, start = cx + total / 2;
      ctx.textAlign = 'right';
      ctx.fillStyle = '#F0D9A5'; ctx.font = '800 ' + 5.4 * u + 'px ' + F; ctx.fillText(ar(s.value), start, sy + sh * .72);
      ctx.fillStyle = '#CFE3E1'; ctx.font = '400 ' + 2.8 * u + 'px ' + F; ctx.fillText(s.label, start - nw - 1.6 * u, sy + sh * .68);
    });

    // التذييل
    ctx.textAlign = 'center'; ctx.fillStyle = 'rgba(255,255,255,.75)'; ctx.font = '400 ' + 2.5 * u + 'px ' + F;
    ctx.fillText(data.site + '   ✦   ' + SLOGAN, W / 2, H - 3.6 * u);
    const st = ctx.createLinearGradient(0, 0, W, 0);
    ['#9C7638', '#F4DFAE', '#C9A35C', '#F4DFAE', '#9C7638'].forEach((c, i) => st.addColorStop(i / 4, c));
    ctx.fillStyle = st; ctx.fillRect(0, H - 1.1 * u, W, 1.1 * u);
    return canvas;
  }

  function loadImage(src) {
    return new Promise((resolve, reject) => {
      const image = new Image();
      image.crossOrigin = 'anonymous'; // OneBox images (CORS) so the canvas can be exported
      image.onload = () => resolve(image);
      image.onerror = reject;
      image.src = src;
    });
  }

  function roundRect(ctx, x, y, w, h, r) {
    ctx.beginPath();
    ctx.moveTo(x + r, y);
    ctx.arcTo(x + w, y, x + w, y + h, r);
    ctx.arcTo(x + w, y + h, x, y + h, r);
    ctx.arcTo(x, y + h, x, y, r);
    ctx.arcTo(x, y, x + w, y, r);
    ctx.closePath();
  }

  // قوس عُماني: نصف دائرة في الأعلى وزوايا مستديرة في الأسفل
  function archPath(ctx, x, y, w, h, top, bottom) {
    ctx.beginPath();
    ctx.moveTo(x, y + top);
    ctx.arc(x + w / 2, y + top, w / 2, Math.PI, 0);
    ctx.lineTo(x + w, y + h - bottom);
    ctx.arcTo(x + w, y + h, x + w - bottom, y + h, bottom);
    ctx.lineTo(x + bottom, y + h);
    ctx.arcTo(x, y + h, x, y + h - bottom, bottom);
    ctx.closePath();
  }

  function drawStar(ctx, cx, cy, r) {
    ctx.beginPath();
    for (let i = 0; i < 16; i++) {
      const rr = i % 2 ? r * .845 : r, a = (Math.PI / 8) * i - Math.PI / 2;
      ctx.lineTo(cx + rr * Math.cos(a), cy + rr * Math.sin(a));
    }
    ctx.closePath(); ctx.fillStyle = '#C6A15A'; ctx.fill();
    ctx.strokeStyle = '#F4DFAE'; ctx.lineWidth = r * .09; ctx.stroke();
    const k = r / 34;
    ctx.beginPath(); ctx.moveTo(cx - 12 * k, cy + 1 * k); ctx.lineTo(cx - 3 * k, cy + 10 * k); ctx.lineTo(cx + 13 * k, cy - 9 * k);
    ctx.strokeStyle = '#fff'; ctx.lineWidth = 6 * k; ctx.lineCap = 'round'; ctx.lineJoin = 'round'; ctx.stroke();
  }

  function wrapText(ctx, text, x, y, maxWidth, lineHeight, maxLines) {
    const words = text.split(' ');
    const lines = [];
    let line = '';
    words.forEach((word) => {
      const trial = line ? line + ' ' + word : word;
      if (ctx.measureText(trial).width > maxWidth && line) { lines.push(line); line = word; }
      else line = trial;
    });
    if (line) lines.push(line);
    if (lines.length > maxLines) {
      lines.length = maxLines;
      lines[maxLines - 1] = lines[maxLines - 1].replace(/\s*\S+$/, '') + '…';
    }
    lines.forEach((l, i) => ctx.fillText(l, x, y + i * lineHeight));
  }
})();
