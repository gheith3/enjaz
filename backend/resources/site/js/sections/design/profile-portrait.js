// Ported from design/js/profile-portrait.js (kept as in the design; data now comes from the server-rendered page).
(() => {
  const frame = document.querySelector('.profile-portrait');
  const photo = frame?.querySelector('.photo');
  const motion = window.matchMedia('(prefers-reduced-motion: reduce)');
  if (!photo || motion.matches) return;
  let observer;
  let finished = false;
  frame.classList.add('motion-pending');
  const settle = () => {
    finished = true;
    observer?.disconnect();
    frame.classList.remove('motion-pending', 'motion-reveal');
  };
  const reveal = () => {
    if (finished) return;
    finished = true;
    observer?.disconnect();
    frame.classList.remove('motion-pending');
    frame.classList.add('motion-reveal');
  };
  const ready = () => {
    if (finished) return;
    if (!photo.naturalWidth || motion.matches) { settle(); return; }
    if (!('IntersectionObserver' in window)) { reveal(); return; }
    observer = new IntersectionObserver(entries => {
      if (entries.some(entry => entry.isIntersecting)) reveal();
    }, { threshold: .25 });
    observer.observe(frame);
  };
  photo.addEventListener('animationend', settle, { once: true });
  motion.addEventListener('change', event => { if (event.matches) settle(); });
  if (photo.complete) ready();
  else {
    photo.addEventListener('load', ready, { once: true });
    photo.addEventListener('error', settle, { once: true });
  }
})();
