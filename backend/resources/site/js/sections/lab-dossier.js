// «ملف الابتكار» (site/lab/show, css lab-dossier): the cover and the story's photos open in full (no crop) in a
// lightbox; the specification sheet's rows appear one after another when it comes into view.
(function () {
  var page = document.querySelector('[data-lab-dossier]');
  if (!page) return;

  var box = document.querySelector('[data-ld-lightbox]');
  var img = box && box.querySelector('img');
  var open = function (src, alt) {
    if (!box || typeof box.showModal !== 'function') { window.open(src, '_blank', 'noopener'); return; }
    img.src = src; img.alt = alt || '';
    box.showModal();
  };
  if (box) {
    box.querySelector('.ld-x').addEventListener('click', function () { box.close(); });
    box.addEventListener('click', function (e) { if (e.target === box) box.close(); });
  }
  var cover = page.querySelector('[data-ld-zoom]');
  if (cover) {
    cover.addEventListener('click', function () { var i = cover.querySelector('img'); open(i.currentSrc || i.src, i.alt); });
  }
  page.querySelectorAll('.ld-body figure img').forEach(function (i) {
    i.addEventListener('click', function () { open(i.currentSrc || i.src, i.alt); });
  });

  var spec = page.querySelector('.ld-spec');
  if (spec) {
    if (!('IntersectionObserver' in window)) { spec.classList.add('is-in'); return; }
    var io = new IntersectionObserver(function (es) {
      if (es.some(function (x) { return x.isIntersecting; })) { spec.classList.add('is-in'); io.disconnect(); }
    }, { threshold: 0.2 });
    io.observe(spec);
  }
})();
