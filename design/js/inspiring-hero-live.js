/* «لحظة كل ملهم»: جدار الصور يتحرك كما هو، وكل ٣ ثوانٍ تقريبًا صورة ظاهرة تتحرر من اللون الفيروزي
   وتتقدّم بإطار ذهبي وعليها اسم الملهم ومجاله، وتضيء شارة مجاله في النص. الكمبيوتر فقط. */
(function () {
  var hero = document.querySelector('.ins-strip'); if (!hero) return;
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return; var phone = window.matchMedia('(max-width: 1099px)').matches; /* نسخة المراجعة: الهاتف أيضًا */
  var who = [['mohammed', 'محمد المشايخي', 'الرياضة'], ['safiya', 'صفية البهلاني', 'الفن'], ['laith', 'ليث الكندي', 'القرآن الكريم'], ['zainab', 'زينب الحراصي', 'العلم']];
  var tiles = Array.prototype.slice.call(hero.querySelectorAll('.is-photo-tile'));
  var chips = Array.prototype.slice.call(hero.querySelectorAll('.is-fields li'));
  tiles.forEach(function (t) {
    var img = t.querySelector('img'), src = img ? img.getAttribute('src') : '';
    for (var i = 0; i < who.length; i++) if (src.indexOf(who[i][0]) > -1) { t.dataset.k = i; break; }
    if (t.dataset.k === undefined) return;
    var c = document.createElement('span'); c.className = 'is-live-cap'; c.setAttribute('aria-hidden', 'true');
    c.innerHTML = '<b>' + who[t.dataset.k][1] + '</b><span>' + who[t.dataset.k][2] + '</span>'; t.appendChild(c);
  });
  var last = -1, cur = null, timer = 0;
  function pick() {
    var h = hero.getBoundingClientRect(), wall = hero.querySelector('.is-photo-wall').getBoundingClientRect();
    var ok = tiles.filter(function (t) {
      if (t.dataset.k === undefined || +t.dataset.k === last) return false;
      var r = t.getBoundingClientRect();
      if (phone) return r.top > h.top + 6 && r.bottom < h.top + h.height * .4 && r.left > h.left + 2 && r.right < h.right - 2;
      return r.top > h.top + 30 && r.bottom < h.bottom - 30 && r.left > h.left + 10 && r.right < wall.left + wall.width * .8;
    });
    return ok.length ? ok[Math.floor(Math.random() * ok.length)] : null;
  }
  function step() {
    var t = pick();
    // الهاتف: إن لم تُوجد صورة مناسبة الآن (الجدار يتحرك) نعيد البحث بعد لحظة بدل انتظار ٣ ثوانٍ
    if (!t && phone) { timer = setTimeout(step, 250); return; }
    if (cur) cur.classList.remove('is-live');
    if (t) {
      t.classList.add('is-live'); cur = t; last = +t.dataset.k;
      var field = who[last][2];
      chips.forEach(function (li) { li.classList.toggle('is-on', li.textContent.trim() === field); });
    }
    timer = setTimeout(step, 3000);
  }
  setTimeout(step, phone ? 500 : 1200);
  document.addEventListener('visibilitychange', function () { clearTimeout(timer); if (!document.hidden) timer = setTimeout(step, 800); });
})();
