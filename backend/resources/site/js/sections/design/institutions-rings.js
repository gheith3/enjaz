// Ported from design/institutions.html (inline script, unchanged).
/* إحصائيات المؤسسات: عدّاد + شريط نسبة يمتلئ عند الظهور */
(function () {
  var box = document.querySelector('[data-orgs-pulse]'); if (!box) return;
  var ar = function (n) { return String(n).replace(/\d/g, function (d) { return '٠١٢٣٤٥٦٧٨٩'[d]; }); };
  var nums = box.querySelectorAll('[data-count]');
  function run() {
    box.classList.add('go');
    if (matchMedia('(prefers-reduced-motion: reduce)').matches) { nums.forEach(function (n) { n.textContent = ar(n.dataset.count); }); return; }
    nums.forEach(function (n, k) {
      var end = +n.dataset.count, t0 = null, dur = 1300, delay = k * 220;
      function step(t) {
        if (!t0) t0 = t;
        var p = Math.min(1, Math.max(0, (t - t0 - delay) / dur)), e = 1 - Math.pow(1 - p, 3);
        n.textContent = ar(Math.round(end * e));
        if (p < 1) requestAnimationFrame(step); else n.parentNode.classList.add('done');
      }
      requestAnimationFrame(step);
    });
  }
  if (!('IntersectionObserver' in window)) return run();
  var io = new IntersectionObserver(function (es) { if (es[0].isIntersecting) { io.disconnect(); run(); } }, { threshold: .6 });
  io.observe(box);
})();
