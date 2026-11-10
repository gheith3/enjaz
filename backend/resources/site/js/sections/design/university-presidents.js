// University presidents: autoplay, click, drag and scroll-snap (design js/university.js, unchanged logic).
// The ribbon and cards are rendered by the server.
(function () {
  var root = document.querySelector('[data-uni-page]');
  if (!root || !root.querySelector('#u-pres .ur')) return;
  var $ = function (s) { return root.querySelector(s); };
  var P = root.querySelectorAll('#u-pres .up-card[data-p]');
  if (!P.length) return;
    var rail = $('[data-u-pres]');
    var activePresident = 0, presidentTimer, manual = false, dragged = false;
    var pausePresidents = function () { manual = true; clearInterval(presidentTimer); };
    var pick = function (i, scroll) {
      activePresident = i;
      [].forEach.call(root.querySelectorAll('#u-pres [data-p]'), function (el) { el.classList.toggle('is-on', +el.dataset.p === i); });
      var c = rail.querySelector('.up-card[data-p="' + i + '"]');
      // سحب الصف أفقيًا فقط حتى تتوسط البطاقة (يعمل مع اتجاه الصفحة العربي)
      if (scroll && c && rail.scrollWidth > rail.clientWidth) rail.scrollBy({ left: c.getBoundingClientRect().left + c.offsetWidth / 2 - (rail.getBoundingClientRect().left + rail.clientWidth / 2), behavior: 'smooth' });
    };
    $('#u-pres').addEventListener('click', function (e) { var t = e.target.closest('[data-p]'); if (t && !dragged) { pausePresidents(); pick(+t.dataset.p, true); } });
    $('#u-pres').addEventListener('keydown', function (e) { var t = e.target.closest('.up-card'); if (t && (e.key === 'Enter' || e.key === ' ')) { e.preventDefault(); pausePresidents(); pick(+t.dataset.p, true); } });
    // الانتقال التلقائي يتوقف عند اختيار بطاقة أو بدء السحب اليدوي.
    pick(0, false);
    presidentTimer = setInterval(function () {
      if (!document.hidden) pick((activePresident + 1) % P.length, true);
    }, 3000);
    rail.addEventListener('pointerdown', function () { pausePresidents(); dragged = false; });
    rail.addEventListener('wheel', pausePresidents, { passive: true });
    rail.addEventListener('dragstart', function (e) { e.preventDefault(); });
    var dragStart = null;
    rail.addEventListener('pointerdown', function (e) {
      if (e.pointerType !== 'mouse' || e.button !== 0) return;
      dragStart = { x: e.clientX, left: rail.scrollLeft, id: e.pointerId };
    });
    rail.addEventListener('pointermove', function (e) {
      if (!dragStart) return;
      var delta = e.clientX - dragStart.x;
      if (Math.abs(delta) > 5) {
        dragged = true;
        rail.setPointerCapture(e.pointerId);
        rail.style.scrollSnapType = 'none';
        rail.style.userSelect = 'none';
      }
      if (dragged) rail.scrollLeft = dragStart.left - delta;
    });
    var endDrag = function () {
      dragStart = null;
      rail.style.scrollSnapType = '';
      rail.style.userSelect = '';
      setTimeout(function () { dragged = false; }, 0);
    };
    window.addEventListener('pointerup', endDrag);
    rail.addEventListener('pointercancel', endDrag);
    var scrollTimer;
    rail.addEventListener('scroll', function () {
      if (!manual) return;
      clearTimeout(scrollTimer);
      scrollTimer = setTimeout(function () {
        var center = rail.getBoundingClientRect().left + rail.clientWidth / 2;
        var nearest = 0, distance = Infinity;
        [].forEach.call(rail.children, function (card, i) {
          var r = card.getBoundingClientRect(), d = Math.abs(r.left + r.width / 2 - center);
          if (d < distance) { distance = d; nearest = i; }
        });
        pick(nearest, false);
      }, 150);
    }, { passive: true });
})();
