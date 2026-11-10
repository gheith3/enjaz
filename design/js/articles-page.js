/* مجلة إنجازات: تصفية المقالات حسب التبويب والبحث */
(function () {
  // أقلام إنجازات: صاحب أحدث مقال أولًا
  var wbox = document.querySelector('.mag-writers');
  if (wbox) [].slice.call(wbox.children).sort(function (a, b) { return (b.dataset.latest || '').localeCompare(a.dataset.latest || ''); }).forEach(function (w) { wbox.appendChild(w); });
  var rows = Array.prototype.slice.call(document.querySelectorAll('.mag-row'));
  var tabs = Array.prototype.slice.call(document.querySelectorAll('.mag-tabs .chip'));
  var input = document.querySelector('[data-art-search]');
  var empty = document.querySelector('.mag-empty');
  var cat = '*', q = '';
  function apply() {
    var n = 0;
    rows.forEach(function (r) {
      var ok = (cat === '*' || r.dataset.cat === cat) && (!q || r.textContent.indexOf(q) > -1);
      r.hidden = !ok; if (ok) n++;
    });
    if (empty) empty.hidden = n > 0;
  }
  tabs.forEach(function (t) {
    t.addEventListener('click', function () {
      tabs.forEach(function (x) { x.classList.remove('active'); x.setAttribute('aria-pressed', 'false'); });
      t.classList.add('active'); t.setAttribute('aria-pressed', 'true');
      cat = t.dataset.tab; apply();
    });
  });
  if (input) input.addEventListener('input', function () {
    q = input.value.trim(); apply();
    if (q) { var list = document.getElementById('mag-all'); if (list && list.getBoundingClientRect().top > innerHeight) list.scrollIntoView({ block: 'start' }); }
  });
})();
