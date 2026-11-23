/* تصاميم ٢٠٤٠ — الشريط العلوي: يحفظ المقاس النهائي للصفحات التالية، استجابة فورية للضغط، وتحميل مسبق للقسم عند المرور عليه */
(function () {
  var h = document.querySelector('header.site-header, header.header'), n = h && h.querySelector('.dnav');
  if (!n) return;
  var save = function () {
    var v = ['--dp', '--df', '--dg'].map(function (k) { return parseFloat(h.style.getPropertyValue(k)); });
    if (v.every(function (x) { return x > 0; })) try { sessionStorage.setItem('x-navfit2:' + innerWidth, v.join(',')); } catch (e) {}
  };
  (document.fonts && document.fonts.ready ? document.fonts.ready : Promise.resolve()).then(function () { setTimeout(save, 400); });
  addEventListener('resize', function () { clearTimeout(save.t); save.t = setTimeout(save, 500); });

  var fetched = {};
  n.addEventListener('pointerover', function (e) {
    var a = e.target.closest('a[href]');
    if (!a || fetched[a.href] || !/^https?:/.test(a.href) || a.origin !== location.origin) return;
    fetched[a.href] = 1;
    var l = document.createElement('link'); l.rel = 'prefetch'; l.href = a.href; document.head.appendChild(l);
  });
  n.addEventListener('pointerdown', function (e) {
    var a = e.target.closest('a[href]');
    if (!a || e.button !== 0) return;
    [].forEach.call(n.querySelectorAll('a.x-going'), function (x) { x.classList.remove('x-going'); });
    a.classList.add('x-going');
  });
  addEventListener('pageshow', function () { [].forEach.call(n.querySelectorAll('a.x-going'), function (x) { x.classList.remove('x-going'); }); });
})();
