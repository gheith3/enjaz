/* جوائز السلطان قابوس — لمسات معتمدة (٢٠٢٦-١١)، منقولة من معاينة «تصاميم جديدة». تعمل فقط في صفحتها. */
(function () {
  if (!document.querySelector('.aws-hero')) return;
/* جوائز السلطان قابوس — لمسات تعمل فوق سلوك الموقع دون تغييره */
(function () {
  var $ = function (s, r) { return (r || document).querySelector(s); };
  var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };
  var RM = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var fine = window.matchMedia('(pointer: fine)').matches;
  var add = function (parent, cls, html, tag) { var el = document.createElement(tag || 'span'); el.className = cls; el.setAttribute('aria-hidden', 'true'); if (html) el.innerHTML = html; parent.appendChild(el); return el; };
  var drop = function (el, ms) { setTimeout(function () { if (el.parentNode) el.parentNode.removeChild(el); }, ms); };

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

  // الواجهة: أشعة، ذرات ذهبية، عمق الصورة مع المؤشر، المجالان تباعًا
  var hero = $('.aws-hero');
  if (hero && !RM) {
    add(hero, 't-rays', '<span style="--l:8%;--rot:16deg;--d:15s"></span><span style="--l:20%;--rot:12deg;--d:11s;width:7%"></span><span style="--l:31%;--rot:19deg;--d:18s;width:9%"></span>');
    $$('.aws-hero-fields li').forEach(function (li, i) { li.classList.add('t-pop'); li.style.setProperty('--t-d', (900 + i * 140) + 'ms'); });
    var portrait = $('.aws-hero-portrait', hero);
    if (fine && portrait) hero.addEventListener('mousemove', function (e) {
      var r = hero.getBoundingClientRect(), x = (e.clientX - r.left) / r.width - .5, y = (e.clientY - r.top) / r.height - .5;
      portrait.style.setProperty('--hx', (x * -18).toFixed(1) + 'px'); portrait.style.setProperty('--hy', (y * -10).toFixed(1) + 'px');
    });
    var cv = add(hero, 't-dust', '', 'canvas'), ctx = cv.getContext('2d'), dpr = Math.min(2, window.devicePixelRatio || 1), W = 0, H = 0, pts = [];
    var size = function () {
      W = hero.clientWidth; H = hero.clientHeight; cv.width = W * dpr; cv.height = H * dpr; cv.style.width = W + 'px'; cv.style.height = H + 'px'; ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
      pts = []; for (var i = 0; i < Math.min(60, W / 24); i++) pts.push({ x: Math.random() * W, y: Math.random() * H, r: .5 + Math.random() * 1.7, v: .12 + Math.random() * .35, a: Math.random() * 6.3, o: .3 + Math.random() * .5 });
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

  // بطاقتا الجائزتين
  var io = ('IntersectionObserver' in window && !RM) ? new IntersectionObserver(function (es) {
    es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('t-show'); e.target.classList.remove('t-pre'); io.unobserve(e.target); } });
  }, { threshold: .15 }) : null;
  $$('.aws-card').forEach(function (card, i) {
    add(card, 't-ring'); var spot = add(card, 't-spot');
    if (io) { card.classList.add('t-pre'); card.style.setProperty('--t-d', (i * 140) + 'ms'); io.observe(card); }
    var go = $('.aws-go', card), art = $('.aws-podium', card) || $('.aws-art', card);
    card.addEventListener('mousemove', function (e) {
      var r = card.getBoundingClientRect(), x = (e.clientX - r.left) / r.width - .5, y = (e.clientY - r.top) / r.height - .5;
      spot.style.setProperty('--mx', (e.clientX - r.left) + 'px'); spot.style.setProperty('--my', (e.clientY - r.top) + 'px');
      if (!fine || RM) return;
      card.classList.add('t-moving');
      card.style.transform = 'translateY(-6px) rotateY(' + (x * 7).toFixed(2) + 'deg) rotateX(' + (-y * 6).toFixed(2) + 'deg)';
      if (go) { var g = go.getBoundingClientRect(); go.style.setProperty('--bx', ((e.clientX - g.left - g.width / 2) * .12).toFixed(1) + 'px'); go.style.setProperty('--by', ((e.clientY - g.top - g.height / 2) * .15).toFixed(1) + 'px'); }
    });
    card.addEventListener('mouseleave', function () {
      card.classList.remove('t-moving'); card.style.transform = '';
      if (go) { go.style.setProperty('--bx', '0px'); go.style.setProperty('--by', '0px'); }
    });
    // شرارات من الكأس عند الدخول
    if (art && !RM) card.addEventListener('mouseenter', function () {
      var r0 = card.getBoundingClientRect(), r = art.getBoundingClientRect(), cx = r.left - r0.left + r.width / 2, cy = r.top - r0.top + r.height * .45;
      for (var k = 0; k < 14; k++) {
        var s = add(card, 't-spark'), ang = -Math.PI / 2 + (Math.random() - .5) * 2.4, d = 40 + Math.random() * 80;
        s.style.left = cx + 'px'; s.style.top = cy + 'px';
        s.style.setProperty('--dx', (Math.cos(ang) * d).toFixed(0) + 'px'); s.style.setProperty('--dy', (Math.sin(ang) * d).toFixed(0) + 'px');
        s.style.animationDelay = (k * 25) + 'ms'; drop(s, 1600);
      }
    });
  });
})();

})();
