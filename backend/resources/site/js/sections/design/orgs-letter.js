// Ported from design/js/orgs-letter.js (kept as in the design).
/* رسالة جلالة السلطان بخط يده: فتح الرسالة كاملة (تكشّف الخط بـ CSS). */
(function () {
  var card = document.querySelector('[data-letter-open]'), dlg = document.querySelector('[data-letter]');
  if (!card || !dlg) return;
  card.addEventListener('click', function () { if (dlg.showModal) dlg.showModal(); else dlg.setAttribute('open', ''); });
  dlg.querySelector('[data-letter-close]').addEventListener('click', function () { dlg.close(); });
  dlg.addEventListener('click', function (e) { if (e.target === dlg) dlg.close(); });
})();
