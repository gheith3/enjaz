/* المختبر على الكمبيوتر:
   ١) «قاعة العرض»: ابتكار واحد في بقعة ضوء كبيرة، يتبدّل تلقائيًا كل ٦ ثوانٍ (يتوقف عند مرور الفأرة)،
      وتحته «شرائح العيّنات» المرقّمة للتنقل بينها.
   ٢) «جدول المعرفة»: المنشورات كخانات جدول دوري، لكل خانة رقم ورمز المجال والنتيجة البارزة.
   البيانات: js/lab-data.js */
(function () {
  var L = window.LAB; if (!L) return;
  var esc = function (s) { return String(s == null ? '' : s).replace(/[&<>"]/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]; }); };
  var ar = function (n) { return String(n).replace(/\d/g, function (d) { return '٠١٢٣٤٥٦٧٨٩'[d]; }); };
  var pad = function (n) { return ar(n); };
  var url = function (t, id) { return 'lab-item.html?t=' + t + '&id=' + encodeURIComponent(id); };
  var byNew = function (a, b) { return b.year - a.year; };

  // ---------- ١. قاعة العرض ----------
  var show = document.querySelector('[data-lx-show]');
  if (show) {
    var inv = L.inv.slice().sort(byNew), n = inv.length;
    show.innerHTML =
      '<div class="lx-stage">' +
        '<div class="lx-pics">' + inv.map(function (x, i) {
          return '<img src="' + esc(x.img) + '" alt="' + esc(x.imgAlt || '') + '" class="' + (i ? '' : 'on') + '" loading="' + (i ? 'lazy' : 'eager') + '">';
        }).join('') + '<span class="lx-scan" aria-hidden="true"></span></div>' +
        '<div class="lx-info">' + inv.map(function (x, i) {
          return '<article class="lx-item' + (i ? '' : ' on') + '" aria-hidden="' + (i ? 'true' : 'false') + '">' +
            '<div class="lx-count"><b>' + pad(i + 1) + '</b><span>/ ' + pad(n) + '</span></div>' +
            '<div class="lx-meta"><span class="lx-medal ' + x.medal + '">' + esc(x.medalLabel) + '</span><span>' + esc(x.eventLabel) + '</span><span>' + esc(x.place || '') + '</span></div>' +
            '<h3>' + esc(x.short || x.title) + '</h3>' +
            '<div class="lx-who"><b>' + esc(x.who) + '</b><span>' + esc(x.org || '') + (x.field ? ' · ' + esc(x.field) : '') + '</span></div>' +
            '<a class="lx-go" href="' + url('inv', x.id) + '" tabindex="' + (i ? '-1' : '0') + '">اقرأ قصة الابتكار <span aria-hidden="true">←</span></a>' +
          '</article>';
        }).join('') + '</div>' +
      '</div>' +
      '<div class="lx-slides" role="tablist" aria-label="الابتكارات">' + inv.map(function (x, i) {
        return '<button type="button" role="tab" class="lx-slide' + (i ? '' : ' on') + '" aria-selected="' + (i ? 'false' : 'true') + '">' +
          '<span class="lx-sn">' + pad(i + 1) + '</span><img src="' + esc(x.img) + '" alt="" loading="lazy"><span class="lx-st">' + esc(x.short || x.title) + '<small>' + esc(x.who) + '</small></span><i class="lx-bar"></i></button>';
      }).join('') + '</div>' +
      '<a class="lx-big-all" href="lab-all.html?t=inv"><span>كل الابتكارات</span><small>' + ar(n) + ' ابتكارات موثّقة</small><i aria-hidden="true">←</i></a>';

    var pics = show.querySelectorAll('.lx-pics img'), items = show.querySelectorAll('.lx-item'), slides = show.querySelectorAll('.lx-slide');
    var cur = 0, timer = 0, DUR = 6000, reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
    var go = function (k) {
      cur = (k + n) % n;
      [pics, items, slides].forEach(function (list) { [].forEach.call(list, function (el, i) { el.classList.toggle('on', i === cur); }); });
      [].forEach.call(items, function (el, i) { el.setAttribute('aria-hidden', i === cur ? 'false' : 'true'); el.querySelector('.lx-go').tabIndex = i === cur ? 0 : -1; });
      [].forEach.call(slides, function (el, i) { el.setAttribute('aria-selected', i === cur ? 'true' : 'false'); });
      show.style.setProperty('--dur', DUR + 'ms');
      show.classList.remove('run'); void show.offsetWidth; if (!reduce && !show.matches(':hover')) show.classList.add('run');
    };
    var play = function () { clearInterval(timer); if (reduce) return; timer = setInterval(function () { go(cur + 1); }, DUR); show.classList.add('run'); };
    var stop = function () { clearInterval(timer); show.classList.remove('run'); };
    [].forEach.call(slides, function (b, i) { b.addEventListener('click', function () { go(i); }); });
    show.addEventListener('mouseenter', stop); show.addEventListener('mouseleave', function () { go(cur); play(); });
    show.addEventListener('focusin', stop);
    go(0); play();
  }

  // ---------- ٢. جدول المعرفة ----------
  var table = document.querySelector('[data-lx-table]');
  if (table) {
    var sym = { 'العلوم': 'عل', 'الطبي': 'طب', 'الطب': 'طب', 'الهندسة': 'هن', 'التقنية': 'تق', 'البيئة': 'بي', 'الزراعة': 'زر' };
    var pubs = L.pubs.slice().sort(byNew);
    table.innerHTML = '<div class="lx-grid">' + pubs.map(function (x, i) {
      return '<a class="lx-el" data-f="' + esc(x.field) + '" href="' + url('pub', x.id) + '">' +
        '<span class="lx-pic"><img src="assets/img/lab/pub-' + esc(x.id) + '.svg" alt="" loading="lazy"><em>صورة تعبيرية</em></span>' +
        '<span class="lx-no">' + pad(i + 1) + '</span><span class="lx-yr">' + ar(x.year) + '</span>' +
        '<span class="lx-num">' + esc(x.finding[0]) + '</span>' +
        '<b>' + esc(x.title) + '</b>' +
        '<span class="lx-by">' + esc(x.lead) + (x.journal ? '<small>' + esc(x.journal) + '</small>' : '') + '</span>' +
      '</a>';
    }).join('') + '</div>' +
      '<a class="lx-big-all" href="lab-all.html?t=pubs"><span>كل المنشورات</span><small>' + ar(pubs.length) + ' منشورات علمية</small><i aria-hidden="true">←</i></a>';
  }
})();
