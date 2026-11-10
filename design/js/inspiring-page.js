/* مسرح الأغلفة: الغلاف في المنتصف مستقيم وكبير، والجانبية مائلة للخلف (ثلاثي الأبعاد) */
(function () {
  var track = document.querySelector('.cf-track');
  if (!track) return;
  var items = Array.prototype.slice.call(track.querySelectorAll('.cf-item'));
  var cap = document.querySelector('.cf-cap');
  var nameEl = cap.querySelector('.cf-name'), fieldEl = cap.querySelector('.cf-field'), lineEl = cap.querySelector('.cf-line');
  var dotsWrap = document.querySelector('.cf-dots');
  var reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  var active = -1, raf = 0, timer = 0;

  // نقاط التنقل
  items.forEach(function (it, i) {
    var b = document.createElement('button');
    b.type = 'button'; b.setAttribute('aria-label', 'عرض ' + it.dataset.name);
    b.addEventListener('click', function () { go(i); stopAuto(); });
    dotsWrap.appendChild(b);
  });
  var dots = Array.prototype.slice.call(dotsWrap.children);

  function setCaption(i) {
    if (i === active) return;
    active = i;
    var it = items[i];
    dots.forEach(function (d, k) { d.setAttribute('aria-current', String(k === i)); });
    items.forEach(function (x, k) { x.setAttribute('aria-pressed', String(k === i)); });
    cap.classList.add('swap');
    setTimeout(function () {
      nameEl.textContent = it.dataset.name; fieldEl.textContent = it.dataset.field; lineEl.textContent = it.dataset.line;
      cap.classList.remove('swap');
    }, reduce ? 0 : 180);
  }

  function paint() {
    var tr = track.getBoundingClientRect(), mid = tr.left + tr.width / 2, best = 0, bestD = 1e9;
    items.forEach(function (it, i) {
      var r = it.getBoundingClientRect();
      var d = (r.left + r.width / 2 - mid) / r.width;           // المسافة عن المنتصف بعدد الأغلفة
      var a = Math.max(-2, Math.min(2, d)), ad = Math.abs(a);
      if (Math.abs(d) < bestD) { bestD = Math.abs(d); best = i; }
      if (reduce) return;
      it.style.transform = 'translateZ(' + (-ad * 90) + 'px) rotateY(' + (a * -38) + 'deg) scale(' + (1 - Math.min(ad, 1) * .12) + ')';
      it.style.opacity = String(1 - Math.min(ad, 2) * .28);
      it.style.filter = 'brightness(' + (1 - Math.min(ad, 1) * .35) + ')';
      it.style.zIndex = String(10 - Math.round(ad * 3));
    });
    setCaption(best);
  }
  track.addEventListener('scroll', function () { cancelAnimationFrame(raf); raf = requestAnimationFrame(paint); }, { passive: true });
  addEventListener('resize', paint);

  function go(i) {
    i = (i + items.length) % items.length;
    var it = items[i], tr = track.getBoundingClientRect(), r = it.getBoundingClientRect();
    track.scrollBy({ left: (r.left + r.width / 2) - (tr.left + tr.width / 2), behavior: reduce ? 'auto' : 'smooth' });
  }
  items.forEach(function (it, i) { it.addEventListener('click', function () { if (i !== active) { go(i); stopAuto(); } }); });
  document.querySelector('[data-cf="prev"]').addEventListener('click', function () { go(active - 1); stopAuto(); });
  document.querySelector('[data-cf="next"]').addEventListener('click', function () { go(active + 1); stopAuto(); });
  track.addEventListener('keydown', function (e) {
    if (e.key === 'ArrowLeft') { go(active + 1); stopAuto(); }      // في العربية: اليسار هو التالي
    if (e.key === 'ArrowRight') { go(active - 1); stopAuto(); }
  });

  // تشغيل تلقائي هادئ يتوقف عند أول تفاعل
  function stopAuto() { clearInterval(timer); timer = 0; }
  ['pointerdown', 'wheel', 'touchstart'].forEach(function (ev) { track.addEventListener(ev, stopAuto, { passive: true }); });
  if (!reduce) timer = setInterval(function () { go(active + 1); }, 3000);

  // البداية من منتصف القائمة حتى تظهر أغلفة على الجانبين
  var startAt = Math.floor(items.length / 2);
  requestAnimationFrame(function () {
    var it = items[startAt], tr = track.getBoundingClientRect(), r = it.getBoundingClientRect();
    track.scrollLeft += (r.left + r.width / 2) - (tr.left + tr.width / 2);
    paint();
  });
})();

/* فرز جدار الإلهام حسب المجال */
(function () {
  var bar = document.querySelector('[data-wall-filter]'), wall = document.querySelector('.wall');
  if (!bar || !wall) return;
  var chips = Array.prototype.slice.call(bar.querySelectorAll('.chip'));
  var tiles = Array.prototype.slice.call(wall.querySelectorAll('.tile'));
  chips.forEach(function (chip) {
    chip.addEventListener('click', function () {
      var f = chip.dataset.field;
      chips.forEach(function (c) { var on = c === chip; c.classList.toggle('active', on); c.setAttribute('aria-pressed', String(on)); });
      tiles.forEach(function (t) { var s = t.querySelector('.tile-b span'); t.hidden = f !== '*' && (!s || s.textContent.trim() !== f); });
      wall.classList.toggle('is-filtered', f !== '*');
    });
  });
})();
