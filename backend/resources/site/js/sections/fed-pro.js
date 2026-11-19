// Federation page (site/organizations/federation, css federation-pro): «أبرز المحطات» — every stop's card is shown at once and a
// ball rolls along the pitch from stop to stop, lighting each card in turn (every 3 s once reached, pauses on hover).
(function () {
  var once = function (el, fn) {
    if (!('IntersectionObserver' in window)) return fn();
    var io = new IntersectionObserver(function (es) { if (es.some(function (x) { return x.isIntersecting; })) { io.disconnect(); fn(); } }, { threshold: 0.25 });
    io.observe(el);
  };

  var pres = document.querySelector('[data-fpr]');
  if (pres) {
    once(pres, function () { pres.classList.add('is-in'); });
    var segs = pres.querySelectorAll('.fpr-bar i'), cards = pres.querySelectorAll('.fpr-card');
    var light = function (k, on) { [segs[k], cards[k]].forEach(function (x) { if (x) x.classList.toggle('lit', on); }); };
    cards.forEach(function (c, k) {
      c.addEventListener('pointerenter', function () { light(k, true); });
      c.addEventListener('pointerleave', function () { light(k, false); });
    });
  }

  var ms = document.querySelector('[data-fms]');
  if (ms) {
    var stops = ms.querySelectorAll('.fms-stop'), items = ms.querySelectorAll('.fms-card');
    var ball = ms.querySelector('.fms-ball'), path = ms.querySelector('.fms-path i');
    var n = stops.length, cur = n - 1, timer = null, hold = false, turns = 0;
    var still = matchMedia('(prefers-reduced-motion: reduce)').matches;
    var go = function (k) {
      stops[cur].classList.remove('on'); stops[cur].setAttribute('aria-selected', 'false');
      items[cur].classList.remove('on');
      turns += (k - cur) * 220;
      cur = k;
      stops[cur].classList.add('on'); stops[cur].setAttribute('aria-selected', 'true');
      items[cur].classList.add('on');
      var p = n > 1 ? cur / (n - 1) : 0;
      ball.style.right = 'calc(50% / ' + n + ' + ' + p + ' * (100% - 100% / ' + n + '))';
      ball.style.setProperty('--r', -turns + 'deg');
      path.style.setProperty('--p', p);
      ball.classList.remove('bounce'); void ball.offsetWidth; ball.classList.add('bounce');
    };
    var play = function () { clearInterval(timer); if (!still && !hold) timer = setInterval(function () { go((cur + 1) % n); }, 3000); };
    stops.forEach(function (s, k) { s.addEventListener('click', function () { go(k); play(); }); });
    ms.addEventListener('pointerenter', function () { hold = true; play(); });
    ms.addEventListener('pointerleave', function () { hold = false; play(); });
    go(0); play();   // moves every 3 s from the start
    once(ms, function () { ms.querySelector('.fms-pitch').classList.add('is-in'); });
  }
})();
