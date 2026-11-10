// "قصتي": read more / hide (design js/main.js). Button labels come from data attributes.
document.querySelectorAll('[data-story]').forEach((card) => {
    const button = card.querySelector('.toggle');
    if (!button) return;
    button.addEventListener('click', () => {
        const open = card.classList.toggle('open');
        button.setAttribute('aria-expanded', String(open));
        button.querySelector('span').textContent = open ? button.dataset.less : button.dataset.more;
    });
});
