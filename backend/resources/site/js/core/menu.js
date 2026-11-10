// Full-screen menu (design js/main.js "القائمة بملء الشاشة"). The desktop search button opens it too.
const menu = document.getElementById('menu');
const openBtn = document.querySelector('[data-menu-open]');

function setMenu(open) {
    if (!menu) return;
    menu.classList.toggle('open', open);
    document.body.classList.toggle('menu-open', open);
    if (openBtn) openBtn.setAttribute('aria-expanded', String(open));
    if (open) {
        const target = menu.querySelector('[data-menu-close]') || menu.querySelector('input');
        setTimeout(() => target && target.focus(), 60);
    } else if (openBtn) {
        openBtn.focus();
    }
}

if (menu) {
    openBtn?.addEventListener('click', () => setMenu(true));
    document.querySelectorAll('[data-menu-close]').forEach((button) => button.addEventListener('click', () => setMenu(false)));
    document.querySelectorAll('.dsearch').forEach((button) => button.addEventListener('click', () => setMenu(true)));
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && menu.classList.contains('open')) setMenu(false);
    });
    menu.querySelectorAll('nav a').forEach((link) => link.addEventListener('click', () => setMenu(false)));
    if (location.hash === '#menu') setMenu(true);
}
