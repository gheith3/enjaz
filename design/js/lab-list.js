/* «المختبر»: بطاقات الابتكارات والمنشورات (بطاقتان في كل سطر)، وصفحة الكل مع الفرز، وصفحة الابتكار أو البحث.
   البيانات في js/lab-data.js. */
(function () {
  var L = window.LAB; if (!L) return;
  var esc = function (s) { return String(s == null ? '' : s).replace(/[&<>"]/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]; }); };
  var ar = function (n) { return String(n).replace(/\d/g, function (d) { return '٠١٢٣٤٥٦٧٨٩'[d]; }); };
  var q = new URLSearchParams(location.search);
  var url = function (t, id) { return 'lab-item.html?t=' + t + '&id=' + encodeURIComponent(id); };
  var byNew = function (a, b) { return b.year - a.year; };

  var invCard = function (x) {
    return '<a class="lb-card" href="' + url('inv', x.id) + '">' +
      '<span class="lb-img"><img src="' + esc(x.img) + '" alt="" loading="lazy"><span class="lb-medal ' + x.medal + '">' + esc(x.medalLabel) + '</span></span>' +
      '<span class="lb-body"><small class="lb-ev">' + esc(x.eventLabel) + '</small><b>' + esc(x.short || x.title) + '</b><span class="lb-who">' + esc(x.who) + '</span><span class="lb-date">' + esc(x.date) + '</span></span></a>';
  };
  var pubCard = function (x) {
    return '<a class="lb-card lb-pub" href="' + url('pub', x.id) + '">' +
      '<span class="lb-img lb-pub-img"><img src="assets/img/lab/pub-' + esc(x.id) + '.svg" alt="" loading="lazy"><em>صورة تعبيرية</em></span>' +
      '<span class="lb-pub-top"><span class="lb-field">' + esc(x.field) + '</span><span class="lb-date">' + esc(x.date) + '</span></span>' +
      '<span class="lb-num">' + esc(x.finding[0]) + '</span>' +
      '<b>' + esc(x.title) + '</b><span class="lb-who">' + esc(x.lead) + '</span></a>';
  };

  // ---------- science.html: أحدث ٦ في كل قسم ----------
  [['inv', invCard, 'كل الابتكارات'], ['pubs', pubCard, 'كل المنشورات']].forEach(function (k) {
    var box = document.querySelector('[data-lab-list="' + k[0] + '"]'); if (!box) return;
    var items = L[k[0]].slice().sort(byNew);
    box.innerHTML = '<div class="lb-grid">' + items.slice(0, 6).map(k[1]).join('') + '</div>' +
      '<a class="lb-more" href="lab-all.html?t=' + k[0] + '">' + k[2] + ' <small>' + ar(items.length) + '</small> <span aria-hidden="true">←</span></a>';
    var tab = document.querySelector('#tab-' + k[0] + ' small'); if (tab) tab.textContent = ar(items.length);
    var desktop = document.querySelector('[data-lab-desktop="' + k[0] + '"]');
    if (desktop) {
      // الكمبيوتر: الابتكارات ٥ بطاقات + بطاقة «اكتشف المزيد» تنقل إلى صفحة الإنجازات؛ المنشورات ٤ في سطر واحد
      var more = k[0] === 'inv'
        ? '<a class="lb-tile-more" href="achievements.html"><span class="lb-tm-ic" aria-hidden="true">✦</span><b>اكتشف المزيد من الإنجازات</b><small>إنجازات عُمانية في الرياضة والثقافة والأدب والعلوم</small><span class="lb-tm-go" aria-hidden="true">←</span></a>' : '';
      desktop.innerHTML = '<div class="lb-grid">' + items.slice(0, k[0] === 'inv' ? 5 : 4).map(k[1]).join('') + more + '</div>';
      var head = desktop.parentNode.querySelector('.lab-collection-head');
      if (head && !head.querySelector('.lb-head-all')) head.insertAdjacentHTML('beforeend', '<a class="lb-head-all" href="lab-all.html?t=' + k[0] + '">' + k[2] + ' <small>' + ar(items.length) + '</small> <span aria-hidden="true">←</span></a>');
    }
  });

  // ---------- بطاقات الكمبيوتر لصفحة «الكل» بنفس طابع صفحة المختبر الجديدة ----------
  var invCardX = function (x) {
    return '<a class="lz-card" href="' + url('inv', x.id) + '">' +
      '<span class="lz-img"><img src="' + esc(x.img) + '" alt="" loading="lazy"><span class="lz-medal ' + x.medal + '">' + esc(x.medalLabel) + '</span></span>' +
      '<span class="lz-body"><small>' + esc(x.eventLabel) + (x.place ? ' • ' + esc(x.place) : '') + '</small><b>' + esc(x.short || x.title) + '</b>' +
      '<span class="lz-who">' + esc(x.who) + (x.org ? '<em>' + esc(x.org) + '</em>' : '') + '</span>' +
      '<span class="lz-foot"><time>' + esc(x.date) + '</time><i>اقرأ القصة ←</i></span></span></a>';
  };
  var pubCardX = function (x) {
    return '<a class="lz-pub" href="' + url('pub', x.id) + '">' +
      '<span class="lz-pic"><img src="assets/img/lab/pub-' + esc(x.id) + '.svg" alt="" loading="lazy"><em>صورة تعبيرية</em></span>' +
      '<span class="lz-body"><span class="lz-top"><span class="lz-field">' + esc(x.field) + '</span><time>' + esc(x.date) + '</time></span>' +
      '<span class="lz-num">' + esc(x.finding[0]) + '</span><b>' + esc(x.title) + '</b>' +
      '<span class="lz-who">' + esc(x.lead) + (x.journal ? '<em>' + esc(x.journal) + '</em>' : '') + '</span>' +
      '<span class="lz-foot"><i>اقرأ الملخّص ←</i></span></span></a>';
  };

  // ---------- lab-all.html: الكل مع الفرز ----------
  var all = document.querySelector('[data-lab-all]');
  if (all) {
    var t = q.get('t') === 'pubs' ? 'pubs' : 'inv', items = L[t];
    var uniq = function (f) { return items.map(f).filter(function (v, i, a) { return a.indexOf(v) === i; }); };
    var groups = t === 'inv'
      ? [['medal', 'الميدالية', [['gold', 'ذهبية'], ['silver', 'فضية']]], ['event', 'المعرض', [['ITEX', 'ITEX ماليزيا'], ['Geneva', 'جنيف']]], ['year', 'السنة', uniq(function (x) { return x.year; }).sort().reverse().map(function (y) { return [String(y), ar(y)]; })]]
      : [['field', 'المجال', uniq(function (x) { return x.field; }).map(function (f) { return [f, f]; })], ['year', 'السنة', uniq(function (x) { return x.year; }).sort().reverse().map(function (y) { return [String(y), ar(y)]; })]];
    var state = { sort: 'new' }; groups.forEach(function (g) { state[g[0]] = ''; });
    document.title = (t === 'inv' ? 'كل الاختراعات والابتكارات' : 'كل المنشورات العلمية') + ' | إنجازات شباب عمان';
    all.querySelector('[data-lab-h]').textContent = t === 'inv' ? 'الاختراعات والابتكارات' : 'المنشورات العلمية';
    all.setAttribute('data-t', t);
    var sub = all.querySelector('.lb-all-hero p'); if (sub) sub.textContent = t === 'inv' ? 'ابتكارات عُمانية صنعت أثرًا ووصلت إلى منصات دولية — ' + ar(items.length) + ' ابتكارات موثّقة.' : 'أبحاث عُمانية منشورة تسهم في تقدّم المعرفة — ' + ar(items.length) + ' منشورات علمية.';
    var crumb = document.querySelector('.crumb-sub'); if (crumb) { crumb.textContent = t === 'inv' ? 'الاختراعات والابتكارات' : 'المنشورات العلمية'; crumb.parentNode.classList.add('has-sub'); }
    var f = all.querySelector('[data-lab-filters]');
    f.innerHTML = groups.map(function (g) {
      return '<div class="lb-fg" role="group" aria-label="' + g[1] + '"><span>' + g[1] + '</span>' +
        [['', 'الكل']].concat(g[2]).map(function (o) { return '<button type="button" data-k="' + g[0] + '" data-v="' + esc(o[0]) + '"' + (o[0] === '' ? ' aria-pressed="true"' : ' aria-pressed="false"') + '>' + esc(o[1]) + '</button>'; }).join('') + '</div>';
    }).join('') + '<div class="lb-fg" role="group" aria-label="الترتيب"><span>الترتيب</span><button type="button" data-k="sort" data-v="new" aria-pressed="true">الأحدث</button><button type="button" data-k="sort" data-v="old" aria-pressed="false">الأقدم</button></div>';
    var out = all.querySelector('[data-lab-out]'), cnt = all.querySelector('[data-lab-count]');
    var draw = function () {
      var list = items.filter(function (x) { return groups.every(function (g) { return !state[g[0]] || String(x[g[0]]) === state[g[0]]; }); })
        .sort(function (a, b) { return state.sort === 'new' ? b.year - a.year : a.year - b.year; });
      cnt.textContent = ar(list.length) + ' من ' + ar(items.length);
      var dk = true; /* نسخة المراجعة: بطاقات الكمبيوتر على الهاتف أيضًا */
      out.innerHTML = list.length ? '<div class="lb-grid' + (dk ? ' lz-grid lz-' + t : '') + '">' + list.map(dk ? (t === 'inv' ? invCardX : pubCardX) : (t === 'inv' ? invCard : pubCard)).join('') + '</div>' : '<p class="lb-empty">لا توجد نتائج بهذا الفرز.</p>';
    };
    f.addEventListener('click', function (e) {
      var b = e.target.closest('button'); if (!b) return;
      state[b.dataset.k] = b.dataset.v;
      [].forEach.call(f.querySelectorAll('[data-k="' + b.dataset.k + '"]'), function (x) { x.setAttribute('aria-pressed', x === b ? 'true' : 'false'); });
      draw();
    });
    draw();
  }

  // ---------- lab-item.html: صفحة الابتكار أو البحث ----------
  var it = document.querySelector('[data-lab-item]');
  if (it) {
    var tt = q.get('t') === 'pub' ? 'pub' : 'inv', x = (L[tt === 'pub' ? 'pubs' : 'inv']).filter(function (v) { return v.id === q.get('id'); })[0];
    if (!x) { it.innerHTML = '<p class="lb-empty">لم يُعثر على هذا الملف. <a href="science.html">العودة إلى المختبر</a></p>'; return; }
    document.title = x.title + ' | إنجازات شباب عمان';
    var crumb2 = document.querySelector('.crumb-sub'); if (crumb2) { crumb2.textContent = tt === 'pub' ? 'المنشورات العلمية' : 'الاختراعات والابتكارات'; crumb2.parentNode.classList.add('has-sub'); }
    var block = function (b) {
      if (b.h) return '<h2>' + esc(b.h) + '</h2>';
      if (b.p) return '<p>' + esc(b.p) + '</p>';
      if (b.ul) return '<ul>' + b.ul.map(function (li) { return '<li>' + esc(li) + '</li>'; }).join('') + '</ul>';
      if (b.q) return '<blockquote><p>' + esc(b.q) + '</p><cite>' + esc(b.by) + '</cite></blockquote>';
      if (b.img) return '<figure class="li-fig"><img src="' + esc(b.img[0]) + '" alt="' + esc(b.img[1]) + '" loading="lazy"><figcaption>' + esc(b.img[1]) + '</figcaption></figure>';
      return '';
    };
    var words = 0;
    if (tt === 'inv') {
      words = [x.lead].concat(x.body.map(function (b) { return b.p || b.q || (b.ul || []).join(' ') || b.h || ''; })).join(' ').split(/\s+/).length;
      it.innerHTML =
        '<header class="li-hero"><img src="' + esc(x.img) + '" alt="' + esc(x.imgAlt) + '"><div class="li-hero-t">' +
          '<span class="lb-medal ' + x.medal + '">' + esc(x.medalLabel) + ' · ' + esc(x.eventLabel) + '</span>' +
          '<h1>' + esc(x.title) + '</h1><p class="li-who"><b>' + esc(x.who) + '</b>' + (x.org ? ' · ' + esc(x.org) : '') + '</p>' +
          '<p class="li-meta"><span>' + esc(x.place) + '</span><span>' + esc(x.date) + '</span><span>' + (function (m) { return m === 1 ? 'دقيقة قراءة' : m === 2 ? 'دقيقتان قراءة' : ar(m) + ' دقائق قراءة'; })(Math.max(1, Math.round(words / 180))) + '</span></p></div></header>' +
        '<div class="li-wrap"><p class="li-lead">' + esc(x.lead) + '</p>' +
          '<aside class="li-facts"><h2>بطاقة الابتكار</h2><dl>' + x.facts.map(function (f) { return '<div><dt>' + esc(f[0]) + '</dt><dd>' + esc(f[1]) + '</dd></div>'; }).join('') + '</dl></aside>' +
          '<div class="li-body">' + x.body.map(block).join('') + '</div>' +
          '<p class="li-credit">الصور: <a href="' + esc(x.credit[1]) + '" target="_blank" rel="noopener">' + esc(x.credit[0]) + '</a></p>' +
          '<div class="li-src"><h2>المصادر</h2><ul>' + x.sources.map(function (s) { return '<li><a href="' + esc(s[1]) + '" target="_blank" rel="noopener">' + esc(s[0]) + '</a></li>'; }).join('') + '</ul></div>' +
          '<h2 class="li-more-h">ابتكارات أخرى</h2><div class="lb-grid">' + L.inv.filter(function (v) { return v.id !== x.id; }).slice(0, 4).map(invCard).join('') + '</div>' +
          '<a class="lb-more" href="lab-all.html?t=inv">كل الابتكارات <span aria-hidden="true">←</span></a></div>';
    } else {
      var cite = x.authors + ' (' + x.year + '). ' + x.title + '. ' + x.journal + '.';
      it.innerHTML =
        '<header class="li-hero li-hero-pub"><div class="li-hero-t"><span class="lb-field">' + esc(x.field) + ' · ' + esc(x.type) + '</span>' +
          '<h1>' + esc(x.title) + '</h1><p class="li-who">' + esc(x.authors) + '</p><p class="li-meta"><span>' + esc(x.journal) + '</span><span>' + esc(x.date) + '</span></p></div></header>' +
        '<div class="li-wrap"><div class="li-finding"><b>' + esc(x.finding[0]) + '</b><span>' + esc(x.finding[1]) + '</span></div>' +
          '<div class="li-body"><h2>ملخص البحث</h2><p>' + esc(x.summary) + '</p></div>' +
          '<button type="button" class="li-cite" data-cite="' + esc(cite) + '">نسخ المرجع</button>' +
          '<h2 class="li-more-h">منشورات أخرى</h2><div class="lb-grid">' + L.pubs.filter(function (v) { return v.id !== x.id; }).slice(0, 4).map(pubCard).join('') + '</div>' +
          '<a class="lb-more" href="lab-all.html?t=pubs">كل المنشورات <span aria-hidden="true">←</span></a></div>';
      var cb = it.querySelector('.li-cite');
      cb.addEventListener('click', function () { (navigator.clipboard ? navigator.clipboard.writeText(cb.dataset.cite) : Promise.reject()).then(function () { cb.textContent = 'نُسخ المرجع ✓'; }, function () { cb.textContent = cb.dataset.cite; }); });
    }
  }

  // ---------- science.html على الهاتف: تحت «المختبر» في الشريط العلوي اسم القسم الظاهر ----------
  var sc = document.querySelector('.crumb[data-page="المختبر"]');
  if (sc && document.querySelector('[data-lx-show]') && 'IntersectionObserver' in window) {
    sc.innerHTML = '<span class="crumb-title">المختبر</span><small class="crumb-sub"></small>'; sc.classList.add('has-sub');
    var subEl = sc.querySelector('.crumb-sub');
    var secs = [[document.querySelector('.lx-show'), 'الاختراعات والابتكارات'], [document.querySelector('.lx-table'), 'المنشورات العلمية']];
    var io = new IntersectionObserver(function () {
      var cur = '';
      secs.forEach(function (p) { if (p[0]) { var r = p[0].getBoundingClientRect(); if (r.top < innerHeight * .5 && r.bottom > 80) cur = p[1]; } });
      subEl.textContent = cur || 'الاختراعات والابتكارات';
    }, { threshold: [0, .25, .5, .75, 1] });
    secs.forEach(function (p) { if (p[0]) io.observe(p[0]); });
    addEventListener('scroll', function () { io.takeRecords(); var cur = ''; secs.forEach(function (p) { if (p[0]) { var r = p[0].getBoundingClientRect(); if (r.top < innerHeight * .5 && r.bottom > 80) cur = p[1]; } }); subEl.textContent = cur || 'الاختراعات والابتكارات'; }, { passive: true });
  }
})();
