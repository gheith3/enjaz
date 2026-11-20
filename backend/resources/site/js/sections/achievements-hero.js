// Achievements hero wall "medals flip" wave, then «لوحة الشرف» dropping in once the wall has settled (the approved design
// «صفحة الإنجازات - معتمد»). The wave waits for the font and the wall's photos so it never runs half-loaded.
// Styles in css/achievements-hero-intro.css and css/achievements-honor.css.
import { reducedMotion } from '../core/labels.js';

let finishHeroIntro;
const heroIntroFinished = new Promise(resolve => { finishHeroIntro = resolve; });
(() => {
// The wilayat names are decoration on the back of the medals.

const WILAYAT = ['مسقط', 'مطرح', 'بوشر', 'السيب', 'العامرات', 'قريات', 'صلالة', 'طاقة', 'مرباط', 'ضلكوت', 'نزوى', 'بهلاء', 'منح', 'الحمراء', 'أدم', 'إزكي', 'سمائل', 'صور', 'جعلان بني بو علي',
    'الكامل والوافي', 'مصيرة', 'إبراء', 'المضيبي', 'بدية', 'القابل', 'وادي بني خالد', 'صحار', 'شناص', 'لوى', 'صحم', 'الخابورة', 'السويق', 'الرستاق', 'بركاء', 'المصنعة', 'نخل', 'وادي المعاول',
    'العوابي', 'عبري', 'ينقل', 'ضنك', 'البريمي', 'محضة', 'خصب', 'دبا', 'بخا', 'مدحاء', 'هيماء', 'الدقم', 'محوت', 'الجازر'];

const hero = document.querySelector('.ah2-hero');
if (hero) {
    if (reducedMotion()) {
        hero.classList.add('ah2-go');
        finishHeroIntro();
    } else {
        const names = [...WILAYAT].sort(() => Math.random() - 0.5);
        const box = hero.getBoundingClientRect();
        const tiles = hero.querySelectorAll('.ah2-tile');
        tiles.forEach((tile, i) => tile.setAttribute('data-w', names[i % names.length]));
        tiles.forEach((tile) => {
            const r = tile.getBoundingClientRect();
            const visible = r.right > box.left && r.left < box.right && r.bottom > box.top && r.top < box.bottom;
            const delay = (visible || r.right > box.left)
                ? Math.max(0, (box.right - r.right) / box.width) * 1.6 + ((r.top - box.top) / box.height) * 0.25 + Math.random() * 0.08
                : 0;
            tile.style.setProperty('--fd', `${delay.toFixed(2)}s`);
        });
        hero.querySelectorAll('.ah2-text > *').forEach((el, i) => el.style.setProperty('--i', i));
        hero.classList.add('ah2-intro');
        let started = false;
        const startIntro = () => {
            if (started) return;
            started = true;
            hero.classList.add('ah2-go');
            requestAnimationFrame(() => {
                const entrance = hero.getAnimations({subtree: true}).filter(animation =>
                    animation.animationName === 'ah2-search-in');
                Promise.allSettled(entrance.map(animation => animation.finished)).then(finishHeroIntro);
                // Search entrance: 1.15s delay + .9s duration.
                setTimeout(finishHeroIntro, 2100);
            });
        };
        Promise.all([document.fonts.ready, ...Array.from(hero.querySelectorAll('img'), img => img.decode().catch(() => {}))])
            .then(() => requestAnimationFrame(() => requestAnimationFrame(startIntro)));
        setTimeout(startIntro, 3500);
    }
} else { finishHeroIntro(); }

})();

(() => {
 const card = document.querySelector('.honor-card');
 if (!card) return;
 if (reducedMotion() || !('IntersectionObserver' in window)) { card.classList.remove('honor-pending'); return; }
 card.classList.add('honor-pending');
 const reveal = () => { card.classList.add('honor-visible'); card.classList.remove('honor-pending'); };
 const observer = new IntersectionObserver(entries => {
   if (entries.some(entry => entry.isIntersecting)) { reveal(); observer.disconnect(); }
 }, {threshold: .15});
 const img = card.querySelector('.honor-img');
 Promise.all([heroIntroFinished, img ? img.decode().catch(() => {}) : Promise.resolve()])
   .then(() => observer.observe(card.closest('.honor')));
})();
