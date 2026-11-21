/* الشريط العلوي: يضبط مسافات أقسام الموقع وحجم خطها حتى تتسع كلها دون أن تصل إلى زر البحث */
(function () {
  var nav = document.querySelector('header .dnav'), header = nav && nav.closest('header'), ul = nav && nav.querySelector('ul');
  if (!ul) return;
  var steps = [[9, 13.5, 2], [8, 13.5, 2], [7, 13, 2], [6, 13, 1], [5, 12.5, 1], [4, 12, 0], [3, 11.5, 0]];
  function fit() {
    if (!window.matchMedia('(min-width: 1100px)').matches) return;
    for (var i = 0; i < steps.length; i++) {
      header.style.setProperty('--dp', steps[i][0] + 'px'); header.style.setProperty('--df', steps[i][1] + 'px'); header.style.setProperty('--dg', steps[i][2] + 'px');
      if (ul.scrollWidth <= nav.clientWidth + 1) return;
    }
  }
  fit();
  addEventListener('resize', function () { clearTimeout(fit.t); fit.t = setTimeout(fit, 80); });
  if (document.fonts && document.fonts.ready) document.fonts.ready.then(fit);
  // الشريط يتقلّص عند النزول في الصفحة، فيُعاد الضبط بعد انتهاء التغيير
  new MutationObserver(function () { clearTimeout(fit.m); fit.m = setTimeout(fit, 350); }).observe(header, { attributes: true, attributeFilter: ['class'] });
})();
