// Ported from design/institutions.html (inline script, unchanged).
/* رأس المؤسسات: صور جلالة السلطان كاملة في إطار ذهبي، تتبدّل تلقائيًا ويمكن اختيارها من الأشرطة */
(function () {
  var hero = document.querySelector('[data-sultan-hero]'); if (!hero) return;
  var sl = hero.querySelectorAll('.fh-slide'), bars = hero.querySelectorAll('.fh-bar'), i = 0, timer;
  var still = matchMedia('(prefers-reduced-motion: reduce)').matches;
  function show(n) {
    sl[i].classList.remove('on'); bars[i].classList.remove('on'); bars[i].removeAttribute('aria-current');
    i = n; sl[i].classList.add('on'); bars[i].classList.add('on'); bars[i].setAttribute('aria-current', 'true');
  }
  function play() { clearInterval(timer); if (!still) timer = setInterval(function () { show((i + 1) % sl.length); }, 5500); }
  [].forEach.call(bars, function (b, n) { b.addEventListener('click', function () { if (n !== i) show(n); play(); }); });
  play();
})();
