/* تصاميم ٢٠٤٠ (معتمد) — صفحة المنجزين: لوحتا «منجز الأسبوع» بعد البنر بثانية + لمسة الهولوغرام */
(function () {
  var sec = document.querySelector('.week-duo'), hero = document.querySelector('.ap-hero');
  if (!sec) return;
  var cards = [].slice.call(sec.querySelectorAll('.week-card'));
  cards.forEach(function (c) {
    ['x-holo', 'x-flash', 'x-ring'].forEach(function (k) { var i = document.createElement('i'); i.className = k; i.setAttribute('aria-hidden', 'true'); c.appendChild(i); });
  });
  var shown = false;
  var show = function () {
    if (shown) return; shown = true;
    sec.classList.add('x-in');
    // نعيد تشغيل حركة اللوحة الداخلية (الخريطة، الصورة، الاسم) لتبدأ مع ظهورها لا قبله
    setTimeout(function () { cards.forEach(function (c) { c.classList.add('motion'); c.classList.remove('is-live'); void c.offsetWidth; c.classList.add('is-live'); }); }, 120);
  };
  var later = function () { setTimeout(show, 1000); };
  if (!hero || !hero.classList.contains('ap-intro') || hero.classList.contains('ap-go')) later();
  else new MutationObserver(function (m, o) { if (hero.classList.contains('ap-go')) { o.disconnect(); later(); } }).observe(hero, { attributes: true, attributeFilter: ['class'] });
  setTimeout(show, 2600);   // حدّ أقصى

  if (!matchMedia('(hover: hover) and (pointer: fine)').matches || matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  cards.forEach(function (c) {
    var raf = 0;
    c.addEventListener('pointermove', function (e) {
      var r = c.getBoundingClientRect(), x = (e.clientX - r.left) / r.width - .5, y = (e.clientY - r.top) / r.height - .5;
      cancelAnimationFrame(raf);
      raf = requestAnimationFrame(function () {
        c.classList.add('x-tilt');
        c.style.setProperty('--x-rx', (-y * 5).toFixed(2) + 'deg'); c.style.setProperty('--x-ry', (x * 6).toFixed(2) + 'deg');
        c.style.setProperty('--x-hx', ((x + .5) * 100).toFixed(1) + '%');
      });
    });
    c.addEventListener('pointerleave', function () { cancelAnimationFrame(raf); c.classList.remove('x-tilt'); });
  });
})();
