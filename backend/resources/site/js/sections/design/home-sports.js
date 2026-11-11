(() => {
  const cards = document.querySelectorAll('.home-sports .sports-person');
  const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
  if (reduced.matches || !('IntersectionObserver' in window)) return;
  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      entry.target.classList.add('is-visible');
      observer.unobserve(entry.target);
    });
  }, { threshold: .25 });
  cards.forEach(card => {
    const source = card.querySelector('.sports-cutout');
    if (!source) return;
    const preload = new Image();
    preload.onload = () => {
      card.classList.add('sports-motion-ready');
      observer.observe(card);
    };
    preload.src = source.currentSrc || source.src;
  });
  reduced.addEventListener('change', event => {
    if (!event.matches) return;
    observer.disconnect();
    cards.forEach(card => card.classList.add('is-visible'));
  });
})();
