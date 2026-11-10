/* Ported from design js/award-volunteer-motion.js (unchanged). */
/* حركة موشن عند التمرير في جائزة العمل التطوعي (الكمبيوتر فقط):
   «زهرة العطاء» تتفتح (المدار يُرسم، القلب يظهر، المجالات تنطلق من القلب إلى أماكنها)،
   ثم «عطاءٌ ينمو» (الشتلات تنمو من الأرض واحدة بعد الأخرى). التنسيق في css/award-volunteer-desktop.css */
(function () {
  if (matchMedia('(prefers-reduced-motion: reduce)').matches) return; /* نسخة المراجعة: الهاتف أيضًا */
  var start = function () {
    var els = [].slice.call(document.querySelectorAll('.v-sec .v-fields, .v-sec .v-crit'));
    if (!els.length || !els[0].children.length) return false;
    els.forEach(function (el) { el.classList.add('vg-anim'); });
    var check = function () {
      var vh = innerHeight;
      els = els.filter(function (el) {
        var r = el.getBoundingClientRect();
        if (r.top < vh * .75 && r.bottom > vh * .1) { el.classList.add('vg-in'); return false; }
        return true;
      });
      if (!els.length) { removeEventListener('scroll', check); clearInterval(poll); }
    };
    addEventListener('scroll', check, { passive: true }); addEventListener('resize', check);
    var poll = setInterval(check, 300); // احتياط إن لم تصل أحداث التمرير
    requestAnimationFrame(check);
    return true;
  };
  // القوائم تُبنى بالسكربت، فننتظر حتى تمتلئ
  if (!start()) { var n = 0, t = setInterval(function () { if (start() || ++n > 40) clearInterval(t); }, 100); }
})();
