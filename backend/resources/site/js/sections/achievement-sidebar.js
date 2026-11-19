// Ported from design 10 (approved) js/achievement-reading.js: the achiever's record beside an achievement.
/* سجل الإنجازات: تبويبا «دولية / محلية»، يُفتح تبويب الإنجاز الحالي ويُمرَّر إليه داخل القائمة. */
document.querySelectorAll('[data-ars]').forEach(function (box) {
  var tabs = [].slice.call(box.querySelectorAll('[role="tab"]'));
  function select(tab, focus) {
    tabs.forEach(function (t) {
      var on = t === tab;
      t.setAttribute('aria-selected', String(on));
      t.tabIndex = on ? 0 : -1;
      box.querySelector('#' + t.getAttribute('aria-controls')).hidden = !on;
    });
    if (focus) tab.focus();
  }
  tabs.forEach(function (tab, i) {
    tab.addEventListener('click', function () { select(tab); });
    tab.addEventListener('keydown', function (e) {
      var step = { ArrowLeft: 1, ArrowRight: -1 }[e.key];
      if (!step) return;
      e.preventDefault();
      select(tabs[(i + step + tabs.length) % tabs.length], true);
    });
  });
  var current = box.querySelector('[aria-current="page"]');
  if (!current) return;
  var list = current.closest('[role="tabpanel"]');
  if (list.scrollHeight > list.clientHeight) {
    var row = current.parentNode;
    list.scrollTop = row.offsetTop - (list.clientHeight - row.offsetHeight) / 2;
  }
});
