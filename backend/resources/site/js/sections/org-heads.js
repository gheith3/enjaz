// The leadership stage (components/site/organization/heads-stage, css org-heads) on ministries' and federations' pages.
// «من تعاقبوا على قيادتها»: the stage plays head after head once reached; the big year counts to each head's year and the
// gold line runs to his portrait. Keyboard focus pauses it; clicking a portrait restarts its three seconds.
document.querySelectorAll('[data-op-heads]').forEach(function (heads) {
  var slides = heads.querySelectorAll('.oh-slide'), dots = heads.querySelectorAll('.oh-dot');
  var yearEl = heads.querySelector('[data-oh-year]'), fill = heads.querySelector('[data-oh-fill]');
  var STEP = 3000, cur = 0, timer = null, shown = 0, hold = false, visible = false, started = false, countFrame = null;
  var still = matchMedia('(prefers-reduced-motion: reduce)').matches;
  var ar = function (n) { return String(n).replace(/\d/g, function (x) { return '٠١٢٣٤٥٦٧٨٩'[x]; }); };
  heads.style.setProperty('--oh-t', STEP + 'ms');
  var fitNames = function () {
    var names = [], size = Infinity;
    slides.forEach(function (slide) {
      var name = slide.querySelector('.oh-name');
      if (!name) return;
      names.push(name);
      name.style.removeProperty('font-size');
      var base = parseFloat(getComputedStyle(name).fontSize), available = name.parentElement.clientWidth;
      var range = document.createRange();
      range.selectNodeContents(name);
      var textWidth = range.getBoundingClientRect().width;
      size = Math.min(size, available && textWidth > available ? base * available / textWidth * .98 : base);
    });
    if (Number.isFinite(size)) names.forEach(function (name) { name.style.fontSize = size + 'px'; });
  };
  fitNames();
  if (document.fonts) document.fonts.ready.then(fitNames);
  if ('ResizeObserver' in window) new ResizeObserver(fitNames).observe(heads);
  else addEventListener('resize', fitNames);
  var countTo = function (to) {
    if (countFrame !== null) cancelAnimationFrame(countFrame);
    if (!yearEl) return;
    if (!to) { yearEl.textContent = ''; return; }
    var from = shown || to, t0 = null;
    if (still) { yearEl.textContent = ar(to); shown = to; return; }
    countFrame = requestAnimationFrame(function step(t) {
      if (!t0) t0 = t;
      var p = Math.min(1, (t - t0) / 900), e = 1 - Math.pow(1 - p, 3);
      shown = Math.round(from + (to - from) * e);
      yearEl.textContent = ar(shown);
      if (p < 1) countFrame = requestAnimationFrame(step);
    });
  };
  var show = function (n) {
    slides[cur].classList.remove('on'); slides[cur].setAttribute('aria-hidden', 'true');
    dots[cur].classList.remove('on'); dots[cur].setAttribute('aria-selected', 'false');
    cur = n;
    slides[cur].classList.add('on'); slides[cur].setAttribute('aria-hidden', 'false');
    dots[cur].classList.add('on'); dots[cur].setAttribute('aria-selected', 'true');
    countTo(parseInt(slides[cur].dataset.year, 10) || 0);
    if (fill) fill.style.setProperty('--p', dots.length > 1 ? cur / (dots.length - 1) : 1);
    var track = dots[cur].parentNode;
    if (track.scrollWidth > track.clientWidth) track.scrollTo({ left: dots[cur].offsetLeft - (track.clientWidth - dots[cur].offsetWidth) / 2, behavior: 'smooth' });
  };
  var play = function () {
    clearInterval(timer);
    var running = started && visible && !document.hidden && !still && !hold && slides.length > 1;
    heads.classList.toggle('is-playing', running);
    if (running) timer = setInterval(function () { show((cur + 1) % slides.length); }, STEP);
  };
  dots.forEach(function (d, n) {
    d.addEventListener('click', function (event) {
      hold = event.detail === 0;
      show(n); play();
    });
  });
  heads.addEventListener('focusin', function (event) {
    hold = event.target.matches(':focus-visible'); play();
  });
  heads.addEventListener('focusout', function (event) {
    if (!heads.contains(event.relatedTarget)) { hold = false; play(); }
  });
  document.addEventListener('visibilitychange', play);
  var start = function () { if (!started) { started = true; show(0); } play(); };
  if (!('IntersectionObserver' in window)) { visible = true; start(); }
  else {
    var ho = new IntersectionObserver(function (es) {
      es.forEach(function (entry) {
        visible = entry.isIntersecting;
        if (visible) start(); else play();
      });
    }, { threshold: 0.35 });
    ho.observe(heads);
  }
});
