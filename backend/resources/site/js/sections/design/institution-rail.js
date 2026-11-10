// Ported from design/institution.html (inline script, unchanged).
/* مؤسسات من نفس المجال: تتحرك ذهاباً وإياباً بهدوء، وتتوقف عند اللمس */
(function () {
  var rail = document.querySelector('[data-org-rail]'); if (!rail) return;
  var view = rail.querySelector('.rail-view'), track = rail.querySelector('.rail-track');
  if (matchMedia('(prefers-reduced-motion: reduce)').matches || !track.animate) { view.classList.add('manual'); return; }
  var anim;
  function build() {
    var dist = track.scrollWidth - view.clientWidth;
    if (anim) anim.cancel();
    if (dist <= 4) return;
    anim = track.animate([{ transform: 'translateX(0)' }, { transform: 'translateX(' + dist + 'px)' }], { duration: Math.max(5000, dist * 26), direction: 'alternate', iterations: Infinity, easing: 'ease-in-out', delay: 700, endDelay: 900 });
  }
  build(); addEventListener('resize', build);
  ['pointerenter', 'touchstart', 'focusin'].forEach(function (e) { rail.addEventListener(e, function () { anim && anim.pause(); }, { passive: true }); });
  ['pointerleave', 'touchend', 'focusout'].forEach(function (e) { rail.addEventListener(e, function () { anim && anim.play(); }, { passive: true }); });
})();
