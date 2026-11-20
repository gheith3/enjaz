/* ملهمون من عُمان — لمسات معتمدة (٢٠٢٦-١١)، منقولة من معاينة «تصاميم جديدة». تعمل فقط في صفحتها. */
(function () {
  if (!document.querySelector('.ins-strip')) return;
/* ملهمون من عُمان — لمسة إبداعية تعمل فوق سلوك الموقع دون تغييره */
(function () {
  var $ = function (s, r) { return (r || document).querySelector(s); };
  var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };
  var RM = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var fine = window.matchMedia('(pointer: fine)').matches;
  var add = function (parent, cls, html, tag) { var el = document.createElement(tag || 'span'); el.className = cls; el.setAttribute('aria-hidden', 'true'); if (html) el.innerHTML = html; parent.appendChild(el); return el; };

  // التقدم وزر الأعلى والترويسة
  var bar = add(document.body, 't-progress', '<b></b>', 'div');
  var top = document.createElement('button'); top.type = 'button'; top.className = 't-top'; top.setAttribute('aria-label', 'العودة إلى أعلى الصفحة');
  top.innerHTML = '<svg viewBox="0 0 54 54" aria-hidden="true"><circle class="bg" cx="27" cy="27" r="24"/><circle class="fg" cx="27" cy="27" r="24"/></svg><i aria-hidden="true">↑</i>';
  document.body.appendChild(top);
  top.addEventListener('click', function () { window.scrollTo({ top: 0, behavior: RM ? 'auto' : 'smooth' }); });
  var header = $('.site-header');
  function onScroll() {
    var h = document.documentElement.scrollHeight - innerHeight, p = h > 0 ? Math.min(1, scrollY / h) : 0;
    bar.style.setProperty('--p', p.toFixed(4)); bar.style.setProperty('--show', p > .005 ? 1 : 0);
    top.style.setProperty('--p', p.toFixed(4)); top.classList.toggle('on', scrollY > innerHeight * .8);
    if (header) header.classList.toggle('t-scrolled', scrollY > 30);
  }
  addEventListener('scroll', onScroll, { passive: true }); addEventListener('resize', onScroll); onScroll();

  // الظهور
  var io = ('IntersectionObserver' in window && !RM) ? new IntersectionObserver(function (es) {
    es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('t-in'); io.unobserve(e.target); } });
  }, { threshold: .12 }) : null;
  var watch = function (el) { if (io) io.observe(el); else el.classList.add('t-in'); };
  $$('.ins-h, .hm-head h2').forEach(function (el) { el.classList.add('t-under'); watch(el); });
  $$('.wall, .hm-lead, .nominate').forEach(function (el, i) { el.classList.add('t-rv'); watch(el); });
  $$('.hm-item').forEach(function (el, i) { el.classList.add('t-slide'); el.style.setProperty('--t-d', (i * 120) + 'ms'); watch(el); });

  // الواجهة: الحقول تظهر تباعًا، الجدار بعمق، ذرات ذهبية، ونجوم تتبع المؤشر
  var hero = $('.ins-strip');
  if (hero && !RM) {
    $$('.is-fields li').forEach(function (li, i) { li.classList.add('t-pop'); li.style.setProperty('--t-d', (500 + i * 110) + 'ms'); });
    var wall = $('.is-photo-wall', hero);
    if (fine) {
      var last = 0;
      hero.addEventListener('mousemove', function (e) {
        var r = hero.getBoundingClientRect(), x = (e.clientX - r.left) / r.width - .5, y = (e.clientY - r.top) / r.height - .5;
        if (wall) { wall.style.setProperty('--wx', (x * -20).toFixed(1) + 'px'); wall.style.setProperty('--wy', (y * -12).toFixed(1) + 'px'); }
        var now = Date.now();
        if (now - last > 45) {
          last = now;
          var s = add(document.body, 't-trail'); s.style.left = e.clientX + 'px'; s.style.top = e.clientY + 'px';
          setTimeout(function () { if (s.parentNode) s.parentNode.removeChild(s); }, 950);
        }
      });
    }
    var cv = add(hero, 't-dust', '', 'canvas'), ctx = cv.getContext('2d'), dpr = Math.min(2, window.devicePixelRatio || 1), W = 0, H = 0, pts = [];
    var size = function () {
      W = hero.clientWidth; H = hero.clientHeight; cv.width = W * dpr; cv.height = H * dpr; cv.style.width = W + 'px'; cv.style.height = H + 'px'; ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
      pts = []; for (var i = 0; i < Math.min(50, W / 28); i++) pts.push({ x: Math.random() * W, y: Math.random() * H, r: .5 + Math.random() * 1.6, v: .12 + Math.random() * .35, a: Math.random() * 6.3, o: .3 + Math.random() * .5 });
    };
    size(); addEventListener('resize', size);
    var vis = true; if ('IntersectionObserver' in window) new IntersectionObserver(function (es) { vis = es[0].isIntersecting; }).observe(hero);
    (function tick() {
      if (vis) {
        ctx.clearRect(0, 0, W, H);
        pts.forEach(function (p) {
          p.y -= p.v; p.a += .01; p.x += Math.sin(p.a) * .25; if (p.y < -10) { p.y = H + 10; p.x = Math.random() * W; }
          ctx.fillStyle = 'rgba(244,216,156,' + (p.o * (.6 + Math.sin(p.a * 3) * .4)).toFixed(3) + ')'; ctx.beginPath(); ctx.arc(p.x, p.y, p.r, 0, 6.283); ctx.fill();
        });
      }
      requestAnimationFrame(tick);
    })();
  }

  // زر «اكتشف قصصهم» وزر الترشيح: مغناطيسيان
  if (fine && !RM) $$('.is-discover, .nominate .btn').forEach(function (el) {
    var host = el.closest('.nominate') || el;
    host.addEventListener('mousemove', function (e) {
      var r = el.getBoundingClientRect();
      el.style.setProperty('--bx', ((e.clientX - r.left - r.width / 2) * .25).toFixed(1) + 'px');
      el.style.setProperty('--by', ((e.clientY - r.top - r.height / 2) * .3).toFixed(1) + 'px');
    });
    host.addEventListener('mouseleave', function () { el.style.setProperty('--bx', '0px'); el.style.setProperty('--by', '0px'); });
  });

  // جدار الوجوه: ملصقات ثلاثية الأبعاد بعمق داخلي ولمعة
  $$('.wall .tile').forEach(function (t) {
    add(t, 't-glare');
    if (!fine || RM) return;
    t.addEventListener('mousemove', function (e) {
      var r = t.getBoundingClientRect(), x = (e.clientX - r.left) / r.width - .5, y = (e.clientY - r.top) / r.height - .5;
      t.classList.add('t-moving');
      t.style.transform = 'translateY(-8px) rotateY(' + (x * 12).toFixed(2) + 'deg) rotateX(' + (-y * 10).toFixed(2) + 'deg)';
      t.style.setProperty('--ix', (x * -14).toFixed(1) + 'px'); t.style.setProperty('--iy', (y * -10).toFixed(1) + 'px');
      t.style.setProperty('--gx', ((x + .5) * 100).toFixed(1) + '%'); t.style.setProperty('--gy', ((y + .5) * 100).toFixed(1) + '%');
    });
    t.addEventListener('mouseleave', function () { t.classList.remove('t-moving'); t.style.transform = ''; t.style.setProperty('--ix', '0px'); t.style.setProperty('--iy', '0px'); });
  });

  // الفرز: موجة على الزر وإعادة ظهور البطاقات الظاهرة تباعًا
  var filter = $('[data-wall-filter]');
  if (filter) filter.addEventListener('click', function (e) {
    var b = e.target.closest('.chip'); if (!b) return;
    if (!RM) {
      var r = b.getBoundingClientRect(), rp = add(b, 't-ripple'); rp.style.left = (e.clientX - r.left) + 'px'; rp.style.top = (e.clientY - r.top) + 'px';
      setTimeout(function () { if (rp.parentNode) rp.parentNode.removeChild(rp); }, 750);
    }
    setTimeout(function () {
      var i = 0;
      $$('.wall .tile').forEach(function (t) {
        t.classList.remove('t-shuffle');
        if (t.offsetParent === null || RM) return;
        void t.offsetWidth; t.style.setProperty('--t-d', (i++ * 60) + 'ms'); t.classList.add('t-shuffle');
      });
    }, 20);
  });

  // همم بلا حدود: إطار ذهبي دوّار حول القصة الرئيسية
  $$('.hm-lead').forEach(function (el) { add(el, 't-ring'); });

  // رشّح ملهمًا: إطار متحرك وقصاصات ذهبية عند المرور
  var nom = $('.nominate');
  if (nom) {
    var svg = add(nom, 't-ants', '<rect x="1" y="1" rx="22" ry="22" width="calc(100% - 2px)" height="calc(100% - 2px)"/>', 'span');
    svg.outerHTML = '<svg class="t-ants" aria-hidden="true"><rect x="1" y="1" rx="22" ry="22" width="99.6%" height="98.6%"/></svg>';
    if (!RM) nom.addEventListener('mouseenter', function () {
      var plus = $('.plus', nom), r0 = nom.getBoundingClientRect(), r = plus ? plus.getBoundingClientRect() : r0;
      var cx = r.left - r0.left + r.width / 2, cy = r.top - r0.top + r.height / 2, colors = ['#E6C98A', '#FFF1CC', '#0C7F89', '#B98E48', '#91C46C'];
      for (var i = 0; i < 16; i++) {
        var c = add(nom, 't-confetti'), ang = Math.random() * 6.283, d = 40 + Math.random() * 70;
        c.style.left = cx + 'px'; c.style.top = cy + 'px'; c.style.background = colors[i % colors.length];
        c.style.setProperty('--dx', (Math.cos(ang) * d).toFixed(0) + 'px'); c.style.setProperty('--dy', (Math.sin(ang) * d).toFixed(0) + 'px'); c.style.setProperty('--dr', (Math.random() * 360).toFixed(0) + 'deg');
        (function (el) { setTimeout(function () { if (el.parentNode) el.parentNode.removeChild(el); }, 1050); })(c);
      }
    });
  }
})();

})();
