// Ported from design «تصميم 9» js/aj-bento.js: «إنجازاتي» tiles from #aj-data (printed by the server). The reader pop-up is
// design 10 (approved: «أفكار-عرض-الإنجازات-3.html», Dr. Musallam Al-Araimi model); its styles are css/aj-reader.css.
/* «إنجازاتي» — تصميم ٤. البيانات من <script type="application/json" id="aj-data"> */
(function () {
  var root = document.querySelector('[data-aj]'); if (!root) return;
  root.classList.add('ab');
  var A = JSON.parse(document.getElementById('aj-data').textContent).items
    .slice().sort(function (a, b) { return b.iso < a.iso ? -1 : b.iso > a.iso ? 1 : 0; });
  var AR = function (v) { return String(v).replace(/\d/g, function (d) { return '٠١٢٣٤٥٦٧٨٩'[d]; }); };
  var esc = function (s) { return String(s == null ? '' : s).replace(/[&<>"]/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]; }); };
  var R = { gold: 'ميدالية ذهبية', silver: 'ميدالية فضية', bronze: 'ميدالية برونزية', first: 'المركز الأول', second: 'المركز الثاني', award: 'جائزة', honor: 'تكريم', milestone: 'محطة' };
  var reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
  var VT = false; /* تصميم ٩: حركة مخصصة بدل View Transitions */
  if (!VT) document.documentElement.classList.add('ab-novt');
  function vt(fn) { if (VT) return document.startViewTransition(fn); fn(); return null; }
  var bySlug = {}; A.forEach(function (a) { bySlug[a.slug] = a; });
  var yr = function (a) { return a.iso.slice(0, 4); };
  var featured = A.find(function (a) { return a.featured === 1; }) || A[0];
  var size = function (a) { return a === featured ? 'xl' : 'sm'; };
  var W = { xl: 0, wd: 1, sm: 2 };
  var scope = 'all', revealed = false;

  /* ---------- القسم ---------- */
  function visible() { return A.filter(function (a) { return scope === 'all' || a.scope === scope; }); }
  function top() {
    var n = { all: A.length, international: 0, local: 0 }, places = [];
    A.forEach(function (a) { n[a.scope]++; if (a.country && a.country !== '—' && places.indexOf(a.country) < 0) places.push(a.country); });
    var ys = A.map(yr).sort();
    var seg = [['all', 'الكل', ''], ['international', 'دولي', 'i'], ['local', 'محلي', 'l']].map(function (x) {
      return '<button type="button" role="tab" aria-selected="' + (scope === x[0]) + '" class="' + (scope === x[0] ? 'on' : '') + '" data-scope="' + x[0] + '">' +
        (scope === x[0] ? '<span class="ab-thumb"></span>' : '') + (x[2] ? '<span class="d ' + x[2] + '"></span>' : '') + x[1] + '<b>' + AR(n[x[0]]) + '</b></button>';
    }).join('');
    return '<div class="ab-top"><p class="ab-sum"><span><b>' + AR(A.length) + '</b>إنجازات</span><i>✦</i><span><b>' + AR(places.length) + '</b>دول</span><i>✦</i><span><b>' + AR(ys[0]) + ' – ' + AR(ys[ys.length - 1]) + '</b></span></p>' +
      '<div class="ab-seg" role="tablist" aria-label="تصفية الإنجازات">' + seg + '</div></div>';
  }
  function rail() {
    var t = function (a) { var p = a.iso.split('-'); return +p[0] + (p[1] ? (p[1] - 1) / 12 + (p[2] ? p[2] / 365 : 0) : .5); };
    var y0 = Math.min.apply(null, A.map(function (a) { return +yr(a); })), y1 = Math.max.apply(null, A.map(function (a) { return +yr(a); })) + 1;
    var pos = function (v) { return (2 + (v - y0) / (y1 - y0) * 96).toFixed(2); };
    var h = '<span class="ab-rail-line"></span>';
    var step = (y1 - y0) > 10 ? 2 : 1;
    for (var y = y0; y < y1; y += step) h += '<span class="ab-rail-y" style="inset-inline-start:' + pos(y) + '%">' + AR(y) + '</span>';
    A.forEach(function (a) {
      var off = scope !== 'all' && a.scope !== scope;
      h += '<button type="button" class="ab-dot ' + (a.scope === 'international' ? 'i' : 'l') + (a.featured === 1 ? ' star' : '') + (off ? ' off' : '') + '" style="inset-inline-start:' + pos(t(a)) + '%" data-open="' + a.slug + '" data-dot="' + a.slug + '" title="' + esc(a.short || a.title) + ' — ' + AR(yr(a)) + '" aria-label="' + esc(a.short || a.title) + ' — ' + AR(yr(a)) + '"' + (off ? ' tabindex="-1"' : '') + '></button>';
    });
    return '<div class="ab-rail" role="group" aria-label="الخط الزمني للإنجازات">' + h + '</div>';
  }
  function tile(a, i) {
    var s = size(a);
    return '<button type="button" class="ab-t ' + s + (revealed ? ' in now' : '') + '" data-open="' + a.slug + '" data-tile="' + a.slug + '" style="--i:' + i + ';view-transition-name:ab-' + a.slug + '" aria-label="' + esc(a.title) + ' — ' + AR(esc(a.date)) + '">' +
      '<img src="' + a.img + '" alt="" loading="lazy" style="object-position:' + (a.pos || '50% 30%') + '">' +
      '<span class="ab-cap"><span class="ab-meta"><span class="ab-y">' + AR(yr(a)) + '</span>' +
      '<span class="ab-k"><span class="ab-chip' + (a.rank === 'honor' || a.rank === 'milestone' ? '' : ' g') + '">' + R[a.rank] + '</span><span class="ab-chip">' + (a.scope === 'international' ? 'دولي' : 'محلي') + '</span></span></span>' +
      '<span class="ab-star">✦ الإنجاز الأبرز' + (a.stat ? ' · ' + esc(a.stat) : '') + '</span>' +
      '<span class="ab-ti">' + esc(s === 'sm' ? (a.short || a.title) : a.title) + '</span>' +
      '<span class="ab-sub">' + esc(a.org) + (a.country && a.country !== '—' ? ' · ' + esc(a.country) : '') + '</span>' +
      '<span class="ab-ex">' + esc((a.details || '').split('\n')[0]) + '</span>' +
      '<span class="ab-cta">اقرأ القصة <i>←</i></span></span></button>';
  }
  function grid() {
    var L = visible().slice().sort(function (a, b) { return W[size(a)] - W[size(b)] || (b.iso < a.iso ? -1 : 1); });
    return '<div class="ab-grid ab-grid-balanced">' + (L.length ? L.map(tile).join('') : '<p class="ab-none">لا توجد إنجازات في هذا التصنيف.</p>') + '</div>';
  }
  function draw() { root.innerHTML = top() + rail() + grid(); }
  function reveal() { if (revealed) return; revealed = true; [].forEach.call(root.querySelectorAll('.ab-t'), function (t) { t.classList.add('in'); }); }
  draw();
  if ('IntersectionObserver' in window && !reduce) {
    var io = new IntersectionObserver(function (es) { if (es.some(function (e) { return e.isIntersecting; })) { reveal(); io.disconnect(); } }, { threshold: .12 });
    io.observe(root); setTimeout(reveal, 4000);
  } else reveal();

  /* ---------- نافذة التفاصيل: تصميم ١٠ المعتمد (نموذج د. مسلم العريمي) ----------
     الصورة تنبثق من البطاقة نفسها (FLIP + قناع) إلى إطار ذهبي، والنص يمينًا: عنوان بسطرين،
     شريط التاريخ والدولة والجهة، فقرة افتتاحية بخط بني، ثم «تفاصيل الإنجاز». */
  var C = A.slice().reverse(); /* بالترتيب الزمني: «السابق» الأقدم و«التالي» الأحدث */
  var ICO = {
    d: '<svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/></svg>',
    c: '<svg viewBox="0 0 24 24"><path d="M12 21s-7-6.2-7-12a7 7 0 0 1 14 0c0 5.8-7 12-7 12z"/><circle cx="12" cy="9" r="2.5"/></svg>',
    o: '<svg viewBox="0 0 24 24"><path d="M3 21h18M5 21V10l7-5 7 5v11M9 21v-6h6v6"/></svg>'
  };
  /* الانبثاق والرجوع بمنحنى متوازن: انطلاق هادئ، ثم انسياب طويل حتى الاستقرار (بلا قفزة في البداية) */
  var DUR = 600, EASE = 'cubic-bezier(.2,0,0,1)', DUR_BACK = 520, EASE_BACK = 'cubic-bezier(.3,0,0,1)';
  var mv = document.createElement('div');
  mv.className = 'mv'; mv.inert = true; mv.setAttribute('aria-hidden', 'true');
  mv.innerHTML = '<div class="mv-bg" data-close></div><article class="mv-box" role="dialog" aria-modal="true" aria-labelledby="mv-title">' +
    '<div class="mv-media"><img class="mv-blur" alt=""><span class="pat"></span><span class="glow"></span><span class="ring"></span>' +
    '<div class="mv-frame"><img class="mv-img" alt=""></div><div class="mv-hd"></div><button type="button" class="mv-x" data-close aria-label="إغلاق">×</button></div>' +
    '<div class="mv-body"></div></article>';
  document.body.appendChild(mv);
  var box = mv.querySelector('.mv-box'), mimg = mv.querySelector('.mv-img'), mblur = mv.querySelector('.mv-blur'),
    mhd = mv.querySelector('.mv-hd'), mb = mv.querySelector('.mv-body'), curA = null, srcTile = null, busy = false, lastFocus = null;

  /* النص كما هو دون حذف: تقسيم عند نهايات الجمل، وإبراز العبارات المعلَّمة في القصة */
  function sentences(t) {
    var out = [];
    t.replace(/([.؟!])\s+/g, '$1\u0000').split('\u0000').forEach(function (x) {
      x = x.trim(); if (!x) return;
      var last = out[out.length - 1];
      if (last && (last.length < 30 || /(^|\s)[ء-ي]\.$/.test(last))) out[out.length - 1] = last + ' ' + x; else out.push(x);
    });
    return out;
  }
  function marks(a, t, tag) {
    var keep = [];
    t = esc(t);
    (a.bold || []).slice().sort(function (x, y) { return y.length - x.length; }).forEach(function (h) {
      var e = esc(h); if (t.indexOf(e) < 0) return;
      t = t.split(e).join('\u0001' + keep.length + '\u0002'); keep.push(e);
    });
    return t.replace(/\u0001(\d+)\u0002/g, function (m, i) { return tag === 'kb' ? '<b class="kb">' + keep[i] + '</b>' : '<strong>' + keep[i] + '</strong>'; });
  }
  function article(a) {
    var blocks = [];
    (a.details || '').split('\n\n').forEach(function (p) {
      var ls = p.split('\n'), txt = ls.filter(function (l) { return l.indexOf('•') !== 0; }).join(' ').trim(), li = ls.filter(function (l) { return l.indexOf('•') === 0; });
      if (txt) blocks.push({ t: txt });
      if (li.length) blocks.push({ ul: li.map(function (l) { return l.slice(1).trim(); }) });
    });
    if (!blocks.length) return '';
    /* فقرة طويلة تُعرض فقرات قصيرة عند نهايات الجمل (حتى نحو ٣٤٠ حرفًا)، كما في صفحة الإنجاز (App\Support\Paragraphs) */
    var chunks = function (t) {
      var out = [], cur = '';
      sentences(t).forEach(function (x) { if (cur.length >= 120 && cur.length + x.length > 340) { out.push(cur); cur = ''; } cur = cur ? cur + ' ' + x : x; });
      if (cur) { if (out.length && cur.length < 90) out[out.length - 1] += ' ' + cur; else out.push(cur); }
      return out;
    };
    var first = blocks[0].t ? sentences(blocks[0].t) : [], rest = blocks.slice(1), body = [];
    if (first.length) {
      var after = first.slice(1).join(' ');
      if (after) chunks(after).forEach(function (t) { body.push({ t: t }); });
    } else rest = blocks;
    rest.forEach(function (b) { if (b.t && b.t.length > 420) chunks(b.t).forEach(function (t) { body.push({ t: t }); }); else body.push(b); });
    var out = first.length ? '<p class="lead">' + marks(a, first[0]) + '</p>' : '';
    body.forEach(function (b, i) {
      if (i === 1) out += '<div class="sec">تفاصيل الإنجاز</div>';
      var tag = i ? 'kb' : '';
      out += b.ul ? '<ul>' + b.ul.map(function (x) { return '<li>' + marks(a, x, tag) + '</li>'; }).join('') + '</ul>' : '<p>' + marks(a, b.t, tag) + '</p>';
    });
    return out;
  }
  function fill(a) {
    curA = a;
    mimg.src = a.img; mblur.src = a.img; mimg.style.objectPosition = a.pos || '';
    box.setAttribute('aria-label', a.title);
    var intl = a.scope === 'international', place = a.country && a.country !== '—' ? a.country : '';
    mhd.innerHTML = '<span class="mv-yr">' + AR(yr(a)) + '</span><span class="lbl">' + (intl ? 'إنجاز دولي' : 'إنجاز محلي') + (place ? ' · ' + esc(place) : '') + '</span>';
    var row = function (ic, k, v) { return '<div class="mv-row"><span class="mv-ic">' + ICO[ic] + '</span><dl><dt>' + k + '</dt><dd>' + v + '</dd></dl></div>'; };
    var rows = [row('d', 'التاريخ', AR(esc(a.date)))];
    if (place) rows.push(row('c', 'الدولة', esc(place)));
    if (a.org) rows.push(row('o', 'الجهة', esc(a.org)));
    var words = (a.details || '').split(/\s+/).filter(Boolean).length, mins = Math.max(1, Math.round(words / 180)), i = C.indexOf(a);
    mb.innerHTML = '<div class="mv-prog"></div>' +
      '<div class="mv-top"><div class="k"><span class="g">' + (intl ? 'إنجاز دولي' : 'إنجاز محلي') + '</span>' + (R[a.rank] ? '<span>' + R[a.rank] + '</span>' : '') + (a.stat ? '<span>' + esc(a.stat) + '</span>' : '') + '</div>' +
      '<h3 id="mv-title">' + esc(a.title) + '</h3></div>' +
      '<div class="mv-strip" style="grid-template-columns:' + (rows.length > 1 ? 'repeat(' + (rows.length - 1) + ',max-content) ' : '') + 'minmax(0,1fr)">' + rows.join('') + '</div>' +
      '<div class="mv-art">' + article(a) + '<div class="mv-meta">' + AR(words) + ' كلمة · ' + AR(mins) + ' ' + (mins > 2 && mins < 11 ? 'دقائق' : 'دقيقة') + ' قراءة</div>' +
      '<div class="mv-links"><a class="mv-page" href="' + esc(a.href) + '">قراءة صفحة الإنجاز كاملة ←</a><button type="button" class="mv-share" data-share="' + esc(a.href) + '">مشاركة الإنجاز</button></div></div>' +
      '<div class="mv-nav"><button type="button" data-nav="-1">→ السابق</button><span>' + AR(i + 1) + ' / ' + AR(C.length) + '</span><button type="button" data-nav="1">التالي ←</button></div>';
    mb.scrollTop = 0; box.scrollTop = 0;
    warm(a);
  }
  function splitTitle() { /* عنوان بسطرين متوازنين دون حذف كلمة */
    var h = mb.querySelector('.mv-top h3'); if (!h) return;
    var w = h.textContent.trim().split(/\s+/); if (w.length < 3) return;
    var best = 1, bd = 1e9, tot = w.join(' ').length;
    for (var k = 1; k < w.length; k++) { var d = Math.abs(w.slice(0, k).join(' ').length - tot / 2); if (d < bd) { bd = d; best = k; } }
    h.innerHTML = '<span class="ln"><span>' + esc(w.slice(0, best).join(' ')) + '</span></span> <span class="ln"><span>' + esc(w.slice(best).join(' ')) + '</span></span>';
    var lines = [].slice.call(h.querySelectorAll('.ln>span')), fs = 28;
    var wide = function () { return lines.some(function (x) { return x.scrollWidth > h.clientWidth + 1; }); };
    lines.forEach(function (x) { x.style.whiteSpace = 'nowrap'; });
    h.style.fontSize = fs + 'px';
    while (fs > 19 && wide()) { fs--; h.style.fontSize = fs + 'px'; }
    if (wide()) lines.forEach(function (x) { x.style.whiteSpace = ''; });
  }
  function rect(el) { var r = el.getBoundingClientRect(); return { l: r.left, t: r.top, w: r.width, h: r.height }; }
  function frames(src, tgt, el) { /* النسخة بحجمها النهائي من البداية، تتحرك بالتحويلات فقط ويُفتح القناع تدريجيًا */
    var s = Math.max(src.w / tgt.w, src.h / tgt.h);
    var dx = (src.l + src.w / 2) - (tgt.l + tgt.w * s / 2), dy = (src.t + src.h / 2) - (tgt.t + tgt.h * s / 2);
    var vw = src.w / s, vh = src.h / s, ix = (tgt.w - vw) / 2, iy = (tgt.h - vh) / 2, r = (parseFloat(getComputedStyle(el).borderTopLeftRadius) || 0) / s;
    return [{ transform: 'translate(' + dx + 'px,' + dy + 'px) scale(' + s + ')', clipPath: 'inset(' + iy + 'px ' + ix + 'px round ' + r + 'px)' },
      { transform: 'translate(0px,0px) scale(1)', clipPath: 'inset(0px 0px round 20px)' }];
  }
  function flyer(tgt, a) {
    var f = document.createElement('div'); f.className = 'mv-fly';
    f.style.cssText = 'left:' + tgt.l + 'px;top:' + tgt.t + 'px;width:' + tgt.w + 'px;height:' + tgt.h + 'px;border-radius:20px;padding:5px;transform-origin:0 0';
    f.innerHTML = '<img src="' + esc(a.img) + '" alt="" style="border-radius:15px;object-position:' + (a.pos || '50% 30%') + '">';
    document.body.appendChild(f); return f;
  }
  var tileOf = function (slug) { return root.querySelector('[data-tile="' + slug + '"]'); };
  var onScreen = function (r) { return r.w > 0 && r.t < innerHeight && r.t + r.h > 0; };

  /* صفحة الإنجاز تُجهَّز في الخلفية بعد لحظة من فتح النافذة، فيفتحها الزر فورًا */
  var spec = null, specTimer = 0;
  function warm(a) {
    clearTimeout(specTimer);
    specTimer = setTimeout(function () {
      if (spec) spec.remove();
      var href = new URL(a.href, location.href).href;
      if (window.HTMLScriptElement && HTMLScriptElement.supports && HTMLScriptElement.supports('speculationrules')) {
        spec = document.createElement('script'); spec.type = 'speculationrules';
        spec.textContent = JSON.stringify({ prerender: [{ source: 'list', urls: [href] }] });
      } else { spec = document.createElement('link'); spec.rel = 'prefetch'; spec.href = href; }
      document.head.appendChild(spec);
    }, 700);
  }
  function cool() { clearTimeout(specTimer); if (spec) { spec.remove(); spec = null; } }

  function open(a, from) {
    if (!a || busy) return;
    if (mv.classList.contains('open')) return go(a);
    busy = true; lastFocus = document.activeElement;
    fill(a);
    box.getAnimations().forEach(function (x) { x.cancel(); });
    mv.inert = false; mv.removeAttribute('aria-hidden');
    mv.classList.remove('closing', 'leaving');
    mv.classList.add('open', 'mv-hold'); document.documentElement.classList.add('mv-lock');
    splitTitle();
    srcTile = from || null;
    var started = false, start = function () {
      if (started) return; started = true;
      var tgt = rect(mimg);
      box.animate([{ opacity: 0 }, { opacity: 1 }], { duration: 360, easing: 'ease-out' });
      if (from && !reduce && tgt.w > 0 && onScreen(rect(from))) {
        /* الصورة ترتفع من البطاقة نفسها: تظهر فوقها بسرعة، ثم تنساب إلى الإطار الذهبي */
        var fr = frames(rect(from), tgt, from), f = flyer(tgt, a);
        var an = f.animate([Object.assign({ opacity: 0 }, fr[0]), { opacity: 1, offset: .12 }, fr[1]], { duration: DUR, easing: EASE, fill: 'forwards' });
        setTimeout(function () { if (srcTile === from) from.style.visibility = 'hidden'; }, DUR * .12);
        setTimeout(function () { mv.classList.add('ready'); }, DUR * .35);
        var landed = false, land = function () {
          if (landed) return; landed = true;
          mv.classList.remove('mv-hold');
          requestAnimationFrame(function () { requestAnimationFrame(function () { f.remove(); }); });
          setTimeout(function () { f.remove(); }, 150); /* احتياط إن تأخرت الإطارات */
          busy = false;
        };
        an.onfinish = land; setTimeout(land, DUR + 150); /* احتياط إن لم يصل حدث النهاية (تبويب في الخلفية) */
      } else { mv.classList.remove('mv-hold'); mv.classList.add('ready'); busy = false; }
      mv.querySelector('.mv-x').focus({ preventScroll: true });
    };
    if (mimg.complete && mimg.naturalWidth) { requestAnimationFrame(start); setTimeout(start, 120); }
    else { /* لا ننتظر صورة بطيئة: بعد ٠٫٤ ثانية تظهر النافذة بالتلاشي، والصورة حين تصل */
      mimg.onload = mimg.onerror = function () { mimg.onload = mimg.onerror = null; requestAnimationFrame(start); setTimeout(start, 120); };
      setTimeout(start, 400);
    }
    history.replaceState(null, '', '#ach-' + a.slug);
  }
  function go(a) { /* التنقل داخل النافذة */
    if (busy || !a || a === curA) return;
    mv.classList.remove('ready');
    setTimeout(function () { fill(a); splitTitle(); mv.classList.add('ready'); history.replaceState(null, '', '#ach-' + a.slug); }, reduce ? 0 : 280);
  }
  function step(d) { var i = C.indexOf(curA); if (i > -1) go(C[(i + d + C.length) % C.length]); }
  function close() {
    if (busy || !mv.classList.contains('open')) return;
    busy = true; cool();
    var t = curA && tileOf(curA.slug), tgt = rect(mimg), tr = t && rect(t);
    var done = false, fin = function () {
      if (done) return; done = true;
      mv.classList.remove('open', 'mv-hold', 'closing', 'leaving'); box.getAnimations().forEach(function (x) { x.cancel(); });
      mv.inert = true; mv.setAttribute('aria-hidden', 'true'); document.documentElement.classList.remove('mv-lock');
      mimg.style.viewTransitionName = '';
      if (srcTile) srcTile.style.visibility = ''; if (t) t.style.visibility = '';
      busy = false; curA = null; srcTile = null;
      if (lastFocus && lastFocus.focus) lastFocus.focus({ preventScroll: true });
    };
    /* الخلفية الداكنة تنسحب مع رجوع الصورة، فتهبط الصورة على الصفحة وهي ظاهرة */
    mv.classList.remove('ready'); mv.classList.add('closing');
    history.replaceState(null, '', location.pathname + location.search);
    if (t && !reduce && tgt.w > 0 && onScreen(tr)) {
      var f = flyer(tgt, curA), fr = frames(tr, tgt, t);
      mv.classList.add('mv-hold');
      if (srcTile && srcTile !== t) srcTile.style.visibility = '';
      t.style.visibility = 'hidden';
      box.animate([{ opacity: 1 }, { opacity: 0 }], { duration: 220, easing: 'ease-out', fill: 'forwards' });
      var an = f.animate([fr[1], { opacity: 1, offset: .8 }, Object.assign({ opacity: 0 }, fr[0])], { duration: DUR_BACK, easing: EASE_BACK, fill: 'forwards' });
      setTimeout(function () { t.style.visibility = ''; }, DUR_BACK * .78); /* البطاقة تظهر تحت الصورة قبل أن تختفي */
      var back = function () { if (f.isConnected) f.remove(); fin(); };
      an.onfinish = back; setTimeout(back, DUR_BACK + 150);
    } else { box.animate([{ opacity: 1 }, { opacity: 0 }], { duration: reduce ? 1 : 260, easing: 'ease-out', fill: 'forwards' }).onfinish = fin; setTimeout(fin, 420); }
  }
  function progress(e) { var el = e.target, h = el.scrollHeight - el.clientHeight, g = mb.querySelector('.mv-prog'); if (g && h > 0) g.style.width = (el.scrollTop / h * 100) + '%'; }
  mb.addEventListener('scroll', progress); box.addEventListener('scroll', progress);
  /* العودة بزر الرجوع إلى النافذة نفسها (ذاكرة الصفحات) */
  addEventListener('pageshow', function (e) { if (e.persisted) mv.classList.remove('leaving'); });

  /* ---------- الأحداث ---------- */
  document.addEventListener('click', function (e) {
    var page = e.target.closest('.mv-page');
    if (page && mv.contains(page) && !(e.metaKey || e.ctrlKey || e.shiftKey || e.altKey)) {
      /* المتصفحات الحديثة تنقل الصورة نفسها إلى صورة صفحة الإنجاز (view transition)، والباقي يرى تلاشيًا ومؤشر تحميل */
      mimg.style.viewTransitionName = 'ach-photo'; mv.classList.add('leaving'); return;
    }
    var t = e.target.closest('[data-open],[data-close],[data-scope],[data-share],[data-nav]'); if (!t) return;
    var inMv = mv.contains(t); if (!root.contains(t) && !inMv) return;
    if (t.hasAttribute('data-close')) return close();
    if (t.dataset.nav) return step(+t.dataset.nav);
    if (t.dataset.scope) { scope = t.dataset.scope; vt(draw); return; }
    if (t.dataset.share) { var u = new URL(t.dataset.share, location.href).href;
      if (navigator.share) navigator.share({ url: u }).catch(function () {}); else if (navigator.clipboard) navigator.clipboard.writeText(u).then(function () { t.textContent = 'نُسخ الرابط ✓'; }); return; }
    if (t.dataset.open) open(bySlug[t.dataset.open], t.dataset.tile ? t : null);
  });
  document.addEventListener('keydown', function (e) {
    if (!mv.classList.contains('open')) return;
    if (e.key === 'Escape') close();
    else if (e.key === 'ArrowLeft') step(1);
    else if (e.key === 'ArrowRight') step(-1);
    else if (e.key === 'Tab') { /* حصر التنقل داخل النافذة */
      var f = box.querySelectorAll('button:not([disabled]),a[href]'); if (!f.length) return;
      var first = f[0], last = f[f.length - 1];
      if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
      else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
    }
  });
  var tx = 0, ty = 0;
  box.addEventListener('touchstart', function (e) { tx = e.touches[0].clientX; ty = e.touches[0].clientY; }, { passive: true });
  box.addEventListener('touchend', function (e) {
    var dx = e.changedTouches[0].clientX - tx, dy = e.changedTouches[0].clientY - ty;
    if (Math.abs(dx) > 70 && Math.abs(dx) > Math.abs(dy) * 1.8) step(dx > 0 ? 1 : -1);
  }, { passive: true });
  /* ربط النقطة بالبطاقة عند المرور */
  function hot(e, on) {
    var t = e.target.closest && e.target.closest('[data-tile],[data-dot]'); if (!t) return;
    var s = t.dataset.tile || t.dataset.dot, o = root.querySelector(t.dataset.tile ? '[data-dot="' + s + '"]' : '[data-tile="' + s + '"]');
    if (o) o.classList.toggle('hot', on);
  }
  root.addEventListener('mouseover', function (e) { hot(e, true); });
  root.addEventListener('mouseout', function (e) { hot(e, false); });
  root.addEventListener('focusin', function (e) { hot(e, true); });
  root.addEventListener('focusout', function (e) { hot(e, false); });
  /* ربط وسام الإحصاءات (دولي/محلي) بالتصفية */
  [].forEach.call(document.querySelectorAll('.ms-row[data-kind]'), function (b) {
    b.addEventListener('click', function () {
      var k = b.dataset.kind === 'intl' ? 'international' : 'local';
      scope = scope === k ? 'all' : k; vt(draw);
      root.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'start' });
    });
  });
  var h = location.hash.replace('#ach-', ''); if (bySlug[h]) { revealed = true; draw(); open(bySlug[h], null); }
})();
