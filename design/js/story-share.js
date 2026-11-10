(() => {
  const title = document.querySelector('#achievement-title')?.textContent.trim() || document.title;
  const url = new URL(window.location.href);
  url.hash = '';
  const message = `${title}\n${url.href}`;
  const destinations = {
    whatsapp: `https://wa.me/?text=${encodeURIComponent(message)}`,
    twitter: `https://twitter.com/intent/tweet?text=${encodeURIComponent(title)}&url=${encodeURIComponent(url.href)}`,
    email: `mailto:?subject=${encodeURIComponent(title)}&body=${encodeURIComponent(message)}`
  };
  document.querySelectorAll('[data-story-channel]').forEach(link => {
    link.href = destinations[link.dataset.storyChannel];
  });
  const panel = document.querySelector('.story-share-options');
  document.addEventListener('click', event => {
    if (panel?.open && !panel.contains(event.target)) panel.open = false;
  });
  panel?.addEventListener('keydown', event => {
    if (event.key !== 'Escape') return;
    event.currentTarget.open = false;
    event.currentTarget.querySelector('summary').focus();
  });
})();
