/* هيدر «كلمات القيادة»: ثلاث شرائح تتبدّل كل ٥ ثوانٍ، مع أشرطة تنقل، وسحب بالإصبع، وإيقاف عند الوقوف بالفأرة.
   كل شريحة: الصورة تصعد، العنوان الذهبي يلمع، والكلمة تتكشّف كلمةً كلمة. ويضاف شعاع ضوء وذرّات ذهبية. */
(function () {
  var hero = document.querySelector('[data-majlis]'); if (!hero) return;
  var DUR = 5000;
  var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var slides = Array.prototype.slice.call(hero.querySelectorAll('.hm-slide'));
  function el(cls) { var d = document.createElement('div'); d.className = cls; d.setAttribute('aria-hidden', 'true'); hero.appendChild(d); return d; }

  el('hm-sheen'); el('hm-star');
  if (!reduce) {
    var dust = el('hm-dust'), n = window.innerWidth < 700 ? 12 : 22;
    for (var i = 0; i < n; i++) {
      var p = document.createElement('i'), s = 1.5 + Math.random() * 3;
      p.style.cssText = 'left:' + (Math.random() * 100).toFixed(1) + '%;--s:' + s.toFixed(1) + 'px;--d:' + (7 + Math.random() * 9).toFixed(1) + 's;--dl:' + (-Math.random() * 14).toFixed(1) + 's;--x:' + (Math.random() * 60 - 30).toFixed(0) + 'px;--o:' + (.35 + Math.random() * .5).toFixed(2);
      dust.appendChild(p);
    }
    // تقسيم كل كلمة إلى عنصر (النص الكامل يبقى لقارئات الشاشة)
    slides.forEach(function (sl) {
      var body = sl.querySelector('.hc-body'); if (!body) return;
      var txt = body.textContent.trim();
      body.setAttribute('aria-label', txt);
      body.innerHTML = txt.split(/\s+/).map(function (w, k) { return '<span class="w" aria-hidden="true" style="--i:' + k + '">' + w + '</span>'; }).join(' ');
    });
    hero.classList.add('hm-ready');
  }
  if (slides.length < 2) return;

  // أشرطة التنقل
  hero.style.setProperty('--hm-dur', DUR + 'ms');
  var dots = document.createElement('div'); dots.className = 'hm-dots';
  slides.forEach(function (sl, k) {
    var b = document.createElement('button'); b.type = 'button'; b.setAttribute('aria-label', sl.getAttribute('aria-label'));
    b.addEventListener('click', function () { go(k); });
    dots.appendChild(b);
  });
  hero.appendChild(dots);
  var btns = dots.querySelectorAll('button'), cur = 0, timer = 0, leaveT = 0, paused = false, started = 0, left = DUR, first = true;

  function show(k) {
    var prev = first ? -1 : cur; first = false;
    clearTimeout(leaveT);
    slides.forEach(function (sl, j) {
      var on = j === k;
      sl.classList.remove('is-leaving', 'is-entering');
      if (j === prev && prev !== k) sl.classList.add('is-leaving');
      if (on && prev > -1 && prev !== k) sl.classList.add('is-entering');
      sl.classList.toggle('is-active', on);
      sl.setAttribute('aria-hidden', on ? 'false' : 'true');
      if ('inert' in sl) sl.inert = !on;
      btns[j].setAttribute('aria-current', on ? 'true' : 'false');
      if (on) { btns[j].style.display = 'none'; void btns[j].offsetWidth; btns[j].style.display = ''; }
    });
    // القادمة تبدأ شفافة ثم تظهر تدريجيًا فوق الخارجة
    var inc = slides[k];
    if (inc.classList.contains('is-entering')) { void inc.offsetWidth; requestAnimationFrame(function () { inc.classList.remove('is-entering'); }); }
    leaveT = setTimeout(function () { slides.forEach(function (sl) { sl.classList.remove('is-leaving'); }); }, 1300);
    hero.classList.remove('hm-flash'); void hero.offsetWidth; hero.classList.add('hm-flash');
  }
  function go(k) { k = (k + slides.length) % slides.length; if (k === cur && !first) return schedule(DUR); show(k); cur = k; schedule(DUR); }
  function schedule(ms) { clearTimeout(timer); left = ms; started = Date.now(); if (!paused && !reduce) timer = setTimeout(function () { go(cur + 1); }, ms); }
  // الإيقاف المؤقت لا يعيد الشريحة: يكمل من حيث توقّف
  function pause(v) {
    if (v === paused) return; paused = v; hero.classList.toggle('is-paused', v);
    if (v) { clearTimeout(timer); left = Math.max(400, left - (Date.now() - started)); }
    else schedule(left);
  }

  // ?slide=2 يفتح على شريحة معيّنة (للمعاينة)
  var q = /[?&]slide=(\d)/.exec(location.search); cur = q ? Math.min(slides.length - 1, Math.max(0, q[1] - 1)) : 0;
  show(cur); schedule(DUR);

  hero.addEventListener('mouseenter', function () { pause(true); });
  hero.addEventListener('mouseleave', function () { pause(false); });
  document.addEventListener('visibilitychange', function () { pause(document.hidden); });

  // السحب بالإصبع (RTL: السحب لليسار = التالي)
  var x0 = null;
  hero.addEventListener('touchstart', function (e) { x0 = e.touches[0].clientX; }, { passive: true });
  hero.addEventListener('touchend', function (e) {
    if (x0 === null) return; var dx = e.changedTouches[0].clientX - x0; x0 = null;
    if (Math.abs(dx) > 40) go(cur + (dx < 0 ? 1 : -1));
  });
})();
