/* المختبر: عدّاد الأرقام، مفتاح التبديل بين الاختراعات والمنشورات، ونسخ المرجع */
(function () {
  var reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  var ar = function (n) { return String(n).replace(/\d/g, function (d) { return '٠١٢٣٤٥٦٧٨٩'[d]; }); };

  // العدّاد
  document.querySelectorAll('[data-count]').forEach(function (el) {
    var to = +el.dataset.count;
    if (reduce) { el.textContent = ar(to); return; }
    var t0 = performance.now(), dur = 1400;
    (function step(t) {
      var p = Math.min(1, (t - t0) / dur), e = 1 - Math.pow(1 - p, 3);
      el.textContent = ar(Math.round(to * e));
      if (p < 1) requestAnimationFrame(step);
    })(t0);
  });

  // مفتاح التبديل
  var sw = document.querySelector('.lab-switch');
  if (sw) {
    var btns = sw.querySelectorAll('[role="tab"]');
    btns.forEach(function (b) {
      b.addEventListener('click', function () {
        btns.forEach(function (x) {
          var on = x === b;
          x.setAttribute('aria-selected', String(on)); x.tabIndex = on ? 0 : -1;
          document.getElementById(x.getAttribute('aria-controls')).hidden = !on;
        });
        sw.dataset.tab = b.dataset.tab;
      });
      b.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowLeft' || e.key === 'ArrowRight') {
          var other = btns[0] === b ? btns[1] : btns[0]; other.click(); other.focus();
        }
      });
    });
  }

  // نسخ المرجع
  document.querySelectorAll('[data-cite]').forEach(function (b) {
    b.addEventListener('click', function () {
      var txt = b.dataset.cite, label = b.querySelector('span');
      var done = function () { b.classList.add('done'); label.textContent = 'تم النسخ'; setTimeout(function () { b.classList.remove('done'); label.textContent = 'نسخ المرجع'; }, 1800); };
      if (navigator.clipboard && navigator.clipboard.writeText) navigator.clipboard.writeText(txt).then(done, done);
      else { var t = document.createElement('textarea'); t.value = txt; document.body.appendChild(t); t.select(); try { document.execCommand('copy'); } catch (e) {} t.remove(); done(); }
    });
  });
})();
