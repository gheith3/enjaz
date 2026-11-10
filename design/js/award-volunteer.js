/* صفحة «جائزة السلطان قابوس للعمل التطوعي» (award-volunteer.html). البيانات من js/award-volunteer-data.js.
   الفكرة: موجات العطاء؛ كل دورة منصّتا تتويج (الأفراد / الجمعيات) بالمراكز الثلاثة، والضغط على أي فائز يفتح بطاقته. */
(function () {
  var V = window.INJAZ_VOL, root = document.querySelector('[data-vol]');
  if (!V || !root) return;
  var $ = function (s) { return root.querySelector(s); };
  var ar = function (n) { return String(n).replace(/\d/g, function (d) { return '٠١٢٣٤٥٦٧٨٩'[d]; }); };
  var esc = function (s) { var d = document.createElement('div'); d.textContent = s == null ? '' : s; return d.innerHTML; };
  var ORD = ['', 'الأولى', 'الثانية', 'الثالثة', 'الرابعة', 'الخامسة', 'السادسة', 'السابعة'];
  var RANK = ['', 'المركز الأول', 'المركز الثاني', 'المركز الثالث'];
  var ICON = {
    ind: '<circle cx="12" cy="7.5" r="3.5"/><path d="M5 20c.8-4 3.6-6 7-6s6.2 2 7 6"/>',
    org: '<path d="M4 20V9l8-5 8 5v11"/><path d="M9 20v-6h6v6"/><path d="M4 20h16"/>',
    hands: '<path d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.6-7 10-7 10z"/>'
  };
  var svg = function (k) { return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' + ICON[k] + '</svg>'; };
  // صورة أو شعار داخل الدائرة، وإلا الأيقونة
  var av = function (w, k, big) {
    var src = w.photo || w.logo;
    if (!src) return '<i class="vp-av' + (big ? ' big' : '') + (k === 'org' && big ? ' is-org' : '') + '">' + svg(k) + '</i>';
    return '<i class="vp-av has-img' + (big ? ' big' : '') + (w.photo ? '' : ' is-logo') + '"><img src="' + esc(src) + '" alt="" loading="lazy"' + (w.pos && w.photo ? ' style="object-position:' + esc(w.pos) + '"' : '') + '></i>';
  };
  var all = [];
  V.editions.forEach(function (e) { e.winners.forEach(function (w) { if (!w.withheld) { w.ed = e; all.push(w); } }); });

  // ---------- الأرقام ----------
  var entries = V.editions.reduce(function (n, e) { return n + e.entries; }, 0);
  $('[data-v-stats]').innerHTML = [[ar(V.editions.length), 'دورات منذ 2011'], [ar(entries), 'مشروعًا تطوعيًا ترشّح'], ['١٥', 'مجالًا'], ['٢', 'فئتان للتنافس']]
    .map(function (s) { return '<span><b>' + s[0] + '</b><small>' + s[1] + '</small></span>'; }).join('');

  // ---------- نبذة ----------
  $('[data-v-about]').textContent = V.about;
  $('[data-v-vision]').textContent = V.vision;
  $('[data-v-goals]').innerHTML = V.goals.map(function (g) { return '<li>' + esc(g) + '</li>'; }).join('');
  $('[data-v-cats]').innerHTML = ['ind', 'org'].map(function (k) {
    return '<div class="vc vc-' + k + '"><span class="vc-ic">' + svg(k) + '</span><b>' + V.cats[k].name + '</b><small>' + esc(V.cats[k].hint) + '</small><em>٣ مراكز</em></div>';
  }).join('') + '<p class="v-honor">' + esc(V.honor) + '</p>';
  $('[data-v-fields]').innerHTML = V.fields.map(function (f, i) { return '<li style="--i:' + i + '">' + esc(f) + '</li>'; }).join('');
  $('[data-v-criteria]').innerHTML = V.criteria.map(function (c, i) { return '<li><span>' + ar(i + 1) + '</span><b>' + c[0] + '</b><small>' + esc(c[1]) + '</small></li>'; }).join('');

  // ---------- منصّات التتويج ----------
  function step(w, k, r, partial) {
    if (!w) return partial ? '<li class="vp-s r' + r + ' is-off"><span class="vp-who"><i class="vp-av">؟</i><b>قيد التوثيق</b><small>لم نجد مصدرًا منشورًا</small></span><span class="vp-blk"><strong>' + ar(r) + '</strong></span></li>' : '';
    if (w.withheld) return '<li class="vp-s r' + w.rank + ' is-off"><span class="vp-who"><i class="vp-av">—</i><b>حُجب</b><small>لم يُمنح هذا المركز</small></span><span class="vp-blk"><strong>' + ar(w.rank) + '</strong></span></li>';
    return '<li class="vp-s r' + w.rank + '"><button type="button" data-vw="' + all.indexOf(w) + '">' +
      '<span class="vp-who">' + av(w, k) +
      '<b>' + esc(w.name) + '</b><small>«' + esc(w.project) + '»</small></span>' +
      '<span class="vp-blk"><strong>' + ar(w.rank) + '</strong></span></button></li>';
  }
  function podium(e, k) {
    var ws = e.winners.filter(function (w) { return w.cat === k; });
    if (!ws.length && e.partial) return '<div class="vp vp-' + k + '"><h4>' + svg(k) + 'فئة ' + V.cats[k].name + '</h4><p class="vp-none">لم نجد أسماء فائزي هذه الفئة في مصدر منشور بعد.</p></div>';
    var get = function (r) { return ws.filter(function (w) { return w.rank === r; })[0]; };
    return '<div class="vp vp-' + k + '"><h4>' + svg(k) + 'فئة ' + V.cats[k].name + '</h4><ol class="vp-row">' + step(get(2), k, 2, e.partial) + step(get(1), k, 1, e.partial) + step(get(3), k, 3, e.partial) + '</ol></div>';
  }
  $('[data-v-eds]').innerHTML = V.editions.map(function (e) {
    if (!e.winners.length) return '';
    return '<li class="ve"><div class="ve-h"><span class="ve-year">' + e.year + '</span><div><b>الدورة ' + ORD[e.n] + '</b><small>' + ar(e.entries) + ' مشروعًا مترشحًا</small></div></div>' +
      (e.cover ? '<figure class="ve-cover"><img src="' + esc(e.cover[0]) + '" alt="' + esc(e.cover[1]) + '" loading="lazy"><figcaption>' + esc(e.cover[1]) + '<span>الصورة: ' + esc(e.cover[2]) + '</span></figcaption></figure>' : '') +
      (e.partial ? '<p class="ve-partial">وثّقنا من هذه الدورة ما نشرته الجهات الفائزة نفسها، وبقية المراكز قيد البحث.</p>' : '') +
      podium(e, 'ind') + podium(e, 'org') + (e.note ? '<p class="ve-note">' + esc(e.note) + '</p>' : '') + '</li>';
  }).join('');
  $('[data-v-early]').innerHTML = V.editions.filter(function (e) { return !e.winners.length && !e.partial; }).map(function (e) {
    return '<li><span class="ve-year">' + e.year + '</span><b>الدورة ' + ORD[e.n] + '</b><small>' + ar(e.entries) + ' مشروعًا</small><em>الفائزون قيد التوثيق</em></li>';
  }).join('');

  // ---------- بطاقة الفائز ----------
  var sheet = $('[data-v-sheet]'), card = sheet.querySelector('.vs-card'), last = null;
  function open(w, btn) {
    last = btn;
    card.className = 'vs-card r' + w.rank;
    card.innerHTML = '<button type="button" class="vs-x" data-v-close aria-label="إغلاق"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 6l12 12M18 6 6 18"/></svg></button>' +
      '<span class="vs-rings" aria-hidden="true"><i></i><i></i><i></i></span>' +
      '<div class="vs-media">' + (w.photo ? '<figure class="vs-photo"><img src="' + esc(w.photo) + '" alt="' + esc(w.name) + '"><figcaption>الصورة: ' + esc(w.credit || '') + '</figcaption></figure>' : av(w, w.cat, true)) +
      (w.gallery ? '<div class="vs-gal">' + w.gallery.map(function (g) { return '<figure><img src="' + esc(g[0]) + '" alt="' + esc(g[1]) + '" loading="lazy"><figcaption>' + esc(g[1]) + '</figcaption></figure>'; }).join('') + '</div>' : '') + '</div>' +
      '<span class="vs-rank">' + RANK[w.rank] + ' · فئة ' + V.cats[w.cat].name + '</span>' +
      '<h3 id="vs-h">' + esc(w.name) + '</h3><p class="vs-proj">مشروع «' + esc(w.project) + '»' + (w.place ? ' · ' + esc(w.place) : '') + '</p>' +
      (w.why ? '<div class="vs-why"><span>نظير ماذا؟</span><p>' + esc(w.why) + '</p></div>' : '<p class="vs-none">لم نجد وصفًا منشورًا لهذا المشروع بعد.</p>') +
      (w.facts ? '<dl class="vs-facts">' + w.facts.map(function (f) { return '<div><dt>' + esc(f[0]) + '</dt><dd>' + esc(f[1]) + '</dd></div>'; }).join('') + '</dl>' : '') +
      (w.src ? '<p class="vs-src">المصدر: <a href="' + esc(w.src[1]) + '" target="_blank" rel="noopener">' + esc(w.src[0]) + '</a></p>' : '') +
      '<p class="vs-ed">الدورة ' + ORD[w.ed.n] + ' · ' + w.ed.year + '</p>';
    sheet.hidden = false;
    requestAnimationFrame(function () { sheet.classList.add('on'); });
    document.documentElement.style.overflow = 'hidden';
    card.querySelector('[data-v-close]').focus();
  }
  function close() {
    sheet.classList.remove('on'); document.documentElement.style.overflow = '';
    setTimeout(function () { sheet.hidden = true; }, 320);
    if (last) last.focus();
  }
  root.addEventListener('click', function (e) { var b = e.target.closest('[data-vw]'); if (b) open(all[+b.dataset.vw], b); });
  sheet.addEventListener('click', function (e) { if (e.target === sheet || e.target.closest('[data-v-close]')) close(); });
  addEventListener('keydown', function (e) { if (e.key === 'Escape' && !sheet.hidden) close(); });

  $('[data-v-src]').innerHTML = V.sources.map(function (s) { return '<li><a href="' + esc(s[1]) + '" target="_blank" rel="noopener">' + esc(s[0]) + '</a></li>'; }).join('');
})();
