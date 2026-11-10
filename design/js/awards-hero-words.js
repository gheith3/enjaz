/* هيدر جوائز السلطان قابوس — «حروف الثقافة»: كلمات مجالات الجائزة تطفو بالذهب في الهيدر كله.
   طبقة خلف المحتوى، وطبقة أمامه بكلمات أكبر ومموّهة لعمق سينمائي. */
(function () {
  var sec = document.querySelector('[data-aws-words]'); if (!sec) return;
  var words = ['ثقافة', 'فنون', 'آداب', 'تطوّع', 'إبداع', 'عطاء', 'شِعر', 'مسرح', 'موسيقى', 'فكر', 'رواية', 'خطّ', 'تشكيل', 'مبادرة', 'إلهام', 'معرفة'];
  var small = window.innerWidth < 700;
  function R(a, b) { return a + Math.random() * (b - a); }
  function layer(front) {
    var d = document.createElement('div'); d.className = 'awf' + (front ? ' front' : ''); d.setAttribute('aria-hidden', 'true');
    if (front) sec.appendChild(d); else sec.insertBefore(d, sec.firstChild); return d;
  }
  var bag = words.slice().sort(function () { return Math.random() - .5; }), k = 0;
  [[layer(false), small ? 9 : 14, false], [layer(true), small ? 2 : 4, true]].forEach(function (L) {
    for (var i = 0; i < L[1]; i++) {
      var e = document.createElement('span'), f = L[2];
      e.className = 'awf-w';
      e.textContent = bag[k++ % bag.length];
      var size = f ? R(small ? 16 : 26, small ? 22 : 40) : R(small ? 9 : 13, small ? 17 : 30);
      e.style.cssText = 'left:' + R(0, 92).toFixed(1) + '%;--y:' + R(5, 85).toFixed(0) + '%;font-size:' + size.toFixed(0) + 'px;--d:' + R(14, 24).toFixed(1) + 's;--dl:' + R(-24, 0).toFixed(1) + 's;--o:' + (f ? R(.22, .35) : R(.14, .32)).toFixed(2) + ';--b:' + (f ? R(1.5, 3) : R(0, 1)).toFixed(1) + 'px';
      L[0].appendChild(e);
    }
  });
  // كلمات إضافية في جهة اليسار (حول صورة جلالته)
  var back = sec.querySelector('.awf:not(.front)'), extra = small ? 6 : 10;
  for (var x = 0; x < extra; x++) {
    var e = document.createElement('span'); e.className = 'awf-w'; e.textContent = bag[k++ % bag.length];
    e.style.cssText = 'left:' + R(0, 42).toFixed(1) + '%;--y:' + R(5, 85).toFixed(0) + '%;font-size:' + R(small ? 9 : 14, small ? 16 : 30).toFixed(0) + 'px;--d:' + R(14, 22).toFixed(1) + 's;--dl:' + R(-22, 0).toFixed(1) + 's;--o:' + R(.16, .32).toFixed(2) + ';--b:' + R(0, 1).toFixed(1) + 'px';
    back.appendChild(e);
  }
  function fit() { sec.style.setProperty('--fy', (sec.offsetHeight + 90) + 'px'); }
  fit(); window.addEventListener('resize', fit);
})();
