/* «مسيرتي» — الصفّان الصاعدان: المحطات في صفّين من البطاقات المتصاعدة يربطهما خط متوهّج،
   والمحطة الحالية تتحوّل إلى الذهبي وتتنقّل تلقائيًا.
   البيانات من <ol class="gj-list"><li data-icon="book"><span class="gj-t">النص</span><b class="gj-y">السنة</b></li></ol>
   (النسخ السابقة: profile-journey.photo.js · profile-journey.art.js) */
(function () {
  var box = document.querySelector('[data-journey-gold]');
  if (!box) return;
  var reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
  var ar = function (v) { return String(v).replace(/\d/g, function (d) { return '٠١٢٣٤٥٦٧٨٩'[d]; }); };
  var IC = {
    book: '<path d="M3 6c3-1.5 6-1.5 9 0v13c-3-1.5-6-1.5-9 0zM12 6c3-1.5 6-1.5 9 0v13c-3-1.5-6-1.5-9 0z"/>',
    cap: '<path d="M2 9l10-5 10 5-10 5zM6 11v5c3 2.5 9 2.5 12 0v-5M22 9v6"/>',
    doc: '<path d="M6 3h9l3 3v15H6zM9 9h6M9 13h6M9 17h4"/>',
    feather: '<path d="M20 4C12 4 6 10 6 18l-2 2M6 18h7c4-3 7-8 7-14M9 15h5M11 11h5"/>',
    uni: '<path d="M3 9l9-5 9 5zM5 9v8M9 9v8M15 9v8M19 9v8M3 20h18M4 17h16"/>',
    trophy: '<path d="M7 4h10v5a5 5 0 0 1-10 0zM7 6H4c0 3 1.5 4.5 3.5 5M17 6h3c0 3-1.5 4.5-3.5 5M12 14v4M8 21h8M9 18h6"/>',
    star: '<path d="M12 3l2.7 5.6 6.1.8-4.5 4.2 1.1 6.1L12 16.8 6.6 19.7l1.1-6.1L3.2 9.4l6.1-.8z"/>',
    ball: '<circle cx="12" cy="12" r="9"/><path d="M12 7.5l3.8 2.8-1.4 4.5H9.6L8.2 10.3zM12 3v4.5M20.6 9.4l-4.8.9M17.3 19.3l-2.9-4.5M6.7 19.3l2.9-4.5M3.4 9.4l4.8.9"/>',
    plane: '<path d="M21 15.5 13.5 11V5.5a1.5 1.5 0 0 0-3 0V11L3 15.5v2l7.5-2.3V19l-2 1.5V22l3.5-1 3.5 1v-1.5l-2-1.5v-3.8l7.5 2.3z"/>',
    medal: '<path d="M8 3h8l-2.5 6h-3zM12 9.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11z"/><path d="M12 12.5l1 2 2.2.3-1.6 1.5.4 2.2-2-1-2 1 .4-2.2-1.6-1.5 2.2-.3z"/>',
    museum: '<path d="M3 9l9-5 9 5zM5 9v8M9 9v8M15 9v8M19 9v8M3 20h18M4 17h16M10.5 6.5h3"/>'
  };
  var list = box.querySelector('.gj-list');
  var items = [].slice.call(list.querySelectorAll('li'));
  var N = items.length; if (!N) return;

  // تجهيز البطاقات: أيقونة + نص + شريط السنة
  items.forEach(function (li, i) {
    li.insertAdjacentHTML('afterbegin', '<span class="gj-ic" aria-hidden="true"><svg viewBox="0 0 24 24">' + (IC[li.dataset.icon] || IC.book) + '</svg></span>');
    var y = li.querySelector('.gj-y'); y.textContent = ar(y.textContent.trim());
    li.tabIndex = 0;
    li.style.setProperty('--d', (.2 + i * .06).toFixed(2) + 's');
  });
  // تقسيم المحطات إلى صفّين متصاعدين
  var half = Math.ceil(N / 2), tops = [124, 318];
  [items.slice(0, half), items.slice(half)].forEach(function (group, r) {
    var row = document.createElement('ol');
    row.className = 'gj-row'; row.style.top = tops[r] + 'px';
    group.forEach(function (li, j) { li.style.setProperty('--h', (128 + j * 9) + 'px'); row.appendChild(li); });
    box.appendChild(row);
  });
  list.remove();

  // الخط المتوهّج تحت كل صف
  var line = function () {
    var old = box.querySelector('.gj-line'); if (old) old.remove();
    var pts = items.map(function (li) { var r = li.parentNode; return { x: r.offsetLeft + li.offsetLeft + li.offsetWidth / 2, y: r.offsetTop + li.offsetTop + li.offsetHeight + 9 }; });
    var seg = function (a, z) { return pts.slice(a, z).map(function (p, i) { return (i ? 'L' : 'M') + p.x.toFixed(1) + ' ' + p.y.toFixed(1); }).join(' '); };
    box.insertAdjacentHTML('afterbegin', '<svg class="gj-line" viewBox="0 0 ' + box.offsetWidth + ' ' + box.offsetHeight + '" aria-hidden="true">'
      + '<defs><linearGradient id="gjLn" gradientUnits="userSpaceOnUse" x1="0" y1="0" x2="' + box.offsetWidth + '" y2="0"><stop offset="0" stop-color="#0E4A4C"/><stop offset=".5" stop-color="#C79049"/><stop offset="1" stop-color="#EBAE5F"/></linearGradient></defs>'
      + '<path d="' + seg(0, half) + '"/><path d="' + seg(half, N) + '"/>'
      + pts.map(function (p) { return '<circle cx="' + p.x.toFixed(1) + '" cy="' + p.y.toFixed(1) + '" r="3"/>'; }).join('') + '</svg>');
    dots = [].slice.call(box.querySelectorAll('.gj-line circle'));
    if (k >= 0) dots.forEach(function (c, i) { c.classList.toggle('on', i === k); });
  };
  var dots = [], k = -1;
  var set = function (i) {
    k = (i + N) % N;
    items.forEach(function (li, j) { li.classList.toggle('on', j === k); li.classList.toggle('past', j < k); });
    dots.forEach(function (c, j) { c.classList.toggle('on', j === k); });
  };
  line();
  var rw = box.offsetWidth;
  window.addEventListener('resize', function () { if (box.offsetWidth !== rw) { rw = box.offsetWidth; line(); } });

  // التنقّل التلقائي ما دام القسم ظاهرًا، والضغط ينقل لمحطة بعينها
  var timer = null, seen = false, started = false;
  var stop = function () { clearInterval(timer); timer = null; };
  var play = function () { stop(); if (!reduce && seen) timer = setInterval(function () { set(k + 1); }, 2800); };
  items.forEach(function (li, i) {
    li.addEventListener('click', function () { set(i); play(); });
    li.addEventListener('keydown', function (e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); set(i); play(); } });
  });
  var check = function () {
    var r = box.getBoundingClientRect(), vh = window.innerHeight;
    var vis = r.top < vh * .8 && r.bottom > vh * .2;
    if (vis === seen) return; seen = vis;
    // صورة المنجز تنبثق من يسار المربع في كل مرة يصل فيها الزائر إلى القسم
    if (vis) { box.classList.remove('ph-go'); void box.offsetWidth; box.classList.add('ph-go'); }
    if (!vis) { stop(); return; }
    if (!started) {
      started = true; box.classList.add('in');
      setTimeout(function () { set(0); play(); }, reduce ? 0 : 1100);
    } else play();
  };
  window.addEventListener('scroll', check, { passive: true });
  check(); setTimeout(check, 400);
})();
