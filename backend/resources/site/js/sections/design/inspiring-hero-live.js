/* «لحظة كل ملهم»: جدار الصور يتحرك كما هو، وكل ٣ ثوانٍ تقريبًا صورة ظاهرة تتحرر من اللون الفيروزي
   وتتقدّم بإطار ذهبي وعليها اسم الملهم ومجاله، وتضيء شارة مجاله في النص. الكمبيوتر فقط. */
(function () {
  var hero = document.querySelector('.ins-strip'); if (!hero) return;
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return; var phone = window.matchMedia('(max-width: 1099px)').matches; /* نسخة المراجعة: الهاتف أيضًا */
  // The server marks each tile with its person (data-name, data-field); the design kept the list here.
  var who = [];
  var tiles = Array.prototype.slice.call(hero.querySelectorAll('.is-photo-tile'));
  var chips = Array.prototype.slice.call(hero.querySelectorAll('.is-fields li'));
  tiles.forEach(function (t) {
    if (!t.dataset.name) return;
    var k = -1;
    for (var i = 0; i < who.length; i++) if (who[i][1] === t.dataset.name) { k = i; break; }
    if (k < 0) { k = who.length; who.push(['', t.dataset.name, t.dataset.field || '']); }
    t.dataset.k = k;
    var c = document.createElement('span'); c.className = 'is-live-cap'; c.setAttribute('aria-hidden', 'true');
    var b = document.createElement('b'), f = document.createElement('span');
    b.textContent = who[k][1]; f.textContent = who[k][2]; c.appendChild(b); c.appendChild(f); t.appendChild(c);
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
