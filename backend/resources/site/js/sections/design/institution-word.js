// Ported from design/institution.html (inline script, unchanged).
/* كلمة الوزير (التصميم المعتمد ٥): الصورة تصعد إلى رأس الكلمة بجانب الاسم، ثم يظهر النص على مراحل */
(function () {
  var sec = document.querySelector('.d5:not(.lw)'); if (!sec) return;
  var art = sec.querySelector('.aisha-art'), img = sec.querySelector('.aisha-portrait');
  var btn = sec.querySelector('[data-word-more]'), text = sec.querySelector('.wc-text');
  var min = parseFloat(getComputedStyle(text).maxHeight) || 118;
  var PH = 98; // ارتفاع الصورة في رأس الكلمة
  function place() {
    if (sec.classList.contains('open')) return;
    // أبعاد ثابتة لا تتأثر بحركة دخول الصورة
    var H = sec.offsetHeight, W = sec.offsetWidth, aH = art.offsetHeight, y0 = H - aH;
    var ox = art.offsetLeft, iw = img.offsetWidth, ih = img.offsetHeight;
    var bx = ox + img.offsetLeft + iw / 2, by = img.offsetTop + ih; // منتصف أسفل الصورة
    var s = PH / ih, Tx = W - 34 - s * iw / 2, Ty = 18 + 16 + 112;  // تقف على خط رأس الكلمة
    sec.style.setProperty('--y0', y0 + 'px');
    sec.style.setProperty('--tx', (Tx - ox - s * (bx - ox)) + 'px');
    sec.style.setProperty('--ty', (Ty - s * by) + 'px');
    sec.style.setProperty('--s', s);
    sec.style.setProperty('--pw', Math.round(s * iw) + 'px');
  }
  place(); addEventListener('resize', place); addEventListener('load', place);
  // ارتفاع النص النهائي يُقاس على نسخة مخفية بالعرض الموسّع، فلا يقفز الطول أثناء الحركة
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
    btn.querySelector('span').textContent = open ? 'إخفاء' : 'اقرأ المزيد';
    if (!open) setTimeout(place, 1150);
  });
})();
