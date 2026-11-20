/* الإنجازات — لمسات معتمدة (٢٠٢٦-١١)، منقولة من معاينة «تصاميم جديدة». تعمل فقط في صفحتها. */
(function () {
  if (!document.querySelector('.ah2-hero')) return;
/* صفحة الإنجازات — لمسات احترافية تعمل فوق سلوك الموقع وLivewire دون تغييره */
(function () {
  var $ = function (s, r) { return (r || document).querySelector(s); };
  var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };
  var RM = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var fine = window.matchMedia('(pointer: fine)').matches;
  var add = function (parent, cls, html, tag) { var el = document.createElement(tag || 'span'); el.className = cls; el.setAttribute('aria-hidden', 'true'); if (html) el.innerHTML = html; parent.appendChild(el); return el; };
  var AR = '٠١٢٣٤٥٦٧٨٩', toAr = function (n) { return String(n).replace(/\d/g, function (d) { return AR[d]; }); };
  var fromAr = function (s) { return parseInt(String(s).replace(/[٠-٩]/g, function (d) { return AR.indexOf(d); }).replace(/\D/g, ''), 10); };

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

  // الواجهة: الجدار يتحرك بعمق مع المؤشر، وضوء يتبعه
  var hero = $('.ah2-hero'), wall = $('.ah2-wall');
  if (hero && fine && !RM) {
    var glow = add($('.ah2-media', hero) || hero, 't-glow');
    hero.addEventListener('mousemove', function (e) {
      var r = hero.getBoundingClientRect(), x = (e.clientX - r.left) / r.width, y = (e.clientY - r.top) / r.height;
      if (wall) { wall.style.setProperty('--wx', ((x - .5) * -22).toFixed(1) + 'px'); wall.style.setProperty('--wy', ((y - .5) * -12).toFixed(1) + 'px'); }
      glow.style.setProperty('--gx', (x * 100).toFixed(1) + '%'); glow.style.setProperty('--gy', (y * 100).toFixed(1) + '%');
    });
  }

  // البحث: اقتراحات تُكتب تلقائيًا، واختصار «/»
  var input = $('#achievements-q'), form = input && input.closest('form');
  if (input) {
    if (fine) add(form, 't-kbd').textContent = '/';
    addEventListener('keydown', function (e) {
      if (e.key === '/' && !/INPUT|TEXTAREA|SELECT/.test(document.activeElement.tagName)) { e.preventDefault(); input.focus(); }
    });
    if (!RM) {
      var base = input.getAttribute('placeholder') || 'ابحث باسم أو إنجاز';
      var ideas = ['جوخة الحارثية', 'علي الحبسي', 'باها حائل', 'معرض ITEX', 'أمير الشعراء', 'جائزة مان بوكر'];
      var wi = 0, ci = 0, del = false;
      var tick = function () {
        if (document.activeElement === input || input.value) { input.setAttribute('placeholder', base); setTimeout(tick, 1200); return; }
        var w = ideas[wi];
        ci += del ? -1 : 1;
        input.setAttribute('placeholder', 'ابحث عن: ' + w.slice(0, ci) + (del ? '' : '|'));
        var wait = del ? 35 : 85;
        if (!del && ci >= w.length) { del = true; wait = 1600; }
        else if (del && ci <= 0) { del = false; wi = (wi + 1) % ideas.length; wait = 350; }
        setTimeout(tick, wait);
      };
      setTimeout(tick, 1400);
    }
  }

  // لمعة وضوء يتبع المؤشر
  var light = function (el) {
    el.classList.add('t-host');
    var s = add(el, 't-shine');
    el.addEventListener('mousemove', function (e) { var r = el.getBoundingClientRect(); s.style.setProperty('--mx', (e.clientX - r.left) + 'px'); s.style.setProperty('--my', (e.clientY - r.top) + 'px'); });
  };
  // لوحة الشرف: إطار ذهبي دوّار
  $$('.honor-card').forEach(function (el) { light(el); add(el, 't-ring'); });

  // موجة ذهبية عند الضغط على التصنيفات والأزرار
  document.addEventListener('pointerdown', function (e) {
    var b = e.target.closest('.chip, .flt-btn, .flt-done, .flt-clear');
    if (!b || RM) return;
    var r = b.getBoundingClientRect(), rp = add(b, 't-ripple');
    rp.style.left = (e.clientX - r.left) + 'px'; rp.style.top = (e.clientY - r.top) + 'px';
    setTimeout(function () { if (rp.parentNode) rp.parentNode.removeChild(rp); }, 750);
  });

  // العدد: عدّ تصاعدي عند التحميل، ونبضة عند تغيّر النتائج
  var count = $('.res-count');
  function animateCount(el) {
    var txt = el.textContent, n = fromAr(txt); if (!n || RM) return;
    var word = txt.replace(/[\d٠-٩]+/, '').trim(), t0 = Date.now(), d = 900;
    el.__busy = true;
    (function step() {
      var p = Math.min(1, (Date.now() - t0) / d);
      el.textContent = toAr(Math.round(n * (1 - Math.pow(1 - p, 3)))) + ' ' + word;
      if (p < 1) setTimeout(step, 30); else { el.textContent = txt; setTimeout(function () { el.__busy = false; }, 0); }
    })();
  }
  if (count) {
    animateCount(count);
    new MutationObserver(function () { if (count.__busy) return; count.classList.remove('t-bump'); void count.offsetWidth; count.classList.add('t-bump'); })
      .observe(count, { childList: true, characterData: true, subtree: true });
  }

  // البطاقات: ظهور متتابع، خط ذهبي، هيكل تحميل، إمالة
  var io = ('IntersectionObserver' in window && !RM) ? new IntersectionObserver(function (es) {
    es.forEach(function (e) { if (!e.isIntersecting) return; e.target.classList.add('t-show'); e.target.classList.remove('t-pre'); io.unobserve(e.target); });
  }, { threshold: .08 }) : null;
  function enhance(root) {
    $$('.ach', root).forEach(function (card, i) {
      if (card.__t) return; card.__t = 1;
      add(card, 't-accent'); light(card);
      if (io) { card.classList.add('t-pre'); card.style.setProperty('--t-d', ((i % 4) * 80) + 'ms'); io.observe(card); }
      var img = $('.ach-img', card);
      if (img && !img.complete) {
        img.classList.add('t-loading');
        var done = function () { img.classList.remove('t-loading'); };
        img.addEventListener('load', done); img.addEventListener('error', done);
      }
      if (fine && !RM) {
        card.addEventListener('mousemove', function (e) {
          var r = card.getBoundingClientRect(), x = (e.clientX - r.left) / r.width - .5, y = (e.clientY - r.top) / r.height - .5;
          card.style.transition = 'transform .12s linear, box-shadow .6s';
          card.style.transform = 'perspective(900px) translateY(-6px) rotateY(' + (x * 5).toFixed(2) + 'deg) rotateX(' + (-y * 4).toFixed(2) + 'deg)';
        });
        card.addEventListener('mouseleave', function () { card.style.transition = ''; card.style.transform = ''; });
      }
    });
  }
  enhance(document);
  // Livewire يعيد رسم الشبكة عند الفلترة أو تغيير الصفحة
  var grid = $('.cards-ov'), results = $('[data-results]') || (grid && grid.parentNode);
  if (results) new MutationObserver(function () { enhance(results); }).observe(results, { childList: true, subtree: true });
})();

})();
