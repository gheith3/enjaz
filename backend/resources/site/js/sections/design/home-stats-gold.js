/* إحصائيات الرئيسية — «العدّاد الذهبي»: يحوّل كل رقم إلى خانات تتدحرج عند ظهور الشريط. الرقم الأصلي يبقى مكتوبًا إن لم يعمل السكربت. */
(function () {
  var box = document.querySelector('[data-gold-stats]'); if (!box) return;
  var AD = '٠١٢٣٤٥٦٧٨٩';
  box.querySelectorAll('[data-n]').forEach(function (el) {
    el.innerHTML = el.dataset.n.split('').map(function (d, i) {
      var cells = ''; for (var k = 0; k < 20; k++) cells += '<i>' + AD[k % 10] + '</i>';
      return '<span class="hs-col" style="--v:' + (10 + +d) + ';--d:' + i + '">' + cells + '</span>';
    }).join('');
  });
  var go = function () { box.classList.add('go'); };
  if (!('IntersectionObserver' in window)) return go();
  var io = new IntersectionObserver(function (es) { if (es[0].isIntersecting) { io.disconnect(); go(); } }, { threshold: .4 });
  io.observe(box);
})();
