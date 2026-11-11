/* ظهور تدريجي لعناصر الرئيسية عند الوصول إليها (لا يمسّ حركات عائشة والرياضة). */
(() => {
  if (!('IntersectionObserver' in window) || matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  const groups = [
    '.home-statistics .stats',
    '.latest-feature, .latest-card, .latest-row',
    '.inspiring-cover',
    '.article-lead, .article-mini',
    '.home-culture .person',
    '.science-lead, .science-brief',
    '.partners-row'
  ];
  const io = new IntersectionObserver(entries => {
    entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('up-in'); io.unobserve(e.target); } });
  }, { threshold: .15, rootMargin: '0px 0px -6% 0px' });
  groups.forEach(sel => {
    document.querySelectorAll(sel).forEach((el, i) => {
      el.style.setProperty('--rv', (i % 4) * .09 + 's');
      el.classList.add('up-rv');
      io.observe(el);
    });
  });
})();
