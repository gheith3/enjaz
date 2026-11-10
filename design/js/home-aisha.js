(() => {
  const section = document.querySelector('.aisha-feature');
  if (!section || !('IntersectionObserver' in window) || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  const portrait = section.querySelector('.aisha-portrait');
  const start = () => {
    section.classList.add('motion-ready');
    const observer = new IntersectionObserver(entries => {
      if (!entries.some(entry => entry.isIntersecting)) return;
      section.classList.add('is-visible');
      observer.disconnect();
    }, { threshold: 0.2 });
    observer.observe(section);
  };
  if (portrait.complete) start();
  else {
    portrait.addEventListener('load', start, { once: true });
    portrait.addEventListener('error', start, { once: true });
  }
})();
