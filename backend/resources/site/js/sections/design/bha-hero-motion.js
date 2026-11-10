/* Ported from design js/bha-hero-motion.js (unchanged). */
/* هيدر جائزة بلعرب — الحركة المعتمدة (٨ نوفمبر): الإطار + ضوء الشمس + كتل الشعار تنبثق وتصعد (شفافية ١٠٪).
   النماذج القديمة تبقى للمقارنة عبر ?fx=1..5 */
(function () {
  var m = /[?&]fx=(\d)/.exec(location.search);
  var fx = m ? +m[1] : 0;
  var hero = document.querySelector('[data-bf] .c-hero'); if (!hero) return;
  function R(a, b) { return a + Math.random() * (b - a); }
  var bm = document.createElement('div'); bm.className = 'bm'; bm.setAttribute('aria-hidden', 'true');
  var img = hero.querySelector('.bf-hero-img');
  hero.insertBefore(bm, img ? img.nextSibling : hero.firstChild);
  hero.classList.add('fx' + fx);

  if (fx === 1) {
    // لمعة تعبر الشعار (طبقة بنفس شكله فوقه تمامًا) والشعار يطفو بهدوء
    var mark = hero.querySelector('.c-mark'), inn = hero.querySelector('.c-hero-in'); if (!mark || !inn) return;
    var g = document.createElement('span'); g.className = 'bm-glint'; g.setAttribute('aria-hidden', 'true'); inn.appendChild(g);
    var put = function () {
      var cs = getComputedStyle(mark), r = mark.getBoundingClientRect(), p = inn.getBoundingClientRect();
      var pl = parseFloat(cs.paddingLeft), pr = parseFloat(cs.paddingRight), pt = parseFloat(cs.paddingTop), pb = parseFloat(cs.paddingBottom), bl = parseFloat(cs.borderLeftWidth), br = parseFloat(cs.borderRightWidth);
      g.style.left = (r.left - p.left + pl + bl) + 'px'; g.style.top = (r.top - p.top + pt) + 'px';
      g.style.width = (r.width - pl - pr - bl - br) + 'px'; g.style.height = (r.height - pt - pb) + 'px';
    };
    put(); window.addEventListener('resize', put); setTimeout(put, 800);
    hero.classList.add('fx1'); mark.closest('.c-hero').classList.add('fx1');
  }
  if (fx === 2) {
    bm.classList.add('right');
    var n = window.innerWidth < 700 ? 9 : 16;
    for (var i = 0; i < n; i++) {
      var s = R(18, 60), b = document.createElement('i'); b.className = 'bm-block';
      b.style.cssText = 'left:' + R(0, 95).toFixed(1) + '%;--y:' + R(5, 80).toFixed(0) + '%;--s:' + s.toFixed(0) + 'px;--h:' + (s * R(.6, 1.8)).toFixed(0) + 'px;--d:' + R(12, 22).toFixed(1) + 's;--dl:' + R(-22, 0).toFixed(1) + 's;--o:' + R(.5, 1).toFixed(2) + ';--r:' + R(-20, 20).toFixed(0) + 'deg';
      bm.appendChild(b);
    }
    var fit = function () { hero.style.setProperty('--fy', (hero.offsetHeight + 120) + 'px'); };
    fit(); window.addEventListener('resize', fit);
  }
  // كتل الشعار: أشكاله الأربعة (نصفا حرف B، العمودان المائلان، المثلث) تنبثق من الأسفل وتصعد عشوائيًا
  function pieces() {
    var shapes = [['0 0 160 100', 'M0 0H110A50 50 0 0 1 110 100H0Z', 1.6], ['0 0 40 100', 'M0 0H40V100L0 84Z', .4], ['0 0 46 100', 'M0 18L46 0V100L0 82Z', .46], ['0 0 100 90', 'M50 0L100 90H0Z', 1.1]];
    var lay = document.createElement('div'); lay.className = 'bm'; lay.setAttribute('aria-hidden', 'true');
    lay.innerHTML = '<svg width="0" height="0" style="position:absolute"><defs><linearGradient id="bmBronze" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#D49B6E"/><stop offset="1" stop-color="#9C5B3A"/></linearGradient></defs></svg>';
    hero.insertBefore(lay, bm);
    var n = window.innerWidth < 700 ? 14 : 26;
    for (var i = 0; i < n; i++) {
      var sh = shapes[i % shapes.length], h = R(16, 46), w = h * sh[2];
      var e = document.createElement('span'); e.className = 'bm-piece';
      e.style.cssText = 'left:' + R(-2, 98).toFixed(1) + '%;--y:' + R(5, 85).toFixed(0) + '%;width:' + w.toFixed(0) + 'px;height:' + h.toFixed(0) + 'px;--d:' + R(11, 20).toFixed(1) + 's;--dl:' + R(-20, 0).toFixed(1) + 's;--r0:' + R(-30, 30).toFixed(0) + 'deg;--r:' + R(-120, 120).toFixed(0) + 'deg;--x:' + R(-50, 50).toFixed(0) + 'px';
      e.innerHTML = '<svg viewBox="' + sh[0] + '" preserveAspectRatio="none"><path d="' + sh[1] + '" fill="url(#bmBronze)"/></svg>';
      lay.appendChild(e);
    }
    var fit = function () { hero.style.setProperty('--fy', (hero.offsetHeight + 120) + 'px'); };
    fit(); window.addEventListener('resize', fit);
  }
  if (fx === 0) { bm.innerHTML = '<div class="bm-sun"></div>'; pieces(); }

  if (fx === 3 || fx === 0) {
    var inner = hero.querySelector('.c-hero-in');
    var box = document.createElement('div'); box.className = 'bm';
    box.style.cssText = 'inset:-14px -18px;overflow:visible';
    box.innerHTML = '<svg class="bm-frame" preserveAspectRatio="none" viewBox="0 0 100 100"><rect pathLength="1" x="0" y="0" width="100" height="100" rx="1.5"/></svg>';
    if (fx === 3) bm.remove();
    // إطار حول المحتوى نفسه
    var holder = document.createElement('div'); holder.setAttribute('aria-hidden', 'true');
    holder.style.cssText = 'position:absolute;pointer-events:none;z-index:2';
    holder.appendChild(box); hero.appendChild(holder);
    var place = function () {
      var r = inner.getBoundingClientRect(), h = hero.getBoundingClientRect(), kids = inner.children, minL = 1e9, maxR = -1e9, minT = 1e9, maxB = -1e9;
      for (var k = 0; k < kids.length; k++) { var c = kids[k].getBoundingClientRect(); if (!c.width) continue; minL = Math.min(minL, c.left); maxR = Math.max(maxR, c.right); minT = Math.min(minT, c.top); maxB = Math.max(maxB, c.bottom); }
      holder.style.left = (minL - h.left) + 'px'; holder.style.top = (minT - h.top) + 'px';
      holder.style.width = (maxR - minL) + 'px'; holder.style.height = (maxB - minT) + 'px';
    };
    place(); window.addEventListener('resize', place); setTimeout(place, 600);
  }
  if (fx === 4) {
    bm.innerHTML = '<div class="bm-sun"></div>';
  }
  if (fx === 5) {
    function arch(x, w, top, bottom) { var r = w / 2; return 'M' + (x - r) + ' ' + bottom + ' V' + (top + r) + ' A' + r + ' ' + r + ' 0 0 1 ' + (x + r) + ' ' + (top + r) + ' V' + bottom; }
    bm.innerHTML = '<svg class="bm-arches" viewBox="0 0 520 420"><path pathLength="1" style="--dl:.3s" d="' + arch(260, 400, 10, 420) + '"/><path pathLength="1" style="--dl:.8s" d="' + arch(260, 320, 60, 420) + '"/><path pathLength="1" style="--dl:1.3s" d="' + arch(260, 240, 110, 420) + '"/></svg>';
    // الكمبيوتر: الأقواس تحيط بالشعار وحده
    var sv = bm.querySelector('.bm-arches'), mk = hero.querySelector('.c-mark');
    var place5 = function () {
      if (window.innerWidth < 1100 || !mk) { sv.removeAttribute('style'); return; }
      var r = mk.getBoundingClientRect(), h = hero.getBoundingClientRect(), cs = getComputedStyle(mk);
      var pr = parseFloat(cs.paddingRight) || 0, pl = parseFloat(cs.paddingLeft) || 0;
      var cx = (r.left + pl + (r.width - pl - pr) / 2) - h.left, w = (r.width - pl - pr) * 2.1;
      sv.style.cssText = 'left:' + (cx - w / 2) + 'px;right:auto;width:' + w + 'px;top:' + (r.bottom - h.top + w * .06 - w * 420 / 520) + 'px;translate:none';
    };
    place5(); window.addEventListener('resize', place5); setTimeout(place5, 800);
  }
})();
