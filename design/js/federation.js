/* صفحة الاتحاد: federation.html?org=oman-fa
   البيانات من js/federations-data.js. إنجازات الاتحاد ومنجزوه تُجلب من صفحتي الإنجازات والمنجزين
   (البطاقات التي تحمل data-orgs بهذا الاتحاد)، فلا تُكرَّر يدويًا. */
(function () {
  var root = document.querySelector('[data-fed-page]');
  var list = window.INJAZ_FEDERATIONS || [];
  if (!root || !list.length) return;
  var slug = new URLSearchParams(location.search).get('org') || 'oman-fa';
  var f = list.filter(function (x) { return x.slug === slug; })[0];
  var $ = function (s) { return root.querySelector(s); };
  var ar = function (n) { return String(n).replace(/\d/g, function (d) { return '٠١٢٣٤٥٦٧٨٩'[d]; }); };
  // السنوات بالأرقام اللاتينية (1980 لا ١٩٨٠): الصفر العربي «٠» يشبه النقطة فتُقرأ ٢٠١٠ خطأً
  var yr = function (n) { return String(n); };
  var esc = function (s) { var d = document.createElement('div'); d.textContent = s == null ? '' : s; return d.innerHTML; };
  var CAT = 'الرياضة';
  var link = function (page) { return page + '?cat=' + encodeURIComponent(CAT) + '&org=' + encodeURIComponent(slug); };
  var plural = function (n, one, two, few, many) { return n === 1 ? one : n === 2 ? two : ar(n) + ' ' + (n <= 10 ? few : many); };

  if (!f) { $('[data-fp-hero]').innerHTML = '<h1>الاتحاد غير موجود</h1><p class="fp-en"><a href="institutions.html">العودة إلى المؤسسات</a></p>'; return; }

  document.title = f.name + ' | إنجازات شباب عمان';
  var crumb = document.querySelector('.crumb'); if (crumb) crumb.textContent = 'المؤسسات';

  // ---------- الرأس ----------
  var chips = (f.memberships || []).map(function (m) { return '<span>' + esc(m[0].replace(/ \(.*\)/, '')) + ' <b>' + yr(m[1]) + '</b></span>'; }).join('');
  $('[data-fp-hero]').innerHTML =
    '<span class="fp-logo"><img src="' + esc(f.logo) + '" alt="شعار ' + esc(f.name) + '"></span>' +
    '<span class="fp-type">مؤسسة رياضية' + (f.founded ? ' – تأسس عام ' + yr(f.founded) : '') + '</span>' +
    '<h1>' + esc(f.name) + '</h1>' + (f.en ? '<p class="fp-en" dir="ltr">' + esc(f.en) + (f.abbr ? ' (' + esc(f.abbr) + ')' : '') + '</p>' : '') +
    (chips ? '<div class="fp-chips">' + chips + '</div>' : '') +
    '<nav class="fp-jump" aria-label="أقسام الصفحة"><a href="#fp-about">عن الاتحاد</a>' + (f.presidents ? '<a href="#fp-pres">الرؤساء</a>' : '') +
    '<a href="#fp-ach">الإنجازات</a><a href="#fp-people">المنجزون</a></nav>';

  // ---------- عن الاتحاد ----------
  var facts = [['التأسيس', f.founded && yr(f.founded)], ['الإشهار الرسمي', f.declared && yr(f.declared)], ['المقر', f.hq],
    ['الرئيس الحالي', (f.presidents || []).filter(function (p) { return p.current; }).map(function (p) { return p.name; })[0]]]
    .filter(function (x) { return x[1]; });
  $('[data-fp-about]').innerHTML = f.about
    ? (facts.length ? '<dl class="fp-facts">' + facts.map(function (x) { return '<div><dt>' + x[0] + '</dt><dd>' + esc(x[1]) + '</dd></div>'; }).join('') + '</dl>' : '') +
      '<p class="fp-text">' + esc(f.about) + '</p>' +
      ((f.memberships || []).length ? '<ul class="fp-members">' + f.memberships.map(function (m) { return '<li><b>' + yr(m[1]) + '</b>' + esc(m[0]) + '</li>'; }).join('') + '</ul>' : '') +
      (f.site ? '<a class="fp-site" href="' + esc(f.site) + '" target="_blank" rel="noopener">الموقع الرسمي للاتحاد <span aria-hidden="true">↗</span></a>' : '')
    : '<p class="fp-soon">تفاصيل الاتحاد (التأسيس، النبذة، الرؤساء) تُستكمل قريبًا.</p>';

  // ---------- الرؤساء: خط زمني ----------
  var pres = $('[data-fp-pres]');
  if (f.presidents && f.presidents.length) {
    pres.querySelector('ol').innerHTML = f.presidents.slice().reverse().map(function (p) {
      var yrs = yr(p.from) + ' – ' + (p.to ? yr(p.to) : 'الآن');
      if (p.gap) return '<li class="fp-gap"><span class="fp-yrs">' + yrs + '</span><span class="fp-note">' + esc(p.note) + '</span></li>';
      // الصورة في إطار مربع على الطرف الأيسر من البطاقة؛ بلا صورة يظهر رسم شخص باهت
      var ph = p.photo ? '<img src="' + esc(p.photo) + '" alt="' + esc(p.name) + '" loading="lazy">'
        : '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="9" r="4"/><path d="M4 21c0-4.4 3.6-7 8-7s8 2.6 8 7z"/></svg>';
      return '<li' + (p.current ? ' class="is-now"' : '') + '><span class="fp-pt"><span class="fp-yrs">' + yrs + '</span><b>' + esc(p.name) + '</b>' +
        (p.note ? '<span class="fp-note">' + esc(p.note) + '</span>' : '') + (p.current ? '<span class="fp-now">الرئيس الحالي</span>' : '') + '</span>' +
        '<span class="fp-pp' + (p.photo ? '' : ' is-empty') + '">' + ph + '</span></li>';
    }).join('');
  } else pres.hidden = true;

  // ---------- أبرز المحطات ----------
  var ms = $('[data-fp-ms]');
  if (f.milestones && f.milestones.length) {
    ms.querySelector('ul').innerHTML = f.milestones.map(function (m) {
      return '<li><span class="fp-y">' + yr(m.year) + '</span><span><b>' + esc(m.title) + '</b>' + (m.place ? '<small>' + esc(m.place) + '</small>' : '') + '</span></li>';
    }).join('');
  } else ms.hidden = true;

  // ---------- المصادر ----------
  var src = $('[data-fp-src]');
  if (f.sources && f.sources.length) src.innerHTML = 'المصادر: ' + f.sources.map(function (s) { return '<a href="' + esc(s[1]) + '" target="_blank" rel="noopener">' + esc(s[0]) + '</a>'; }).join(' · ');
  else src.hidden = true;

  // ---------- إنجازات الاتحاد ومنجزوه: من صفحتي الإنجازات والمنجزين ----------
  var has = function (el) { return (' ' + (el.getAttribute('data-orgs') || '') + ' ').indexOf(' ' + slug + ' ') > -1; };
  var load = function (page) {
    return fetch(page, { cache: 'no-cache' }).then(function (r) { if (!r.ok) throw 0; return r.text(); })
      .then(function (h) { return new DOMParser().parseFromString(h, 'text/html'); });
  };
  var achBox = $('[data-fp-achlist]'), achMore = $('[data-fp-achmore]');
  var pplBox = $('[data-fp-ppl]'), pplMore = $('[data-fp-pplmore]');
  achMore.href = link('achievements.html'); pplMore.href = link('achievers.html');

  load('achievements.html').then(function (doc) {
    var items = [].filter.call(doc.querySelectorAll('[data-results] > .ach'), function (el) { return has(el) && !el.hasAttribute('data-sample'); })
      .sort(function (a, b) { return (b.dataset.date || '').localeCompare(a.dataset.date || ''); });
    $('[data-fp-achcount]').textContent = items.length ? plural(items.length, 'إنجاز واحد', 'إنجازان', 'إنجازات', 'إنجازًا') : '';
    if (!items.length) { achBox.innerHTML = '<p class="fp-soon">لا توجد إنجازات مرتبطة بهذا الاتحاد بعد.</p>'; achMore.hidden = true; return; }
    achBox.innerHTML = items.slice(0, 6).map(function (el) {
      var a = el.querySelector('.ach-link'), img = el.querySelector('.ach-img'), who = el.querySelector('.ach-meta span'), t = el.querySelector('time');
      return '<a class="fp-card" href="' + esc(a ? a.getAttribute('href') : '#') + '"><span class="fp-img"><img src="' + esc(img ? img.getAttribute('src') : '') + '" alt="" loading="lazy" style="' + esc(img ? img.getAttribute('style') || '' : '') + '"></span>' +
        '<span class="fp-cb"><b>' + esc((el.querySelector('h3') || {}).textContent) + '</b><small>' + esc(who ? who.textContent : '') + (t ? ' · ' + esc(t.textContent) : '') + '</small></span></a>';
    }).join('');
    achMore.innerHTML = 'كل إنجازات الاتحاد (' + ar(items.length) + ') <span aria-hidden="true">←</span>';
  }).catch(function () { achBox.innerHTML = '<p class="fp-soon">تظهر الإنجازات عند فتح الموقع من الخادم (Live Server).</p>'; });

  load('achievers.html').then(function (doc) {
    var seen = {}, items = [].filter.call(doc.querySelectorAll('[data-results] > .person-card'), function (el) {
      var n = (el.querySelector('h3') || {}).textContent; if (!has(el) || el.hasAttribute('data-sample') || seen[n]) return false; seen[n] = 1; return true;
    });
    $('[data-fp-pplcount]').textContent = items.length ? plural(items.length, 'منجز واحد', 'منجزان', 'منجزين', 'منجزًا') : '';
    if (!items.length) { pplBox.innerHTML = '<p class="fp-soon">لا يوجد منجزون مرتبطون بهذا الاتحاد بعد.</p>'; pplMore.hidden = true; return; }
    pplBox.innerHTML = items.map(function (el) {
      var a = el.querySelector('.ach-link'), img = el.querySelector('.ach-img'), hl = el.querySelector('.ach-headline'), m = el.querySelector('.ach-meta');
      return '<a class="fp-person" href="' + esc(a ? a.getAttribute('href') : '#') + '"><span class="fp-ph"><img src="' + esc(img ? img.getAttribute('src') : '') + '" alt="" loading="lazy" style="' + esc(img ? img.getAttribute('style') || '' : '') + '"></span>' +
        '<b>' + esc((el.querySelector('h3') || {}).textContent) + '</b><small>' + esc(hl ? hl.textContent : '') + (m ? ' · ' + esc(m.textContent) : '') + '</small></a>';
    }).join('');
  }).catch(function () { pplBox.innerHTML = '<p class="fp-soon">يظهر المنجزون عند فتح الموقع من الخادم (Live Server).</p>'; });
})();
