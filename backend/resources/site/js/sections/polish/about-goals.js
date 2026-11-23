/* ===================== تصاميم ٢٠٥٠ (معتمد) — «أهدافنا»: ظهور متتابع وتوهّج يتبع الماوس ===================== */
(function () {
  var card = document.getElementById('vmv-goals');
  if (!card) return;
  var show = function () { card.classList.add('g-seen', 'seen'); };
  if (!('IntersectionObserver' in window) || matchMedia('(prefers-reduced-motion: reduce)').matches) return show();
  new IntersectionObserver(function (es, ob) { if (es[0].isIntersecting) { show(); ob.disconnect(); } }, { threshold: .2 }).observe(card);
  // نفس توهّج بطاقات الرسالة والرؤية: يتبع موضع الماوس
  card.addEventListener('pointermove', function (e) {
    var r = card.getBoundingClientRect();
    card.style.setProperty('--mx', (e.clientX - r.left) + 'px');
    card.style.setProperty('--my', (e.clientY - r.top) + 'px');
  });
})();
