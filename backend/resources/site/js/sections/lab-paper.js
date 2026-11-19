// «الورقة البحثية» (site/lab/show for publications, css lab-paper): the key finding is drawn when it comes into view
// (ring, arrow, ruler or code cells); copying the citation (handled by sections/lab.js) flashes the citation text.
(function () {
  var page = document.querySelector('[data-lab-paper]');
  if (!page) return;

  var finding = page.querySelector('.lp-finding');
  if (finding) {
    if (!('IntersectionObserver' in window)) finding.classList.add('is-in');
    else {
      var io = new IntersectionObserver(function (es) {
        if (es.some(function (x) { return x.isIntersecting; })) { finding.classList.add('is-in'); io.disconnect(); }
      }, { threshold: 0.35 });
      io.observe(finding);
    }
  }

  var cite = page.querySelector('.lp-cite');
  var btn = cite && cite.querySelector('.lp-cite-b');
  if (btn) {
    btn.addEventListener('click', function () {
      cite.classList.remove('is-copied'); void cite.offsetWidth; cite.classList.add('is-copied');
    });
  }
})();
