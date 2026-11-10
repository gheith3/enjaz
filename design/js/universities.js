/* صفحة «الجامعات والكليات» (universities.html): الجامعات أولًا، ثم الكليات، ثم جامعة التقنية والعلوم التطبيقية.
   البيانات من js/universities-data.js، وأعداد المنجزين والإنجازات المرتبطة من js/org-links.js. */
(function () {
  var root = document.querySelector('[data-unis]');
  var list = window.INJAZ_UNIS || [];
  if (!root || !list.length) return;
  var ar = function (n) { return String(n).replace(/\d/g, function (d) { return '٠١٢٣٤٥٦٧٨٩'[d]; }); };
  var pl = function (n, one, two, few, many) { return n === 1 ? one : n === 2 ? two : ar(n) + ' ' + (n <= 10 ? few : many); };
  var esc = function (s) { var d = document.createElement('div'); d.textContent = s == null ? '' : s; return d.innerHTML; };
  var url = function (u) { return 'university.html?org=' + encodeURIComponent(u.slug); };
  var by = function (g) { return list.filter(function (u) { return u.group === g; }); };
  var unis = by('uni'), cols = by('college'), tech = by('tech');
  var mono = function (u) { return (u.abbr || u.en.replace(/[^A-Z]/g, '')).slice(0, 4); };
  var logo = function (u, wide) {
    var src = wide && u.logoWide || u.logo;
    return src ? '<img src="' + esc(src) + '" alt="" loading="lazy">' : '<span class="u-mono" aria-hidden="true">' + esc(mono(u)) + '</span>';
  };

  // ---------- العدّاد في الرأس ----------
  var sum = document.querySelector('[data-u-sum]');
  if (sum) sum.innerHTML = '<span><b>' + ar(unis.length) + '</b>جامعات</span><span><b>' + ar(cols.length) + '</b>كلية</span>' +
    (tech[0] ? '<span><b>' + ar(tech[0].branches.length) + '</b>فروع للتقنية</span>' : '');

  // ---------- البطاقات ----------
  function card(u) {
    return '<a class="u-card" href="' + url(u) + '" data-slug="' + u.slug + '" data-name="' + esc(u.name + ' ' + u.en + ' ' + (u.city || '')) + '">' +
      '<span class="u-logo" data-r="' + (u.r || 1) + '">' + logo(u) + '</span>' +
      '<span class="u-info"><b>' + esc(u.name) + '</b><small>' + esc(u.city || '') + (u.sector ? ' · ' + esc(u.sector) : '') + '</small></span>' +
      '<span class="u-links" data-links hidden></span></a>';
  }
  var sq = unis[0], restUnis = unis.slice(1);
  var spot = sq && sq.about ? '<a class="u-spot" href="' + url(sq) + '"' + (sq.color ? ' style="--c1:' + sq.color[0] + ';--c2:' + sq.color[1] + '"' : '') + ' data-slug="' + sq.slug + '" data-name="' + esc(sq.name + ' ' + sq.en) + '">' +
    '<span class="u-spot-logo">' + logo(sq, true) + '</span>' +
    '<span class="u-spot-txt"><small>' + esc(sq.tagline || 'الملف التعريفي') + '</small><b>' + esc(sq.name) + '</b>' +
    '<span class="u-spot-stats">' + (sq.stats || []).slice(0, 3).map(function (s) { return '<span><b>' + esc(s[0]) + '</b>' + esc(s[1]) + '</span>'; }).join('') + '</span>' +
    '<em>الملف التعريفي والرؤساء والمنجزون <span aria-hidden="true">←</span></em></span><span class="u-links" data-links hidden></span></a>' : '';
  var techCard = tech.map(function (u) {
    return '<a class="u-tech" href="' + url(u) + '" data-slug="' + u.slug + '" data-name="' + esc(u.name + ' ' + u.en + ' ' + (u.branches || []).join(' ')) + '">' +
      '<span class="u-tech-top"><span class="u-logo" data-r="' + (u.r || 1) + '">' + logo(u) + '</span>' +
      '<span class="u-info"><small>' + esc('سابقًا: ' + (u.formerly || '')) + '</small><b>' + esc(u.name) + '</b></span></span>' +
      '<span class="u-branches">' + (u.branches || []).map(function (b) { return '<span>' + esc(b) + '</span>'; }).join('') + '</span>' +
      '<span class="u-links" data-links hidden></span></a>';
  }).join('');

  root.innerHTML =
    '<section class="u-group" data-g="uni"><h2 class="u-gh"><span>الجامعات</span><b>' + ar(unis.length) + '</b></h2>' + spot + '<div class="u-grid">' + restUnis.map(card).join('') + '</div></section>' +
    '<section class="u-group" data-g="college"><h2 class="u-gh"><span>الكليات</span><b>' + ar(cols.length) + '</b></h2><div class="u-grid">' + cols.map(card).join('') + '</div></section>' +
    (tech.length ? '<section class="u-group" data-g="tech"><h2 class="u-gh"><span>جامعة التقنية والعلوم التطبيقية</span><b>' + ar(tech[0].branches.length) + ' فروع</b></h2>' + techCard + '</section>' : '') +
    '<p class="u-empty" data-u-empty hidden>لا توجد نتائج مطابقة.</p>';

  // ---------- موازنة أحجام الشعارات ----------
  function sizeLogos() {
    [].forEach.call(root.querySelectorAll('.u-logo'), function (box) {
      var img = box.querySelector('img'); if (!img) return;
      var bw = box.clientWidth - 12, bh = box.clientHeight - 12; if (bw <= 0 || bh <= 0) return;
      var r = +box.dataset.r || 1, A = 0.5 * bw * bh, w = Math.sqrt(A * r), h = w / r;
      if (w > bw) { w = bw; h = w / r; } if (h > bh) { h = bh; w = h * r; }
      img.style.width = Math.round(w) + 'px'; img.style.height = Math.round(h) + 'px';
    });
  }
  sizeLogos();
  var rt; window.addEventListener('resize', function () { clearTimeout(rt); rt = setTimeout(sizeLogos, 120); });

  // ---------- التصفية: المجموعة + البحث ----------
  var st = { g: '', q: '' };
  var chips = document.querySelector('[data-u-chips]'), q = document.querySelector('[data-u-q]'), empty = root.querySelector('[data-u-empty]');
  function apply() {
    var any = false, needle = st.q.trim();
    [].forEach.call(root.querySelectorAll('.u-group'), function (g) {
      var on = !st.g || g.dataset.g === st.g, n = 0;
      [].forEach.call(g.querySelectorAll('[data-name]'), function (c) { var m = !needle || c.dataset.name.indexOf(needle) > -1; c.hidden = !m; if (m) n++; });
      g.hidden = !on || !n; if (!g.hidden) any = true;
    });
    empty.hidden = any; sizeLogos();
  }
  if (chips) chips.addEventListener('click', function (e) {
    var b = e.target.closest('[data-g]'); if (!b) return; st.g = b.dataset.g;
    [].forEach.call(chips.children, function (x) { x.setAttribute('aria-pressed', String(x === b)); x.classList.toggle('active', x === b); });
    apply();
  });
  if (q) q.addEventListener('input', function () { st.q = q.value; apply(); });

  // ---------- أعداد المنجزين والإنجازات المرتبطة ----------
  if (window.InjazLinks) InjazLinks.summary().then(function (s) {
    [].forEach.call(root.querySelectorAll('[data-slug]'), function (c) {
      var x = s[c.dataset.slug]; if (!x) return; var parts = [];
      if (x.own) parts.push(pl(x.own, 'إنجاز واحد', 'إنجازان', 'إنجازات', 'إنجازًا'));
      if (x.people) parts.push(pl(x.people, 'منتسب منجز', 'منتسبان منجزان', 'منتسبين منجزين', 'منتسبًا منجزًا'));
      if (x.members) parts.push(pl(x.members, 'إنجاز لمنتسبيها', 'إنجازان لمنتسبيها', 'إنجازات لمنتسبيها', 'إنجازًا لمنتسبيها'));
      var el = c.querySelector('[data-links]'); if (el && parts.length) { el.textContent = parts.join(' · '); el.hidden = false; }
    });
  }).catch(function () {});
})();
