// Ported from design «تصميم 9» js/aj-bento.js (kept as in the design): «إنجازاتي» tiles and reader from #aj-data (printed by the server).
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
  var size = function (a) { return a.featured === 1 ? 'xl' : ((a.details || '').length > 260 || (a.sources || []).length ? 'wd' : 'sm'); };
  var W = { xl: 0, wd: 1, sm: 2 };
  var scope = 'all', revealed = false, cur = null, ov = null, lastFocus = null;

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
      '<span class="ab-y">' + AR(yr(a)) + '</span>' +
      '<span class="ab-k"><span class="ab-chip' + (a.rank === 'honor' || a.rank === 'milestone' ? '' : ' g') + '">' + R[a.rank] + '</span><span class="ab-chip">' + (a.scope === 'international' ? 'دولي' : 'محلي') + '</span></span>' +
      '<span class="ab-cap"><span class="ab-star">✦ الإنجاز الأبرز' + (a.stat ? ' · ' + esc(a.stat) : '') + '</span>' +
      '<span class="ab-ti">' + esc(s === 'sm' ? (a.short || a.title) : a.title) + '</span>' +
      '<span class="ab-sub">' + esc(a.org) + (a.country && a.country !== '—' ? ' · ' + esc(a.country) : '') + '</span>' +
      '<span class="ab-ex">' + esc((a.details || '').split('\n')[0]) + '</span>' +
      '<span class="ab-cta">اقرأ القصة <i>←</i></span></span></button>';
  }
  function grid() {
    var L = visible().slice().sort(function (a, b) { return W[size(a)] - W[size(b)] || (b.iso < a.iso ? -1 : 1); });
    return '<div class="ab-grid">' + (L.length ? L.map(tile).join('') : '<p class="ab-none">لا توجد إنجازات في هذا التصنيف.</p>') + '</div>';
  }
  function draw() { root.innerHTML = top() + rail() + grid(); }
  function reveal() { if (revealed) return; revealed = true; [].forEach.call(root.querySelectorAll('.ab-t'), function (t) { t.classList.add('in'); }); }
  draw();
  if ('IntersectionObserver' in window && !reduce) {
    var io = new IntersectionObserver(function (es) { if (es.some(function (e) { return e.isIntersecting; })) { reveal(); io.disconnect(); } }, { threshold: .12 });
    io.observe(root); setTimeout(reveal, 4000);
  } else reveal();

  /* ---------- نافذة القراءة ---------- */
  function body(t) {
    return (t || '').split('\n\n').map(function (p) {
      var ls = p.split('\n'), n = ls.filter(function (l) { return l.indexOf('•') !== 0; }), b = ls.filter(function (l) { return l.indexOf('•') === 0; });
      return (n.length ? '<p>' + esc(n.join(' ')) + '</p>' : '') + (b.length ? '<ul>' + b.map(function (l) { return '<li>' + esc(l.slice(1).trim()) + '</li>'; }).join('') + '</ul>' : '');
    }).join('');
  }
  function dlg(a, heroName) {
    var i = A.indexOf(a), prev = A[i + 1], next = A[i - 1]; /* A مرتبة من الأحدث؛ «التالي» = الأحدث زمنيًا */
    var sib = a.project ? A.filter(function (x) { return x.project === a.project && x !== a; }) : [];
    var nv = function (x, cls, lab, arrow) {
      return '<button type="button" class="ab-nv ' + cls + '"' + (x ? ' data-open="' + x.slug + '"' : ' disabled') + '>' + (cls === 'p' ? '<span class="ar">→</span>' : '') +
        '<span class="lb"><small>' + lab + '</small>' + (x ? '<b>' + esc(x.short || x.title) + '</b>' : '') + '</span>' + (cls === 'n' ? '<span class="ar">←</span>' : '') + '</button>';
    };
    return '<div class="ab-dlg ab" role="dialog" aria-modal="true" aria-label="' + esc(a.title) + '">' +
      '<button type="button" class="ab-x" data-close aria-label="إغلاق">✕</button>' +
      '<div class="ab-main"><div class="ab-media" style="view-transition-name:' + heroName + '"><img src="' + a.img + '" alt="" style="object-position:' + (a.pos || '50% 30%') + '">' +
      '<div class="ab-media-in"><span class="ab-my">' + AR(yr(a)) + '</span><div class="ab-mk"><span class="ab-chip g">' + R[a.rank] + '</span><span class="ab-chip">' + (a.scope === 'international' ? 'إنجاز دولي' : 'إنجاز محلي') + '</span>' + (a.stat ? '<span class="ab-chip">' + esc(a.stat) + '</span>' : '') + '</div>' +
      '<h3 class="ab-mt">' + esc(a.title) + '</h3></div></div>' +
      '<div class="ab-content" tabindex="-1"><dl class="ab-facts"><div><dt>التاريخ</dt><dd>' + AR(esc(a.date)) + '</dd></div><div><dt>الدولة</dt><dd>' + esc(a.country) + '</dd></div><div><dt>الجهة</dt><dd>' + esc(a.org) + '</dd></div><div><dt>' + (a.project ? 'العمل' : 'النطاق') + '</dt><dd>' + esc(a.project ? '«' + a.project + '»' : (a.scope === 'international' ? 'دولي' : 'محلي')) + '</dd></div></dl>' +
      (a.highlights && a.highlights.length ? '<ul class="ab-hl">' + a.highlights.map(function (h) { return '<li>' + h + '</li>'; }).join('') + '</ul>' : '') +
      '<div class="ab-txt">' + body(a.details) + '</div>' +
      (sib.length ? '<div class="ab-sec"><b>من إنجازات «' + esc(a.project) + '» أيضًا</b>' + sib.map(function (x) { return '<button type="button" class="ab-sb" data-open="' + x.slug + '"><em>' + AR(yr(x)) + '</em>' + esc(x.short || x.title) + '</button>'; }).join('') + '</div>' : '') +
      (a.sources && a.sources.length ? '<div class="ab-sec"><b>المصادر</b>' + a.sources.map(function (s) { return '<a class="s" href="' + esc(s.url) + '" target="_blank" rel="noopener">↗ ' + esc(s.org) + (s.title ? ': ' + esc(s.title) : '') + '</a>'; }).join('') + '</div>' : '') +
      '<div class="ab-links"><button type="button" data-share="' + a.href + '">مشاركة الإنجاز</button><a href="' + a.href + '">فتح في صفحة مستقلة</a></div></div></div>' +
      '<div class="ab-foot">' + nv(prev, 'p', 'الأقدم') + '<div class="ab-pg" aria-hidden="true">' + A.slice().reverse().map(function (x) { return '<button type="button" tabindex="-1" class="' + (x === a ? 'on' : '') + '" data-open="' + x.slug + '" title="' + esc(x.short || x.title) + '"></button>'; }).join('') + '</div>' + nv(next, 'n', 'الأحدث') + '</div></div>';
  }
  var tileOf = function (slug) { return root.querySelector('[data-tile="' + slug + '"]'); };
  function open(slug, animate) {
    var a = bySlug[slug]; if (!a) return;
    if (!ov) {
      lastFocus = document.activeElement;
      var t = tileOf(slug), name = t && animate ? 'ab-' + slug : 'ab-hero';
      var mount = function () {
        if (t && animate) t.style.viewTransitionName = 'none';
        ov = document.createElement('div'); ov.className = 'ab-ov'; ov.innerHTML = dlg(a, name);
        document.body.appendChild(ov); document.documentElement.style.overflow = 'hidden';
      };
      var tr = animate ? vt(mount) : (mount(), null);
      var settle = function () { var m = ov && ov.querySelector('.ab-media'); if (m) m.style.viewTransitionName = 'ab-hero'; if (t) t.style.viewTransitionName = 'ab-' + slug; };
      if (tr && tr.finished) tr.finished.then(settle, settle); else settle();
    } else {
      vt(function () { ov.innerHTML = dlg(a, 'ab-hero'); });
    }
    cur = slug;
    history.replaceState(null, '', '#ach-' + slug);
    setTimeout(function () { var x = ov && ov.querySelector('.ab-x'); x && x.focus({ preventScroll: true }); }, 30);
  }
  function close() {
    if (!ov) return;
    var t = tileOf(cur), m = ov.querySelector('.ab-media'), slug = cur;
    if (t && m && VT) { t.style.viewTransitionName = 'none'; m.style.viewTransitionName = 'ab-' + slug; }
    var o = ov; ov = null; o.classList.add('out'); setTimeout(function () { o.remove(); if (!ov) document.documentElement.style.overflow = ''; }, reduce ? 0 : 320);
    cur = null;
    history.replaceState(null, '', location.pathname + location.search);
    if (lastFocus && lastFocus.focus) lastFocus.focus({ preventScroll: true });
  }
  function step(d) { var i = A.indexOf(bySlug[cur]) - d; if (A[i]) open(A[i].slug); } /* d=+1 الأحدث */

  /* ---------- الأحداث ---------- */
  document.addEventListener('click', function (e) {
    if (ov && e.target === ov) return close();
    var t = e.target.closest('[data-open],[data-close],[data-scope],[data-share]'); if (!t) return;
    if (!root.contains(t) && !(ov && ov.contains(t))) return;
    if (t.hasAttribute('data-close')) return close();
    if (t.dataset.scope) { scope = t.dataset.scope; vt(draw); return; }
    if (t.dataset.share) { var u = new URL(t.dataset.share, location.href).href;
      if (navigator.share) navigator.share({ url: u }).catch(function () {}); else if (navigator.clipboard) navigator.clipboard.writeText(u).then(function () { t.textContent = 'نُسخ الرابط ✓'; }); return; }
    if (t.dataset.open) open(t.dataset.open, !!t.dataset.tile);
  });
  document.addEventListener('keydown', function (e) {
    if (!ov) return;
    if (e.key === 'Escape') close();
    else if (e.key === 'ArrowLeft') step(1);
    else if (e.key === 'ArrowRight') step(-1);
    else if (e.key === 'Tab') { /* حصر التنقل داخل النافذة */
      var f = ov.querySelectorAll('button:not([disabled]):not([tabindex="-1"]),a[href]'); if (!f.length) return;
      var first = f[0], last = f[f.length - 1];
      if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
      else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
    }
  });
  var tx = 0, ty = 0;
  document.addEventListener('touchstart', function (e) { if (ov) { tx = e.touches[0].clientX; ty = e.touches[0].clientY; } }, { passive: true });
  document.addEventListener('touchend', function (e) {
    if (!ov) return; var dx = e.changedTouches[0].clientX - tx, dy = e.changedTouches[0].clientY - ty;
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
  var h = location.hash.replace('#ach-', ''); if (bySlug[h]) { revealed = true; draw(); open(h, false); }
})();
