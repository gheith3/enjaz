/* Ported from design js/fed-gallery.js (data from window.INJAZ_FED_GALLERY instead of federations-data.js).
   «رواق الاتحاد»: المكتبة المرئية في صفحة الاتحاد (federation.html?org=...).
   البيانات من gallery في js/federations-data.js: صور ووثائق (photos) ومقاطع يوتيوب (videos).
   تبويبات (الكل، صور، وثائق، فيديو) + حقب زمنية + عارض مكبّر بأسهم ولوحة مفاتيح وسحب. */
(function () {
  var sec = document.querySelector('[data-fp-gal]');
  if (!sec) return;
  // Gallery data from the database (gallery media and videos of the federation), rendered by the server.
  var g = window.INJAZ_FED_GALLERY;
  if (!g || !((g.photos || []).length + (g.videos || []).length)) { sec.hidden = true; return; }

  var esc = function (s) { var d = document.createElement('div'); d.textContent = s == null ? '' : s; return d.innerHTML; };
  var ar = function (n) { return String(n).replace(/\d/g, function (d) { return '٠١٢٣٤٥٦٧٨٩'[d]; }); };
  var ERAS = [['', 'كل الحقب'], ['early', 'قبل 1970'], ['70s', 'السبعينيات'], ['80s', 'الثمانينيات'], ['later', 'التسعينيات وما بعدها']];
  var items = (g.photos || []).map(function (p) { return Object.assign({ type: p.kind === 'doc' ? 'doc' : 'photo' }, p); })
    .concat((g.videos || []).map(function (v) { return Object.assign({ type: 'video', src: 'https://i.ytimg.com/vi/' + v.yt + '/hqdefault.jpg' }, v); }));
  var count = function (t) { return items.filter(function (i) { return i.type === t; }).length; };
  var TABS = [['', 'الكل'], ['photo', 'صور'], ['doc', 'وثائق وقصاصات'], ['video', 'فيديو']].filter(function (t) { return !t[0] || count(t[0]); });
  var st = { tab: '', era: '' }, shown = [];

  var PLAY = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5.5v13l11-6.5z"/></svg>';
  sec.querySelector('[data-gal-sum]').textContent = [count('photo') && ar(count('photo')) + ' صورة', count('doc') && ar(count('doc')) + ' وثيقة', count('video') && ar(count('video')) + ' مقاطع'].filter(Boolean).join(' · ');
  var tabsEl = sec.querySelector('[data-gal-tabs]'), erasEl = sec.querySelector('[data-gal-eras]'), grid = sec.querySelector('[data-gal-grid]');

  function draw() {
    tabsEl.innerHTML = TABS.map(function (t) { return '<button type="button" role="tab" data-tab="' + t[0] + '" aria-selected="' + (st.tab === t[0]) + '">' + t[1] + '</button>'; }).join('');
    var inTab = items.filter(function (i) { return !st.tab || i.type === st.tab; });
    var eras = ERAS.filter(function (e) { return !e[0] || inTab.some(function (i) { return i.era === e[0]; }); });
    if (!eras.some(function (e) { return e[0] === st.era; })) st.era = '';
    erasEl.innerHTML = eras.length > 2 ? eras.map(function (e) { return '<button type="button" data-era="' + e[0] + '" aria-pressed="' + (st.era === e[0]) + '">' + e[1] + '</button>'; }).join('') : '';
    shown = inTab.filter(function (i) { return !st.era || i.era === st.era; });
    grid.innerHTML = shown.map(function (it, n) {
      return '<button type="button" class="gal-it gal-' + it.type + (n === 0 && shown.length > 2 ? ' is-big' : '') + '" data-i="' + n + '">' +
        '<span class="gal-img"><img src="' + esc(it.src) + '" alt="" loading="lazy">' + (it.type === 'video' ? '<span class="gal-play">' + PLAY + '</span>' : '') + '</span>' +
        (it.year ? '<span class="gal-yr">' + esc(it.year) + '</span>' : '') +
        '<span class="gal-cap">' + (it.type === 'doc' ? '<small>وثيقة</small>' : it.type === 'video' ? '<small>فيديو</small>' : '') + esc(it.title) + '</span></button>';
    }).join('');
  }
  tabsEl.addEventListener('click', function (e) { var b = e.target.closest('[data-tab]'); if (b) { st.tab = b.dataset.tab; draw(); } });
  erasEl.addEventListener('click', function (e) { var b = e.target.closest('[data-era]'); if (b) { st.era = b.dataset.era; draw(); } });
  grid.addEventListener('click', function (e) { var b = e.target.closest('[data-i]'); if (b) open(+b.dataset.i); });

  // ---------- العارض المكبّر ----------
  var box = document.createElement('div');
  box.className = 'gal-box'; box.hidden = true; box.setAttribute('role', 'dialog'); box.setAttribute('aria-modal', 'true'); box.setAttribute('aria-label', 'رواق الاتحاد');
  box.innerHTML = '<button type="button" class="gal-x" data-x aria-label="إغلاق">✕</button>' +
    '<button type="button" class="gal-nav gal-prev" data-step="-1" aria-label="السابق">›</button><button type="button" class="gal-nav gal-next" data-step="1" aria-label="التالي">‹</button>' +
    '<figure><div class="gal-stage" data-stage></div><figcaption data-cap></figcaption></figure>';
  document.body.appendChild(box);
  var cur = 0, last = null;
  function show(n) {
    cur = (n + shown.length) % shown.length; var it = shown[cur];
    box.querySelector('[data-stage]').innerHTML = it.type === 'video'
      ? '<iframe src="https://www.youtube-nocookie.com/embed/' + esc(it.yt) + '?autoplay=1&rel=0" title="' + esc(it.title) + '" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe>'
      : '<img src="' + esc(it.src) + '" alt="' + esc(it.title) + '">';
    box.querySelector('[data-cap]').innerHTML = '<b>' + esc(it.title) + '</b>' +
      '<span>' + [it.year, it.note].filter(Boolean).map(esc).join(' · ') + '</span>' +
      '<small>' + ar(cur + 1) + ' / ' + ar(shown.length) + ' — ' +
      (it.type === 'video' ? '<a href="https://www.youtube.com/watch?v=' + esc(it.yt) + '" target="_blank" rel="noopener">مشاهدة على يوتيوب ↗</a>'
        : it.credit ? 'المصدر: <a href="' + esc(it.credit[1]) + '" target="_blank" rel="noopener">' + esc(it.credit[0]) + '</a>' : '') + '</small>';
    box.classList.toggle('is-single', shown.length < 2);
  }
  function open(n) { last = document.activeElement; show(n); box.hidden = false; document.documentElement.classList.add('gal-lock'); box.querySelector('[data-x]').focus(); }
  function close() { box.hidden = true; box.querySelector('[data-stage]').innerHTML = ''; document.documentElement.classList.remove('gal-lock'); if (last) last.focus(); }
  box.addEventListener('click', function (e) {
    if (e.target === box || e.target.closest('[data-x]')) close();
    var s = e.target.closest('[data-step]'); if (s) show(cur + +s.dataset.step);
  });
  document.addEventListener('keydown', function (e) {
    if (box.hidden) return;
    if (e.key === 'Escape') close();
    else if (e.key === 'ArrowLeft') show(cur + 1); // الاتجاه عربي: اليسار = التالي
    else if (e.key === 'ArrowRight') show(cur - 1);
  });
  var x0 = null;
  box.addEventListener('touchstart', function (e) { x0 = e.touches[0].clientX; }, { passive: true });
  box.addEventListener('touchend', function (e) { if (x0 == null) return; var dx = e.changedTouches[0].clientX - x0; if (Math.abs(dx) > 50) show(cur + (dx > 0 ? 1 : -1)); x0 = null; });

  draw();
})();
