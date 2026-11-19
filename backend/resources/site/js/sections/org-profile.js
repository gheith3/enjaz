// «ملف المؤسسة» (site/organizations/institution, css org-profile): the in-page links follow the section being read,
// «المؤسسة في سطور» unfolds when reached, and the gallery's photos open whole in a dialog.
(function () {
  var page = document.querySelector('[data-org-profile]');
  if (!page) return;

  var jump = page.querySelector('[data-op-jump]');
  if (jump && 'IntersectionObserver' in window) {
    var links = {};
    jump.querySelectorAll('a').forEach(function (a) { links[a.getAttribute('href').slice(1)] = a; });
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (!e.isIntersecting || !links[e.target.id]) return;
        jump.querySelectorAll('a.on').forEach(function (a) { a.classList.remove('on'); });
        links[e.target.id].classList.add('on');
        var row = jump.firstElementChild, a = links[e.target.id];
        if (row.scrollWidth > row.clientWidth) row.scrollTo({ left: a.offsetLeft - (row.clientWidth - a.offsetWidth) / 2, behavior: 'smooth' });
      });
    }, { rootMargin: '-35% 0px -60% 0px' });
    Object.keys(links).forEach(function (id) { var s = document.getElementById(id); if (s) io.observe(s); });
  }

  // «المؤسسة في سطور»: its numbers, path and heads appear one after another when the section is reached.
  var story = page.querySelector('[data-op-story]');
  if (story) {
    if (!('IntersectionObserver' in window)) story.classList.add('is-in');
    else {
      var so = new IntersectionObserver(function (es) {
        if (es.some(function (x) { return x.isIntersecting; })) { story.classList.add('is-in'); so.disconnect(); }
      }, { threshold: 0.15 });
      so.observe(story);
    }
  }

  var box = page.querySelector('[data-op-lightbox]');
  if (box) {
    var img = box.querySelector('img');
    page.querySelectorAll('[data-op-zoom]').forEach(function (b) {
      b.addEventListener('click', function () {
        var src = b.querySelector('img');
        if (typeof box.showModal !== 'function') { window.open(src.currentSrc || src.src, '_blank', 'noopener'); return; }
        img.src = src.currentSrc || src.src; img.alt = src.alt;
        box.showModal();
      });
    });
    box.querySelector('.op-x').addEventListener('click', function () { box.close(); });
    box.addEventListener('click', function (e) { if (e.target === box) box.close(); });
  }
})();
