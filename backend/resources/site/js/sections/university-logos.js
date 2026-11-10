// Balance logo sizes by their width/height ratio (design js/universities.js sizeLogos()).
const sizeLogos = () => document.querySelectorAll('.u-logo').forEach((box) => {
    const img = box.querySelector('img');
    if (!img) return;
    const bw = box.clientWidth - 12, bh = box.clientHeight - 12;
    if (bw <= 0 || bh <= 0) return;
    const r = +box.dataset.r || 1, area = 0.5 * bw * bh;
    let w = Math.sqrt(area * r), h = w / r;
    if (w > bw) { w = bw; h = w / r; }
    if (h > bh) { h = bh; w = h * r; }
    img.style.width = `${Math.round(w)}px`;
    img.style.height = `${Math.round(h)}px`;
});

if (document.querySelector('.u-logo')) {
    sizeLogos();
    let timer;
    addEventListener('resize', () => { clearTimeout(timer); timer = setTimeout(sizeLogos, 120); });
    document.addEventListener('livewire:init', () => window.Livewire.hook('morphed', () => requestAnimationFrame(sizeLogos)));
}
