/* تصاميم ٢٠٤٠ (معتمد) — صفحة المقال: عرض تحريري احترافي.
   ١) واجهة مجلة: العنوان والكاتب يمينًا والغلاف يسارًا (وقت القراءة يرسمه القالب في .xa-time).
   ٢) ثلاثة أعمدة: «في هذا المقال» (فهرس ثابت) · نص المقال · شريط المشاركة (أزرار المشاركة نفسها تُنقل إليه).
   ٣) شريط تقدّم القراءة، عناوين مرقّمة، الاقتباس باسم الكاتب، وظهور ناعم للفقرات. */
(function () {
  var art = document.querySelector('.ar-page'), head = art && art.querySelector('.ar-head'), body = art && art.querySelector('.ar-body');
  if (!head || !body) return;
  var cover = art.querySelector('.ar-cover');
  var digits = function (n) { return String(n).replace(/\d/g, function (d) { return '٠١٢٣٤٥٦٧٨٩'[d]; }); };

  // ١) الواجهة: العنوان بجانب الغلاف
  var hero = document.createElement('section'); hero.className = 'wrap xa-hero';
  art.insertBefore(hero, head); head.classList.remove('wrap'); hero.appendChild(head); if (cover) hero.appendChild(cover);

  // ٢) ثلاثة أعمدة
  var lay = document.createElement('div'); lay.className = 'wrap xa-layout';
  body.parentNode.insertBefore(lay, body); body.classList.remove('wrap');
  var hs = [].slice.call(body.querySelectorAll(':scope > h2'));
  var toc = document.createElement('aside'); toc.className = 'xa-side xa-toc'; toc.setAttribute('aria-label', art.dataset.toc || '');
  var title = document.createElement('p'); title.className = 'xa-side-t'; title.textContent = art.dataset.toc || '';
  var ol = document.createElement('ol');
  var item = function (href, i, num, text) {
    var li = document.createElement('li'), a = document.createElement('a'), b = document.createElement('b');
    a.href = href; a.dataset.i = i; b.textContent = num; a.appendChild(b); a.appendChild(document.createTextNode(text)); li.appendChild(a); ol.appendChild(li);
  };
  body.id = body.id || 'xa-top';
  item('#' + body.id, -1, '●', art.dataset.tocIntro || '');
  hs.forEach(function (h, i) {
    h.id = h.id || 'xa-s' + (i + 1);
    var text = h.textContent.trim(), n = document.createElement('span'); n.className = 'xa-n'; n.textContent = digits(String(i + 1).padStart(2, '0'));
    h.insertBefore(n, h.firstChild);
    item('#' + h.id, i, n.textContent, text);
  });
  toc.appendChild(title); toc.appendChild(ol);

  // شريط المشاركة: أزرار المشاركة الموجودة نفسها (بمستمعيها ونصوصها) تُنقل إليه
  var rail = document.createElement('aside'); rail.className = 'xa-side xa-rail';
  var share = body.querySelector('.ar-share');
  if (share) {
    var label = share.querySelector(':scope > span');
    rail.setAttribute('aria-label', label ? label.textContent.trim() : '');
    if (label) { var rt = document.createElement('p'); rt.className = 'xa-side-t'; rt.textContent = label.textContent.trim(); rail.appendChild(rt); }
    [].slice.call(share.children).forEach(function (el) { if (el !== label) { if (el.matches('small')) el.classList.add('xa-done'); rail.appendChild(el); } });
  }
  lay.appendChild(toc); lay.appendChild(body); if (rail.children.length) lay.appendChild(rail);

  // الاقتباس: اسم الكاتب تحته
  var pull = body.querySelector('.ar-pull'), name = (head.querySelector('.byline b') || {}).textContent;
  if (pull && name) pull.setAttribute('data-by', name);

  // ٣) شريط التقدم + الفهرس النشط + ظهور الفقرات
  var bar = document.createElement('div'); bar.className = 'xa-progress'; bar.setAttribute('aria-hidden', 'true'); bar.appendChild(document.createElement('i')); document.body.appendChild(bar);
  var links = [].slice.call(toc.querySelectorAll('a'));
  var tick = function () {
    var r = body.getBoundingClientRect(), p = Math.min(1, Math.max(0, (innerHeight * .35 - r.top) / (r.height - innerHeight * .35)));
    bar.style.setProperty('--xa-p', p.toFixed(4));
    var cur = -1; hs.forEach(function (h, i) { if (h.getBoundingClientRect().top < 160) cur = i; });
    links.forEach(function (a) { a.classList.toggle('on', +a.dataset.i === cur); });
  };
  addEventListener('scroll', function () { requestAnimationFrame(tick); }, { passive: true }); tick();
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (es) { es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('xa-in'); io.unobserve(e.target); } }); }, { rootMargin: '0px 0px -8% 0px' });
    [].forEach.call(body.querySelectorAll(':scope > p:not(.ar-lead), :scope > h2, .ar-pull, .ar-box, .ar-author'), function (el) {
      if (el.getBoundingClientRect().top > innerHeight) { el.classList.add('xa-rv'); io.observe(el); }
    });
  }
})();
