// Leader's word on university pages (design js/leader-word.js reveal() + wire(), unchanged). The markup is rendered
// by the server (components/site/organization/word-d5, variant "leader"); this only animates it.
(function () {
  function reveal(sec) {
    var portrait = sec.querySelector('.aisha-portrait');
    if (!('IntersectionObserver' in window) || matchMedia('(prefers-reduced-motion: reduce)').matches) { sec.classList.add('is-visible'); return; }
    var start = function () {
      sec.classList.add('motion-ready');
      var io = new IntersectionObserver(function (e) {
        if (!e.some(function (x) { return x.isIntersecting; })) return;
        sec.classList.add('is-visible'); io.disconnect();
      }, { threshold: 0.2 });
      io.observe(sec);
    };
    if (portrait.complete) start(); else { portrait.addEventListener('load', start, { once: true }); portrait.addEventListener('error', start, { once: true }); }
  }

  // «اقرأ المزيد»: الصورة تصعد إلى رأس الكلمة بجانب الاسم (نفس منطق صفحة المؤسسة)
  function wire(sec) {
    var art = sec.querySelector('.aisha-art'), img = sec.querySelector('.aisha-portrait');
    var btn = sec.querySelector('[data-word-more]'), text = sec.querySelector('.wc-text');
    var min = parseFloat(getComputedStyle(text).maxHeight) || 118;
    var PH = 98;
    function place() {
      if (sec.classList.contains('open') || !img.offsetHeight) return;
      var H = sec.offsetHeight, W = sec.offsetWidth, aH = art.offsetHeight, y0 = H - aH;
      var ox = art.offsetLeft, iw = img.offsetWidth, ih = img.offsetHeight;
      var bx = ox + img.offsetLeft + iw / 2, by = img.offsetTop + ih;
      var s = PH / ih, Tx = W - 34 - s * iw / 2, Ty = 18 + 16 + 112;
      sec.style.setProperty('--y0', y0 + 'px');
      sec.style.setProperty('--tx', (Tx - ox - s * (bx - ox)) + 'px');
      sec.style.setProperty('--ty', (Ty - s * by) + 'px');
      sec.style.setProperty('--s', s);
      sec.style.setProperty('--pw', Math.round(s * iw) + 'px');
    }
    place(); addEventListener('resize', place); addEventListener('load', place); img.addEventListener('load', place);
    function fullHeight() {
      var c = sec.cloneNode(true);
      c.classList.add('open', 'd5-measure'); c.removeAttribute('aria-labelledby');
      c.style.cssText = 'position:absolute;visibility:hidden;pointer-events:none;left:0;right:0;top:0';
      c.querySelectorAll('[id]').forEach(function (el) { el.removeAttribute('id'); });
      var t = c.querySelector('.wc-text'); t.style.maxHeight = 'none';
      sec.parentNode.appendChild(c); var h = t.scrollHeight; c.remove();
      return h;
    }
    btn.addEventListener('click', function () {
      place();
      var open = !sec.classList.contains('open');
      var h = open ? fullHeight() : min;
      sec.classList.add('is-visible', 'settled');
      sec.classList.toggle('open', open);
      text.style.maxHeight = h + 'px';
      btn.setAttribute('aria-expanded', String(open));
      btn.querySelector('span').textContent = open ? (sec.dataset.less || 'إخفاء') : (sec.dataset.more || 'اقرأ المزيد');
      if (!open) setTimeout(place, 1150);
    });
  }

  document.querySelectorAll('.d5.lw').forEach(function (sec) { reveal(sec); wire(sec); });
})();
