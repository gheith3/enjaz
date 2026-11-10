/* نفس إشارة الكمبيوتر تحت صور العرض على الهاتف. */
(function () {
  var hero = document.querySelector('.hero-majlis');
  var slot = document.querySelector('.home-hero-cue');
  if (!hero || !slot) return;
  var cue = hero.querySelector('.scroll-cue');
  if (!cue) return;
  var phone = matchMedia('(max-width: 699px)');
  function place() { (phone.matches ? slot : hero).appendChild(cue); }
  phone.addEventListener('change', place);
  place();
})();
