// «ديوان المؤسسات» (site/pages/organizations, css orgs-diwan): the hero's photos follow each other — the next one fades in
// while the previous keeps its slow approach until it has faded out — every 6.5 s, or when its bar is chosen. The autoplay
// stops with reduced motion and pauses while the page is hidden. (The ledger counters are sections/design/institutions-rings.)
(function () {
  var hero = document.querySelector('[data-od-hero]');
  if (!hero) return;
  var slides = hero.querySelectorAll('.od-slide'), bars = hero.querySelectorAll('.od-bar');
  if (slides.length < 2) return;
  var STEP = 6500, i = 0, timer = null;
  var still = matchMedia('(prefers-reduced-motion: reduce)').matches;
  hero.style.setProperty('--od-t', STEP + 'ms');

  function show(n) {
    if (n === i) return;
    var prev = slides[i];
    prev.classList.remove('on'); prev.classList.add('off');
    setTimeout(function () { if (!prev.classList.contains('on')) prev.classList.remove('off'); }, 1500);
    bars[i] && (bars[i].classList.remove('on'), bars[i].removeAttribute('aria-current'));
    i = n;
    slides[i].classList.remove('off'); slides[i].classList.add('on');
    var img = slides[i].querySelector('img');
    if (img && img.loading === 'lazy') img.loading = 'eager';
    bars[i] && (bars[i].classList.add('on'), bars[i].setAttribute('aria-current', 'true'));
  }
  function play() {
    clearInterval(timer);
    if (!still && !document.hidden) timer = setInterval(function () { show((i + 1) % slides.length); }, STEP);
  }
  bars.forEach(function (bar, n) { bar.addEventListener('click', function () { show(n); play(); }); });
  document.addEventListener('visibilitychange', play);
  // The next photo is fetched ahead so it never fades in half-loaded.
  setTimeout(function () { var next = slides[1].querySelector('img'); if (next) next.loading = 'eager'; }, 1200);
  play();
})();
