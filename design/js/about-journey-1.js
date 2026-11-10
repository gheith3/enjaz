/* من نحن — «خارطة العمل» على الكمبيوتر: طريق ذهبي بمحطات السنوات، والضغط على سنة يعرض محطاتها بطاقاتٍ بالصور والنص الكامل.
   تتقدم السنوات تلقائيًا حتى يتفاعل الزائر. البيانات من js/about-roadmap.js (window.INJAZ_ROADMAP). */
(function () {
  if (!matchMedia('(min-width: 1100px)').matches) return;
  var M = window.INJAZ_ROADMAP, sec = document.querySelector('#journey');
  if (!M || !sec) return;
  var IMG = sec.dataset.img || 'assets/img/';
  var esc = function (s) { var d = document.createElement('div'); d.innerHTML = s; return d.innerHTML; };
  var years = []; M.forEach(function (m) { if (years.indexOf(m.y) < 0) years.push(m.y); });
  var ar = function (n) { return String(n).replace(/\d/g, function (d) { return '٠١٢٣٤٥٦٧٨٩'[d]; }); };
  var word = function (n) { return n === 1 ? 'محطة واحدة' : n === 2 ? 'محطتان' : ar(n) + (n <= 10 ? ' محطات' : ' محطة'); };
  var box = document.createElement('div');
  box.className = 'jd';
  box.innerHTML = '<p class="jd-lead">من فكرةٍ صغيرة إلى منصّة وطنية — ' + word(M.length) + ' في ' + ar(years.length) + ' مراحل. اختر سنة لتتعرّف على محطاتها.</p>' +
    '<div class="jd-road" role="tablist" aria-label="سنوات الرحلة"><span class="jd-line" aria-hidden="true"><i></i></span>' +
    years.map(function (y, i) {
      var n = M.filter(function (m) { return m.y === y; }).length;
      return '<button type="button" role="tab" class="jd-st" aria-selected="' + (i ? 'false' : 'true') + '"' + (i ? ' tabindex="-1"' : '') + '><span class="jd-dot" aria-hidden="true"></span><b>' + y + '</b><small>' + word(n) + '</small><i class="jd-bar" aria-hidden="true"></i></button>';
    }).join('') + '</div>' +
    '<div class="jd-stage" aria-live="polite"></div>';
  var tl = sec.querySelector('[data-rm-tl]'); tl.parentNode.insertBefore(box, tl);
  var stage = box.querySelector('.jd-stage'), sts = box.querySelectorAll('.jd-st'), line = box.querySelector('.jd-line i');
  var cur = 0, timer = 0, DUR = 7000, reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  var show = function (k, focus) {
    cur = k;
    [].forEach.call(sts, function (b, i) { b.setAttribute('aria-selected', i === k ? 'true' : 'false'); b.tabIndex = i === k ? 0 : -1; b.classList.toggle('done', i < k); });
    line.style.width = (years.length > 1 ? k / (years.length - 1) * 100 : 0) + '%';
    var items = M.filter(function (m) { return m.y === years[k]; });
    stage.innerHTML = '<div class="jd-grid n' + Math.min(items.length, 4) + '">' + items.map(function (m, i) {
      return '<article class="jd-card" style="--d:' + (i * 70) + 'ms"><span class="jd-img"><img src="' + IMG + m.img + '" alt="" loading="lazy"><em>' + m.date + '</em></span><p>' + esc(m.t) + '</p></article>';
    }).join('') + '</div>';
    box.style.setProperty('--dur', DUR + 'ms');
    box.classList.remove('run'); void box.offsetWidth; if (timer) box.classList.add('run');
    if (focus) sts[k].focus();
  };
  var stop = function () { clearInterval(timer); timer = 0; box.classList.remove('run'); };
  [].forEach.call(sts, function (b, i) {
    b.addEventListener('click', function () { stop(); show(i); });
    b.addEventListener('keydown', function (e) { var d = e.key === 'ArrowLeft' ? 1 : e.key === 'ArrowRight' ? -1 : 0; if (d) { e.preventDefault(); stop(); show((i + d + sts.length) % sts.length, true); } });
  });
  stage.addEventListener('mouseenter', stop);
  if (!reduce) timer = setInterval(function () { show((cur + 1) % years.length); }, DUR);
  show(0);
})();
