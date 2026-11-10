/* هيدر «ملهمون من عُمان»: ألواح متجاورة؛ اللوح النشط يتّسع ويتبدّل تلقائيًا كل بضع ثوانٍ، أو بالضغط/السحب/الأسهم. */
(function () {
  var hero = document.querySelector('.ins-strip');
  if (!hero) return;
  var panels = Array.prototype.slice.call(hero.querySelectorAll('.is-panel'));
  var wrap = hero.querySelector('.is-panels');
  var reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  var DUR = 4500, cur = 0, timer = 0, visible = true;
  hero.style.setProperty('--is-dur', DUR + 'ms');

  function show(i) {
    cur = (i + panels.length) % panels.length;
    panels.forEach(function (p, k) {
      var on = k === cur;
      p.classList.toggle('is-on', on);
      p.classList.remove('run');
      p.setAttribute('aria-pressed', String(on));
    });
    void panels[cur].offsetWidth;
    panels[cur].classList.add('run');
    schedule();
  }
  function schedule() {
    clearTimeout(timer);
    if (!visible || reduce) return;
    timer = setTimeout(function () { show(cur + 1); }, DUR);
  }

  panels.forEach(function (p, i) { p.addEventListener('click', function () { if (i !== cur) show(i); else show(cur + 1); }); });
  wrap.addEventListener('keydown', function (e) {
    if (e.key === 'ArrowLeft') { e.preventDefault(); show(cur + 1); panels[cur].focus(); }   // في العربية: اليسار هو التالي
    if (e.key === 'ArrowRight') { e.preventDefault(); show(cur - 1); panels[cur].focus(); }
  });
  // سحب أفقي على الهاتف
  var sx = null;
  wrap.addEventListener('touchstart', function (e) { sx = e.touches[0].clientX; }, { passive: true });
  wrap.addEventListener('touchend', function (e) {
    if (sx === null) return;
    var dx = e.changedTouches[0].clientX - sx; sx = null;
    if (Math.abs(dx) > 40) show(cur + (dx > 0 ? 1 : -1));
  }, { passive: true });
  // يتوقف حين يخرج من الشاشة
  if ('IntersectionObserver' in window) {
    new IntersectionObserver(function (en) {
      visible = en[0].isIntersecting; hero.classList.toggle('paused', !visible);
      if (visible) show(cur); else clearTimeout(timer);
    }).observe(hero);
  }
  show(0);
})();
