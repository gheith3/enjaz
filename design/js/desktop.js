/* Use the content viewport, excluding the scrollbar, for full-width sections. */
(function () {
  function syncWidth() {
    document.documentElement.style.setProperty('--layout-width', document.documentElement.clientWidth + 'px');
  }
  syncWidth();
  window.addEventListener('resize', syncWidth, { passive: true });
  if ('ResizeObserver' in window) new ResizeObserver(syncWidth).observe(document.documentElement);
})();

/* الكمبيوتر: قائمة أفقية في الهيدر تُبنى من روابط القائمة نفسها، وزر بحث يفتح القائمة الكاملة،
   وهيدر يصغر عند النزول. على الهاتف لا يظهر شيء من هذا (مخفي بالتنسيق). */
(function () {
  // صفحة «من نحن» لها هيدر خاص (.page > .header)؛ تُعامل مثل بقية الصفحات
  var header = document.querySelector('.site-header') || document.querySelector('.about-page .page > .header');
  var links = document.querySelectorAll('.menu nav a');
  if (!header || !links.length || header.querySelector('.dnav')) return;
  // الروابط الأساسية فقط حتى لا يزدحم الهيدر؛ البقية في القائمة الكاملة
  var keep = ['index.html', 'achievements.html', 'achievers.html', 'inspiring.html', 'institutions.html', 'awards.html', 'bilarab-award.html', 'universities.html', 'science.html', 'articles.html', 'about.html', 'contact.html'];
  var short = { 'bilarab-award.html': 'جائزة بلعرب', 'awards.html': 'جوائز السلطان قابوس', 'universities.html': 'الجامعات', 'science.html': 'الابتكارات العلمية', 'index.html': 'الرئيسية', 'achievers.html': 'المنجزون', 'about.html': 'من نحن' };
  var nav = document.createElement('nav');
  nav.className = 'dnav'; nav.setAttribute('aria-label', 'أقسام الموقع');
  var ul = document.createElement('ul');
  var here = location.pathname.split('/').pop() || 'index.html';
  [].forEach.call(links, function (a) {
    var href = (a.getAttribute('href') || '').replace(/^\.\//, '');
    if (keep.indexOf(href) < 0) return;
    var li = document.createElement('li'), b = document.createElement('a');
    b.href = href; b.textContent = short[href] || a.textContent.trim();
    if (a.getAttribute('aria-current') === 'page' || href === here) b.setAttribute('aria-current', 'page');
    li.appendChild(b); ul.appendChild(li);
  });
  if (!ul.children.length) return;
  nav.appendChild(ul);
  header.classList.add('has-desktop-nav');
  var brand = header.querySelector('.brand') || header.firstElementChild;
  brand.insertAdjacentElement('afterend', nav);
  var openBtn = header.querySelector('[data-menu-open], [data-open-drawer]');
  if (openBtn) {
    var s = document.createElement('button');
    s.type = 'button'; s.className = 'dsearch'; s.setAttribute('aria-label', 'البحث والقائمة الكاملة');
    s.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>';
    s.addEventListener('click', function () { openBtn.click(); });
    nav.insertAdjacentElement('afterend', s);
  }
  var onScroll = function () { header.classList.toggle('is-scrolled', window.scrollY > 40); };
  window.addEventListener('scroll', onScroll, { passive: true }); onScroll();
})();

/* Rebuild the logo groups when their container changes width. */
(function () {
  document.querySelectorAll('.partners-track').forEach(function (track) {
    var groups = Array.from(track.querySelectorAll('.partners-group'));
    var box = track.parentElement;
    if (groups.length < 2 || !box) return;
    var originals = groups.map(function (g) { return Array.from(g.children); });
    var previousWidth = -1;
    function fit() {
      var width = box.clientWidth;
      if (width === previousWidth) return;
      previousWidth = width;
      groups.forEach(function (g, index) {
        Array.from(g.children).slice(originals[index].length).forEach(function (c) { c.remove(); });
      });
      if (!matchMedia('(min-width: 700px)').matches) return;
      var guard = 0;
      while (groups[0].scrollWidth < width * 1.1 && guard++ < 8) {
        groups.forEach(function (g, index) {
          originals[index].forEach(function (el) {
            var c = el.cloneNode(true);
            c.setAttribute('aria-hidden', 'true');
            if (c.matches('a,button,[tabindex]')) c.tabIndex = -1;
            c.querySelectorAll('a,button,input,[tabindex]').forEach(function (e) { e.tabIndex = -1; });
            g.appendChild(c);
          });
        });
      }
    }
    fit();
    if ('ResizeObserver' in window) new ResizeObserver(fit).observe(box);
    else window.addEventListener('resize', fit, { passive: true });
  });
})();
