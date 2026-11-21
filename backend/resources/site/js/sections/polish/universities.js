/* صفحة الجامعات والكليات — لمسات فخمة (تصاميم ٢٠٣٠): عدد فروع جامعة التقنية في شارة ذهبية، وظهور متتابع للمربعات والبطاقات. */
(function () {
  if (!document.querySelector('.u-page')) return;
  var AR = '٠١٢٣٤٥٦٧٨٩', ar = function (n) { return String(n).replace(/\d/g, function (d) { return AR[d]; }); };

  function dress() {
    document.querySelectorAll('.u-tech').forEach(function (t) {
      if (t.querySelector('.u-tech-count')) return;
      var n = t.querySelectorAll('.u-branches span').length, info = t.querySelector('.u-info');
      if (!n || !info) return;
      var c = document.createElement('span'); c.className = 'u-tech-count'; c.innerHTML = '<b>' + ar(n) + '</b> فرعًا في المحافظات';
      info.appendChild(c);
    });
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches || !('IntersectionObserver' in window)) return;
    var items = Array.prototype.slice.call(document.querySelectorAll('.u-spot, .u-tech, .u-card')).filter(function (e) { return !e.classList.contains('x-rv'); });
    var io = new IntersectionObserver(function (es) {
      var shown = 0;
      es.forEach(function (e) {
        if (!e.isIntersecting) return;
        e.target.style.setProperty('--x-d', (shown++ % 8) * 70 + 'ms');
        e.target.classList.add('x-in'); io.unobserve(e.target);
      });
    }, { threshold: .12, rootMargin: '0px 0px -40px 0px' });
    items.forEach(function (e) { e.classList.add('x-rv'); io.observe(e); });
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', dress); else dress();
  // بعد البحث أو الفلترة (Livewire يعيد رسم القائمة) تُلبَس البطاقات الجديدة أيضًا
  document.addEventListener('livewire:morphed', dress);
  document.addEventListener('livewire:navigated', dress);
})();
