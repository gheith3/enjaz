// Ported from design/js/profile-portrait.js (kept as in the design; data now comes from the server-rendered page).
// The hero text now rises with the portrait; achiever.blade.php sets the waiting state before the first paint.

// The introduction stays on one line: when it would wrap, its font shrinks a little (to 80% at most);
// only a sentence too long even then wraps (narrow phones).
(() => {
  const about = document.querySelector('.profile-hero .about');
  if (!about) return;
  const fit = () => {
    about.style.fontSize = '';
    about.style.whiteSpace = 'nowrap';
    let size = parseFloat(getComputedStyle(about).fontSize);
    const min = size * 0.8;
    while (about.scrollWidth > about.clientWidth + 1 && size > min) {
      size -= 0.5;
      about.style.fontSize = `${size}px`;
    }
    if (about.scrollWidth > about.clientWidth + 1) {
      about.style.fontSize = '';
      about.style.whiteSpace = '';
    }
  };
  let timer;
  (document.fonts?.ready ?? Promise.resolve()).then(fit);
  fit();
  addEventListener('resize', () => { clearTimeout(timer); timer = setTimeout(fit, 120); });
})();
(() => {
  const frame = document.querySelector('.profile-portrait');
  const photo = frame?.querySelector('.photo');
  const hero = frame?.closest('.profile-hero');
  const motion = window.matchMedia('(prefers-reduced-motion: reduce)');
  // The name, English name and introduction rise with the portrait (overrides.css: .hero-wait / .hero-in).
  // Waits for the Cairo font (preloaded, so normally already there; at most 0.3 s) so the lines never re-wrap mid-animation.
  const fontsReady = Promise.race([document.fonts?.ready ?? Promise.resolve(), new Promise((done) => setTimeout(done, 300))]);
  const textIn = () => fontsReady.then(() => { if (hero?.classList.contains('hero-wait')) { hero.classList.remove('hero-wait'); hero.classList.add('hero-in'); } });
  if (!photo || motion.matches) { textIn(); return; }
  let observer;
  let finished = false;
  frame.classList.add('motion-pending');
  const settle = () => {
    finished = true;
    observer?.disconnect();
    frame.classList.remove('motion-pending', 'motion-reveal');
    textIn();
  };
  const reveal = () => {
    if (finished) return;
    if (!frame.classList.contains('motion-pending')) { settle(); return; } // already shown by the page's 4 s fallback
    finished = true;
    observer?.disconnect();
    frame.classList.remove('motion-pending');
    frame.classList.add('motion-reveal');
    textIn();
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
