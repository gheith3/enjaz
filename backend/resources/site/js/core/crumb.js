// The section name next to the logo links back to the section's main page (design js/main.js "اسم القسم").
// The server renders it with data-href only when the visitor is not already on that page.
document.querySelectorAll('.crumb[data-href]').forEach((crumb) => {
    const go = () => { location.href = crumb.dataset.href; };
    crumb.addEventListener('click', go);
    crumb.addEventListener('keydown', (event) => { if (event.key === 'Enter') go(); });
});
