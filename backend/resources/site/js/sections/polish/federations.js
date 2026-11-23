/* تصاميم ٢٠٤٠ (معتمد) — الاتحادات الرياضية والعدد (صفحتا الإنجازات والمنجزين).
   ١) زر «الرياضة» يفتح المستطيل فورًا عند اللمس (المستطيل مرسوم مخفيًا في الصفحة)، وLivewire يجلب العدد والنتائج في الخلفية.
   ٢) حركة الماوس على الاتحاد: ميلان ثلاثي الأبعاد يتبع المؤشر، ولمعة ذهبية (العنصر .x-glint في القالب).
   ٣) العدد الذهبي ينبض حين يتغير، والنتائج الجديدة تظهر بنعومة. */
(function () {
  var filters = document.querySelector('.ach-filters');
  if (!filters) return;
  var $$ = function (s, r) { return [].slice.call((r || document).querySelectorAll(s)); };
  var RM = matchMedia('(prefers-reduced-motion: reduce)').matches, fine = matchMedia('(hover: hover) and (pointer: fine)').matches;

  /* ٢) حركة الماوس: المستمع على المستطيل نفسه، فيعمل مع كل بطاقة يرسمها Livewire لاحقًا */
  if (fine && !RM) {
    var raf = 0;
    filters.addEventListener('pointermove', function (e) {
      var btn = e.target.closest('.fed'); if (!btn) return;
      var r = btn.getBoundingClientRect(), x = (e.clientX - r.left) / r.width - .5, y = (e.clientY - r.top) / r.height - .5;
      cancelAnimationFrame(raf);
      raf = requestAnimationFrame(function () {
        btn.style.setProperty('--rx', (-y * 16).toFixed(2) + 'deg'); btn.style.setProperty('--ry', (x * 18).toFixed(2) + 'deg');
        btn.style.setProperty('--tx', (x * 4).toFixed(1) + 'px'); btn.style.setProperty('--ty', (y * 3).toFixed(1) + 'px');
        btn.style.setProperty('--gx', ((x + .5) * 100).toFixed(1) + '%'); btn.style.setProperty('--gy', ((y + .5) * 100).toFixed(1) + '%');
      });
    });
    filters.addEventListener('pointerout', function (e) {
      var btn = e.target.closest('.fed'); if (!btn || btn.contains(e.relatedTarget)) return;
      cancelAnimationFrame(raf); ['--rx', '--ry', '--tx', '--ty'].forEach(function (k) { btn.style.removeProperty(k); });
    });
  }
  // ضغطة على اتحاد: موجة ذهبية
  filters.addEventListener('pointerdown', function (e) {
    var b = e.target.closest('.fed'); if (!b) return;
    b.classList.remove('x-tap'); void b.offsetWidth; b.classList.add('x-tap');
  });

  /* ١) فتح المستطيل فورًا: نسبق ردّ الخادم بإظهار النسخة المخفية */
  filters.addEventListener('pointerdown', function (e) {
    var chip = e.target.closest('[data-fed-field]'); if (!chip || e.button !== 0) return;
    var preview = filters.querySelector('.fed-panel.x-preview[hidden]');
    if (!filters.classList.contains('fed-open') && preview) {
      preview.hidden = false; preview.classList.add('x-panel'); filters.classList.add('x-fed-on');
      $$('.chip', filters).forEach(function (c) { c.classList.toggle('active', c === chip); c.setAttribute('aria-pressed', String(c === chip)); });
    } else if (filters.classList.contains('fed-open')) {
      var open = filters.querySelector('.fed-panel:not(.x-preview)'); if (open) open.classList.add('x-panel', 'x-closing');
    }
  });

  /* ٣) العدد ينبض حين يتغير، والنتائج تظهر بنعومة (بعد كل تحديث من Livewire) */
  var last = (document.querySelector('.x-count .x-n') || {}).textContent;
  new MutationObserver(function () {
    var badge = document.querySelector('.x-count'), n = badge && badge.querySelector('.x-n');
    if (n && n.textContent !== last) {
      last = n.textContent;
      badge.classList.remove('x-pulse'); void badge.offsetWidth; badge.classList.add('x-pulse');
      var results = document.querySelector('[data-results]');
      if (results && !RM) { results.classList.remove('x-swap'); void results.offsetWidth; results.classList.add('x-swap'); }
    }
  }).observe(filters.parentNode, { childList: true, subtree: true, characterData: true });
})();
