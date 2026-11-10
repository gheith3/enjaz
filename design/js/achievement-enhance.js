/* صفحة الإنجاز — تحسينات عامة لكل إنجاز:
   مدة القراءة وشريط التقدّم، موقع الإنجاز في المسيرة (السابق/التالي)،
   الهيدر المصغّر بصورة المنجز، زر مشاركة واحد، وبطاقة الإنجاز (صورة / PDF / مشاركة). */
(function () {
  var $ = function (s, r) { return (r || document).querySelector(s); };
  var $$ = function (s, r) { return [].slice.call((r || document).querySelectorAll(s)); };
  var ar = function (v) { return String(v).replace(/\d/g, function (d) { return '٠١٢٣٤٥٦٧٨٩'[d]; }); };
  var esc = function (v) { return String(v || '').replace(/[&<>"]/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]; }); };
  var reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
  var title = ($('#achievement-title') || {}).textContent || document.title;

  /* ---------- مدة القراءة + شريط التقدّم ---------- */
  var article = $('[data-article]');
  var rt = $('[data-read-time]');
  if (article && rt) {
    var words = article.textContent.trim().split(/\s+/).length;
    var mins = Math.max(1, Math.round(words / 180));
    rt.textContent = '⏱ ' + (mins === 1 ? 'دقيقة واحدة' : mins === 2 ? 'دقيقتان' : ar(mins) + (mins <= 10 ? ' دقائق' : ' دقيقة')) + ' قراءة';
    rt.hidden = false;
  }
  var bar = $('[data-read-progress]');
  if (bar && article) {
    var tick = function () {
      var r = article.getBoundingClientRect(), vh = window.innerHeight;
      var p = Math.min(1, Math.max(0, (vh * .35 - r.top) / (r.height - vh * .35)));
      bar.style.transform = 'scaleX(' + p.toFixed(3) + ')';
    };
    window.addEventListener('scroll', tick, { passive: true });
    window.addEventListener('resize', tick);
    tick();
  }

  /* ---------- موقع الإنجاز في المسيرة ---------- */
  var jNode = $('#journey-data'), path = $('[data-path]'), pnav = $('[data-path-nav]');
  if (jNode && path) {
    try {
      var J = JSON.parse(jNode.textContent), items = J.items, cur = 0;
      items.forEach(function (it, i) { if (it.slug === J.current) cur = i; });
      var link = function (it) { return it.href || (J.profile + '#ach-' + it.slug); };
      path.style.setProperty('--at', (items.length > 1 ? cur / (items.length - 1) : 0).toFixed(3));
      path.innerHTML = '<span class="path-line" aria-hidden="true"><i></i></span><ol>' + items.map(function (it, i) {
        var here = i === cur;
        return '<li class="' + it.scope + (here ? ' here' : '') + '">'
          + (here ? '<span class="path-dot" aria-current="step"></span>' : '<a class="path-dot" href="' + link(it) + '" aria-label="' + esc(it.title) + ' — ' + ar(it.year) + '"></a>')
          + '<small>' + ar(it.year) + '</small></li>';
      }).join('') + '</ol>';
      if (pnav) {
        var prev = items[cur - 1], next = items[cur + 1];
        pnav.innerHTML = (prev ? '<a class="pn prev" href="' + link(prev) + '"><small>→ الإنجاز السابق · ' + ar(prev.year) + '</small><b>' + esc(prev.title) + '</b></a>' : '<span></span>')
          + (next ? '<a class="pn next" href="' + link(next) + '"><small>الإنجاز التالي · ' + ar(next.year) + ' ←</small><b>' + esc(next.title) + '</b></a>' : '<span></span>');
      }
    } catch (e) {}
  }

  /* ---------- هيدر مصغّر بصورة المنجز ---------- */
  var header = $('.site-header'), hero = $('.story-hero'), crumb = $('.crumb'), author = $('[data-avatar]');
  if (header && hero && crumb && author && 'IntersectionObserver' in window) {
    var av = document.createElement('span');
    av.className = 'crumb-avatar'; av.setAttribute('aria-hidden', 'true');
    var avImg = document.createElement('img'); avImg.src = author.dataset.avatar; avImg.alt = '';
    av.appendChild(avImg);
    crumb.parentNode.insertBefore(av, crumb);
    new IntersectionObserver(function (es) {
      header.classList.toggle('is-compact', !es[0].isIntersecting);
    }, { rootMargin: '-64px 0px 0px 0px' }).observe(hero);
  }

  /* ---------- زر مشاركة واحد ---------- */
  var sheet = $('#share-sheet'), fab = $('[data-share-open]');
  var open = function (d) { if (d.showModal) d.showModal(); else d.setAttribute('open', ''); };
  if (sheet && fab) {
    fab.addEventListener('click', function () { open(sheet); });
    $('[data-share-close]', sheet).addEventListener('click', function () { sheet.close(); });
    sheet.addEventListener('click', function (e) { if (e.target === sheet) sheet.close(); });
    var copyBtn = $('[data-copy-link]', sheet), copyLabel = $('[data-copy-label]', sheet);
    copyBtn.addEventListener('click', function () {
      var url = location.href.split('#')[0];
      var done = function () { copyLabel.textContent = 'تم النسخ ✓'; setTimeout(function () { copyLabel.textContent = 'نسخ الرابط'; }, 1600); };
      if (navigator.clipboard) navigator.clipboard.writeText(url).then(done, function () { prompt('انسخ الرابط:', url); });
      else prompt('انسخ الرابط:', url);
    });
    // إخفاء الزر العائم فوق التذييل
    var foot = $('.site-footer');
    if (foot && 'IntersectionObserver' in window) {
      new IntersectionObserver(function (es) { fab.classList.toggle('away', es[0].isIntersecting); }).observe(foot);
    }
  }

  /* ---------- بطاقة الإنجاز ---------- */
  var dlg = $('#acard'), card = $('[data-acard-sheet]');
  if (!dlg || !card) return;
  var note = $('[data-acard-note]', dlg), canvas = null;
  var say = function (t) { note.hidden = false; note.textContent = t; };
  var ds = card.dataset;

  function loadImage(src) {
    return new Promise(function (res, rej) { var i = new Image(); i.onload = function () { res(i); }; i.onerror = rej; i.src = src; });
  }
  function rr(ctx, x, y, w, h, r) {
    ctx.beginPath(); ctx.moveTo(x + r, y);
    ctx.arcTo(x + w, y, x + w, y + h, r); ctx.arcTo(x + w, y + h, x, y + h, r);
    ctx.arcTo(x, y + h, x, y, r); ctx.arcTo(x, y, x + w, y, r); ctx.closePath();
  }
  function lines(ctx, text, max) {
    var out = [], line = '';
    text.split(' ').forEach(function (w) {
      var t = line ? line + ' ' + w : w;
      if (ctx.measureText(t).width > max && line) { out.push(line); line = w; } else line = t;
    });
    if (line) out.push(line);
    return out;
  }

  async function draw() {
    await document.fonts.ready;
    var W = 1080, H = 1350, F = '"Cairo", Tahoma, sans-serif';
    var imgs = await Promise.all([loadImage(ds.photo), loadImage(ds.avatar).catch(function () { return null; }),
      loadImage('assets/svg/logo.svg').catch(function () { return null; }), loadImage('assets/svg/watermark.svg').catch(function () { return null; })]);
    var photo = imgs[0], avatar = imgs[1], logo = imgs[2], mark = imgs[3];
    var c = document.createElement('canvas'); c.width = W; c.height = H;
    var ctx = c.getContext('2d');
    rr(ctx, 0, 0, W, H, 56); ctx.clip();
    var bg = ctx.createLinearGradient(0, 0, W * .6, H);
    bg.addColorStop(0, '#0C7F89'); bg.addColorStop(.55, '#075A62'); bg.addColorStop(1, '#063135');
    ctx.fillStyle = bg; ctx.fillRect(0, 0, W, H);

    // صورة الإنجاز في الأعلى تذوب في الخلفية (قناع شفافية بلا حدّ ظاهر)
    var ph = 760, sc = Math.max(W / photo.width, ph / photo.height);
    var pc = document.createElement('canvas'); pc.width = W; pc.height = ph;
    var px = pc.getContext('2d');
    px.drawImage(photo, (W - photo.width * sc) / 2, (ph - photo.height * sc) * .3, photo.width * sc, photo.height * sc);
    px.globalCompositeOperation = 'destination-in';
    var mask = px.createLinearGradient(0, 0, 0, ph);
    mask.addColorStop(0, 'rgba(0,0,0,1)'); mask.addColorStop(.55, 'rgba(0,0,0,1)'); mask.addColorStop(1, 'rgba(0,0,0,0)');
    px.fillStyle = mask; px.fillRect(0, 0, W, ph);
    ctx.drawImage(pc, 0, 0);
    var top = ctx.createLinearGradient(0, 0, 0, 220);
    top.addColorStop(0, 'rgba(4,38,42,.75)'); top.addColorStop(1, 'rgba(4,38,42,0)');
    ctx.fillStyle = top; ctx.fillRect(0, 0, W, 220);
    if (mark) { ctx.globalAlpha = .06; var mw = W * 1.1, mh = mw * mark.height / mark.width; ctx.drawImage(mark, (W - mw) / 2, H - mh * .8, mw, mh); ctx.globalAlpha = 1; }

    // الرأس
    if (logo) { var lh = 120, lw = lh * logo.width / logo.height; ctx.drawImage(logo, W - 60 - lw, 44, lw, lh); }
    ctx.direction = 'rtl'; ctx.textAlign = 'left';
    ctx.fillStyle = '#F0D9A5'; ctx.font = '700 36px ' + F; ctx.fillText('بطاقة إنجاز', 60, 100);
    ctx.direction = 'ltr'; ctx.fillStyle = 'rgba(255,255,255,.6)'; ctx.font = '400 22px ' + F; ctx.letterSpacing = '6px';
    ctx.fillText('ACHIEVEMENT', 60, 138); ctx.letterSpacing = '0px';

    // النطاق والسنة
    ctx.direction = 'rtl'; ctx.textAlign = 'right';
    var y = 760; ctx.font = '700 32px ' + F;
    var pill = ds.scope + '  ·  ' + ds.year, pw = ctx.measureText(pill).width + 64;
    var g = ctx.createLinearGradient(W - 60 - pw, 0, W - 60, 0); g.addColorStop(0, '#C9A35C'); g.addColorStop(1, '#F4DFAE');
    rr(ctx, W - 60 - pw, y, pw, 60, 30); ctx.fillStyle = g; ctx.fill();
    ctx.fillStyle = '#2F230C'; ctx.fillText(pill, W - 92, y + 42);

    // العنوان
    ctx.fillStyle = '#fff'; ctx.font = '800 66px ' + F;
    var tl = lines(ctx, title.trim(), W - 120).slice(0, 3);
    tl.forEach(function (l, i) { ctx.fillText(l, W - 60, 910 + i * 92); });
    y = 910 + tl.length * 92;

    // المنجز
    var cy = Math.min(H - 190, y + 40), r = 46, cx = W - 60 - r;
    ctx.save(); ctx.beginPath(); ctx.arc(cx, cy, r, 0, Math.PI * 2);
    var cg = ctx.createRadialGradient(cx, cy - 15, 5, cx, cy, r); cg.addColorStop(0, 'rgba(240,217,165,.5)'); cg.addColorStop(1, 'rgba(6,49,53,.7)');
    ctx.fillStyle = cg; ctx.fill(); ctx.restore();
    if (avatar) {
      // الصورة تجلس على أسفل الدائرة ويخرج رأسها من أعلاها (مثل الهيدر)
      var aw = r * 4.84, ah = aw * avatar.height / avatar.width;
      ctx.save(); ctx.beginPath();
      ctx.rect(cx - r, cy - r * 2.2, r * 2, r * 2.2); ctx.moveTo(cx + r, cy); ctx.arc(cx, cy, r, 0, Math.PI);
      ctx.clip();
      ctx.drawImage(avatar, cx - aw * .617, cy + r - ah * .53, aw, ah);
      ctx.restore();
    }
    ctx.beginPath(); ctx.arc(cx, cy, r, 0, Math.PI * 2); ctx.strokeStyle = '#D9B977'; ctx.lineWidth = 3; ctx.stroke();
    ctx.textAlign = 'right'; ctx.fillStyle = '#F0D9A5'; ctx.font = '700 40px ' + F; ctx.fillText(ds.name, cx - r - 24, cy - 2);
    ctx.fillStyle = '#CFE3E1'; ctx.font = '400 28px ' + F; ctx.fillText(ds.field, cx - r - 24, cy + 40);

    // التذييل
    ctx.textAlign = 'center'; ctx.fillStyle = 'rgba(255,255,255,.75)'; ctx.font = '400 28px ' + F;
    ctx.fillText('إنجازات شباب عُمان   ✦   نحن إنجازات هذا الوطن', W / 2, H - 52);
    var st = ctx.createLinearGradient(0, 0, W, 0);
    ['#9C7638', '#F4DFAE', '#C9A35C', '#F4DFAE', '#9C7638'].forEach(function (col, i) { st.addColorStop(i / 4, col); });
    ctx.fillStyle = st; ctx.fillRect(0, H - 14, W, 14);
    return c;
  }

  async function ensure() {
    if (canvas) return canvas;
    canvas = await draw();
    card.innerHTML = '<img src="' + canvas.toDataURL('image/png') + '" alt="بطاقة الإنجاز: ' + esc(title) + '"><i class="idc-shine" aria-hidden="true"></i>';
    return canvas;
  }
  function closeCard() {
    if (!dlg.open) return;
    if (reduce) { dlg.close(); return; }
    dlg.classList.add('is-closing');
    setTimeout(function () { dlg.close(); dlg.classList.remove('is-closing'); }, 220);
  }
  $$('[data-acard-open]').forEach(function (b) {
    b.addEventListener('click', async function () {
      if (sheet && sheet.open) sheet.close();
      note.hidden = true;
      try { await ensure(); } catch (e) { say('تعذر تجهيز البطاقة.'); }
      dlg.classList.remove('is-closing'); open(dlg);
    });
  });
  $('[data-acard-close]', dlg).addEventListener('click', closeCard);
  dlg.addEventListener('cancel', function (e) { e.preventDefault(); closeCard(); });
  dlg.addEventListener('click', function (e) { if (e.target === dlg) closeCard(); });

  $('[data-acard-image]', dlg).addEventListener('click', async function () {
    try {
      var c = await ensure(), a = document.createElement('a');
      a.href = c.toDataURL('image/png'); a.download = 'injazat-achievement.png'; a.click();
      say('حُفظت صورة البطاقة.');
    } catch (e) { say('تعذر حفظ الصورة.'); }
  });
  $('[data-acard-pdf]', dlg).addEventListener('click', function () {
    var page = document.createElement('style');
    page.textContent = '@page { size: 120mm 150mm; margin: 0; }';
    document.head.appendChild(page);
    document.body.classList.add('printing-acard');
    var done = function () { document.body.classList.remove('printing-acard'); page.remove(); };
    window.addEventListener('afterprint', done, { once: true });
    window.print();
    setTimeout(done, 1500);
  });
  $('[data-acard-share]', dlg).addEventListener('click', async function () {
    var url = location.href.split('#')[0];
    try {
      var c = await ensure();
      var blob = await new Promise(function (r) { c.toBlob(r, 'image/png'); });
      var file = new File([blob], 'injazat-achievement.png', { type: 'image/png' });
      if (navigator.canShare && navigator.canShare({ files: [file] })) await navigator.share({ files: [file], title: title, text: title + '\n' + url });
      else if (navigator.share) await navigator.share({ title: title, url: url });
      else { await navigator.clipboard.writeText(title + '\n' + url); say('نُسخ العنوان والرابط.'); }
    } catch (e) { if (!e || e.name !== 'AbortError') say('تعذرت المشاركة من هذا المتصفح.'); }
  });
})();
