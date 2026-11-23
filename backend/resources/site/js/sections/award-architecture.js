/* جائزة بلعرب (bilarab-award.html): the motion part of design js/bha-final.js (the content is rendered by the server):
   numbers count up when the page opens and sections appear when scrolled to. */
(function () {
  var root = document.querySelector('[data-bf]');
  if (!root) return;
  var ar = function (n) { return String(n).replace(/\d/g, function (d) { return '٠١٢٣٤٥٦٧٨٩'[d]; }); };
  var reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;

  if (!reduce) root.querySelectorAll('[data-to]').forEach(function (el, k) {
    var to = +el.dataset.to, t0 = performance.now() + 300 + k * 150, dur = 1700;
    el.textContent = ar(0);
    (function step(t) { var p = Math.max(0, Math.min(1, (t - t0) / dur)), e = 1 - Math.pow(1 - p, 3); el.textContent = ar(Math.round(to * e)); if (p < 1) requestAnimationFrame(step); })(performance.now());
  });

  root.classList.add('js');
  var els = [].slice.call(root.querySelectorAll('.c-head, .bf-idea, .bf-radial, .c-word, .bf-cy, .c-card'));
  if (reduce || !('IntersectionObserver' in window)) els.forEach(function (e) { e.classList.add('in'); });
  else { var io = new IntersectionObserver(function (es) { es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } }); }, { threshold: .2 }); els.forEach(function (e) { io.observe(e); }); }
})();
