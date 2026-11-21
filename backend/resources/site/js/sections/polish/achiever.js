
/* ===================== لمسات إبداعية على صفحة المنجز ===================== */
(function () {
  var RM = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var all = function (s) { return Array.prototype.slice.call(document.querySelectorAll(s)); };
  var pos = function (el) { if (getComputedStyle(el).position === 'static') el.style.position = 'relative'; };
  function go() {
    if (!document.querySelector('.profile-hero')) return;
    // ١) شريط تقدّم القراءة
    var bar = document.createElement('i'); bar.className = 'px-progress'; bar.setAttribute('aria-hidden', 'true'); document.body.appendChild(bar);
    var onScroll = function () { var h = document.documentElement.scrollHeight - innerHeight; bar.style.setProperty('--p', h > 0 ? Math.min(1, scrollY / h).toFixed(4) : 0); };
    addEventListener('scroll', onScroll, { passive: true }); onScroll();
    // ٢) لمعة على الاسم بلونه الأصلي
    var h1 = document.querySelector('.profile-hero h1');
    if (h1) { h1.style.setProperty('--hc', getComputedStyle(h1).color); h1.classList.add('px-shine'); }
    // ٣) ذرّات ضوء حول صورة المنجز
    var hero = document.querySelector('.profile-hero');
    if (hero && !RM) {
      var r = function (a, b) { return a + Math.random() * (b - a); }, d = document.createElement('span'); d.className = 'px-dust'; d.setAttribute('aria-hidden', 'true');
      for (var i = 0; i < 18; i++) d.insertAdjacentHTML('beforeend', '<i style="--x:' + r(2, 98).toFixed(1) + '%;--s:' + r(3, 7).toFixed(1) + 'px;--d:' + r(9, 17).toFixed(1) + 's;--dl:-' + r(0, 17).toFixed(1) + 's;--o:' + r(.35, .8).toFixed(2) + ';--sw:' + r(-30, 30).toFixed(0) + 'px"></i>');
      pos(hero); hero.appendChild(d);
    }
    // ٦) ضوء يتبع المؤشر
    all('.kc, .said-card, .rel-card').forEach(function (c) {
      pos(c); c.classList.add('px-spot');
      c.addEventListener('mousemove', function (e) { var b = c.getBoundingClientRect(); c.style.setProperty('--mx', (e.clientX - b.left) + 'px'); c.style.setProperty('--my', (e.clientY - b.top) + 'px'); });
    });
    if (RM || !('IntersectionObserver' in window)) return;
    // ٤ و٥) خط العناوين وظهور الأقسام
    var io = new IntersectionObserver(function (es) { es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('px-in'); io.unobserve(e.target); } }); }, { threshold: .15 });
    all('.sec-title h2').forEach(function (h) { pos(h); h.classList.add('px-line'); io.observe(h); });
    all('main > section.section').forEach(function (s, i) { if (i === 0) return; var b = s.getBoundingClientRect(); if (b.top < innerHeight) return; s.classList.add('px-rv'); io.observe(s); });
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', go); else go();
})();
