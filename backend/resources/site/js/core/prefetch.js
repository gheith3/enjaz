// تصاميم ٢٠٧٠ — سرعة التنقل: ملف تنسيق كل قسم يُجلب مسبقًا (مرة واحدة، ويبقى في ذاكرة المتصفح سنة)، فلا تنتظر الصفحة التالية
// ملف تنسيقها بعد الضغط. في وقت الفراغ: أقسام الشريط العلوي. عند الوقوف على أي رابط أو لمسه: ملف صفحته.
// الخريطة من الخادم (#x-css-map، App\Support\Site\PagePrefetch). لا شيء عند «توفير البيانات» أو الشبكات البطيئة جدًا.
(function () {
  var el = document.getElementById('x-css-map');
  if (!el) return;
  var map;
  try { map = JSON.parse(el.textContent); } catch (e) { return; }
  var conn = navigator.connection || {};
  var light = conn.saveData || /(^|-)2g$/.test(conn.effectiveType || '');
  var done = {};
  done[location.pathname] = 1;
  [].forEach.call(document.querySelectorAll('link[rel=stylesheet]'), function (l) { done[l.href] = 1; });

  function cssFor(href) {
    var u;
    try { u = new URL(href, location.href); } catch (e) { return null; }
    if (u.origin !== location.origin) return null;
    var p = u.pathname.replace(/\/+$/, '') || '/';
    if (map.pages[p]) return map.pages[p];
    for (var k in map.prefixes) { if (p.indexOf(k) === 0 && p.length > k.length) return map.prefixes[k]; }
    return null;
  }
  function fetchCss(css) {
    if (!css || done[css]) return;
    done[css] = 1;
    var l = document.createElement('link');
    l.rel = 'prefetch'; l.as = 'style'; l.href = css;
    document.head.appendChild(l);
  }
  function fromLink(e) {
    var a = e.target && e.target.closest ? e.target.closest('a[href]') : null;
    if (a && !a.target && !a.hasAttribute('download')) fetchCss(cssFor(a.href));
  }
  if (!light) {
    document.addEventListener('pointerover', fromLink, { passive: true });
    document.addEventListener('touchstart', fromLink, { passive: true });
    document.addEventListener('focusin', fromLink);
  }

  // أقسام الشريط العلوي بعد اكتمال الصفحة، واحدًا تلو الآخر حتى لا تزاحم صور الصفحة الحالية.
  if (light) return;
  var idle = window.requestIdleCallback || function (f) { return setTimeout(f, 200); };
  function nav() {
    var queue = [];
    [].forEach.call(document.querySelectorAll('.site-header a[href], .header a[href]'), function (a) {
      var css = cssFor(a.href);
      if (css && queue.indexOf(css) < 0) queue.push(css);
    });
    (function next() {
      if (!queue.length) return;
      idle(function () { fetchCss(queue.shift()); setTimeout(next, 150); }, { timeout: 3000 });
    })();
  }
  if (document.readyState === 'complete') setTimeout(nav, 1200);
  else addEventListener('load', function () { setTimeout(nav, 1200); });
})();
