/* صفحة الإنجازات: عند اختيار تصنيف له اتحادات (مثل الرياضة) تختفي بقية المجالات (تبقى «الكل» والمختار)،
   وينزل مستطيل عرضي بشعارات الاتحادات في سطرين، مع اختيار الجنس.
   اختيار شعار يعرض إنجازات ذلك الاتحاد فقط، ويُظهر شريطًا تعريفيًا به فوق النتائج.
   البيانات من #federations-data، وفي الموقع الحي تُولَّد من «المؤسسات» في لوحة التحكم. */
(function () {
  var dataEl = document.getElementById('federations-data');
  var panel = document.querySelector('[data-fed-panel]');
  var track = document.querySelector('[data-fed-track]');
  var spot = document.querySelector('[data-fed-spot]');
  var results = document.querySelector('[data-results]');
  if (!dataEl || !panel || !track || !spot || !results || !window.injazFilter) return;
  var cfg; try { cfg = JSON.parse(dataEl.textContent); } catch (e) { return; }
  var groups = cfg.groups || {};
  var titleEl = panel.querySelector('[data-fed-title]'); if (titleEl && cfg.title) titleEl.textContent = cfg.title;
  var reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  var openCat = '', activeOrg = '';
  var section = panel.closest('.ach-filters') || panel.parentElement;
  // الجنس والسنة والمستوى من مربع «فلترة» (js/ach-filters.js)؛ هنا فقط تُحدَّث أعداد الاتحادات

  var ar = function (n) { return String(n).replace(/\d/g, function (d) { return '٠١٢٣٤٥٦٧٨٩'[d]; }); };
  // صيغة العدد العربية: «إنجاز» في صفحة الإنجازات، و«منجز» في صفحة المنجزين (data-unit="achievers" على المستطيل)
  var people = panel.dataset.unit === 'achievers';
  var plural = function (n) {
    if (people) return n === 0 ? 'لا منجزين بعد' : n === 1 ? 'منجز واحد' : n === 2 ? 'منجزان' : n <= 10 ? ar(n) + ' منجزين' : ar(n) + ' منجزًا';
    if (n === 0) return 'لا إنجازات بعد';
    if (n === 1) return 'إنجاز واحد';
    if (n === 2) return 'إنجازان';
    if (n <= 10) return ar(n) + ' إنجازات';
    return ar(n) + ' إنجازًا';
  };
  var esc = function (s) { var d = document.createElement('div'); d.textContent = s; return d.innerHTML; };
  var count = function (cat, slug) {
    var n = 0, f = window.injazFilter.get ? window.injazFilter.get() : {};
    [].forEach.call(results.children, function (el) {
      var y = (el.dataset.date || '').slice(0, 4);
      if ((el.dataset.cat || '') === cat && (!f.gender || el.dataset.gender === f.gender) && (!f.from || y >= f.from) && (!f.to || y <= f.to) &&
        (!f.scope || el.dataset.scope === f.scope) && (' ' + (el.dataset.orgs || '') + ' ').indexOf(' ' + slug + ' ') > -1) n++;
    });
    return n;
  };
  var syncUrl = function () {
    try {
      var p = new URLSearchParams(location.search);
      if (openCat) p.set('cat', openCat); else p.delete('cat');
      if (activeOrg) p.set('org', activeOrg); else p.delete('org');
      var q = p.toString(); history.replaceState(null, '', location.pathname + (q ? '?' + q : '') + location.hash);
    } catch (e) {}
  };

  // موازنة الأحجام: كل شعار يأخذ المساحة نفسها تقريبًا داخل بطاقته، أيًّا كان شكله (عرضي، طولي، مربع).
  // r = العرض ÷ الارتفاع للشعار بعد قص فراغه. تُقاس البطاقة الفعلية (تختلف بين الهاتف والكمبيوتر) ويُحسب المقاس بالبكسل.
  function sizeLogos() {
    var first = track.querySelector('.fed:not(.fed-ph)'); if (!first) return;
    var tw = first.clientWidth, th = first.clientHeight - 36; if (!tw || !th) return;
    var A = 0.42 * tw * th; // المساحة المستهدفة لكل شعار
    [].forEach.call(track.querySelectorAll('.fed:not(.fed-ph) .fed-logo'), function (el) {
      var r = +el.dataset.r || 1, w = Math.sqrt(A * r), h = w / r;
      if (w > tw * 0.88) { w = tw * 0.88; h = w / r; }
      if (h > th * 0.8) { h = th * 0.8; w = h * r; }
      el.style.width = Math.round(w) + 'px'; el.style.height = Math.round(h) + 'px';
    });
  }
  var rt; window.addEventListener('resize', function () { clearTimeout(rt); rt = setTimeout(sizeLogos, 120); });
  function render(cat) {
    track.innerHTML = '';
    (groups[cat] || []).forEach(function (f) {
      var n = count(cat, f.slug);
      if (cfg.hideEmpty && !n) return;
      var b = document.createElement('button');
      b.type = 'button'; b.className = 'fed' + (n ? '' : ' is-empty'); b.dataset.slug = f.slug; b.setAttribute('aria-pressed', 'false');
      b.title = f.name; b.setAttribute('aria-label', f.name + '، ' + plural(n));
      // الاسم المختصر (short) يظهر تحت الشعار، والاسم الكامل في التلميح وشريط الاتحاد
      if (f.placeholder) b.className += ' fed-ph'; // شعار تجريبي: يظهر اسمه تحته
      b.innerHTML = '<span class="fed-logo" data-r="' + (+f.r || 1) + '"><img src="' + esc(f.logo) + '" alt="" loading="lazy"></span>' +
        (n ? '<span class="fed-count" aria-hidden="true">' + ar(n) + '</span>' : '') +
        '<span class="fed-name" aria-hidden="true">' + esc(f.short || f.name) + '</span>';
      b.addEventListener('click', function () { selectOrg(activeOrg === f.slug ? '' : f.slug); });
      track.appendChild(b);
    });
    if (!panel.hidden) sizeLogos();
  }
  function openPanel(cat) {
    openCat = cat; render(cat);
    if (titleEl) titleEl.textContent = cat;
    section.classList.add('fed-open'); // تُخفي بقية المجالات (css/federations.css)
    panel.hidden = false; sizeLogos();
    requestAnimationFrame(function () { requestAnimationFrame(function () { panel.classList.add('open'); }); });
  }
  function closePanel() {
    openCat = ''; panel.classList.remove('open'); section.classList.remove('fed-open');
    var done = function () { if (!openCat) panel.hidden = true; };
    if (reduce) done(); else setTimeout(done, 460);
  }
  // تغيير أي فلترة: تُحدَّث أعداد الشعارات وشريط الاتحاد المختار
  ['injaz:filters', 'injaz:filters-reset'].forEach(function (ev) {
    document.addEventListener(ev, function () {
      if (!openCat) return;
      render(openCat); [].forEach.call(track.children, function (b) { b.setAttribute('aria-pressed', String(b.dataset.slug === activeOrg)); });
      if (activeOrg) selectOrg(activeOrg);
    });
  });
  function selectOrg(slug) {
    activeOrg = slug;
    [].forEach.call(track.children, function (b) { b.setAttribute('aria-pressed', String(b.dataset.slug === slug)); });
    window.injazFilter.setOrg(slug);
    var f = slug && (groups[openCat] || []).filter(function (x) { return x.slug === slug; })[0];
    if (f) {
      spot.innerHTML = '<span class="spot-logo"><img src="' + esc(f.logo) + '" alt="شعار ' + esc(f.name) + '"></span>' +
        '<span class="spot-txt"><span class="spot-eyebrow">' + (people ? 'منجزون بالشراكة مع' : 'إنجازات موثّقة بالشراكة مع') + '</span><span class="spot-name">' + esc(f.name) + '</span>' +
        '<span class="spot-count">' + plural(count(openCat, slug)) + ' في ' + esc(openCat) + '</span>' +
        (f.url ? '<a class="spot-link" href="' + esc(f.url) + '">صفحة الاتحاد <span aria-hidden="true">←</span></a>' : '') + '</span>' +
        '<button type="button" class="spot-close" aria-label="إلغاء تصفية الاتحاد"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg></button>';
      spot.querySelector('.spot-close').addEventListener('click', function () { selectOrg(''); });
      spot.hidden = false;
    } else { spot.hidden = true; spot.innerHTML = ''; }
    syncUrl();
  }

  var allChip = document.querySelector('[data-chips] .chip[data-filter="*"]');
  // يُستدعى بعد معالج التصنيف في main.js (سُجّل قبله)، فيكون التصنيف قد طُبّق
  document.querySelectorAll('[data-chips] .chip').forEach(function (chip) {
    chip.addEventListener('click', function () {
      var cat = chip.dataset.filter;
      if (activeOrg) selectOrg('');
      if (groups[cat] && openCat !== cat) openPanel(cat);
      else if (groups[cat] && openCat === cat) { closePanel(); if (allChip) allChip.click(); } // ضغطة ثانية (✕) تعيد كل المجالات
      else if (openCat) closePanel();
      syncUrl();
    });
  });
  // إلغاء كل التصفيات من زر «مسح» يعيد الحالة الأولى
  document.querySelectorAll('[data-clear-all]').forEach(function (b) { b.addEventListener('click', function () { if (activeOrg) selectOrg(''); }); });

  // فتح رابط مشارَك: achievements.html?cat=الرياضة&org=oman-fa
  try {
    var p = new URLSearchParams(location.search), c = p.get('cat'), o = p.get('org');
    if (c && groups[c]) {
      var chip = [].filter.call(document.querySelectorAll('[data-chips] .chip'), function (x) { return x.dataset.filter === c; })[0];
      if (chip) { chip.click(); if (o) selectOrg(o); }
    }
  } catch (e) {}
})();
