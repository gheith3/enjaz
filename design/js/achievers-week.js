/* حركة «منجز الأسبوع»: تبدأ عند ظهور المربع، وميل خفيف مع الفأرة على الكمبيوتر */
(function () {
  [].forEach.call(document.querySelectorAll('.week-card'), function (card) {
  if (matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  card.classList.add('motion');
  var start = function () { card.classList.add('is-live'); };
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (es) {
      if (es.some(function (e) { return e.isIntersecting; })) { start(); io.disconnect(); }
    }, { threshold: .35 });
    io.observe(card);
  } else start();

  if (!matchMedia('(hover: hover) and (pointer: fine)').matches) return;
  var raf = 0;
  card.addEventListener('pointermove', function (e) {
    var r = card.getBoundingClientRect();
    var x = ((e.clientX - r.left) / r.width) * 2 - 1, y = ((e.clientY - r.top) / r.height) * 2 - 1;
    cancelAnimationFrame(raf);
    raf = requestAnimationFrame(function () { card.style.setProperty('--mx', x.toFixed(3)); card.style.setProperty('--my', y.toFixed(3)); });
  });
  card.addEventListener('pointerleave', function () { card.style.setProperty('--mx', 0); card.style.setProperty('--my', 0); });
  });
})();
