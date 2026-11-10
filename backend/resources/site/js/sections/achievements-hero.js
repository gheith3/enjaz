// Achievements hero wall "medals flip" wave (design js/achievements-hero-intro.js). Desktop motion only,
// styles in css/achievements-hero-intro.css. The wilayat names are decoration on the back of the medals.
import { reducedMotion } from '../core/labels.js';

const WILAYAT = ['مسقط', 'مطرح', 'بوشر', 'السيب', 'العامرات', 'قريات', 'صلالة', 'طاقة', 'مرباط', 'ضلكوت', 'نزوى', 'بهلاء', 'منح', 'الحمراء', 'أدم', 'إزكي', 'سمائل', 'صور', 'جعلان بني بو علي',
    'الكامل والوافي', 'مصيرة', 'إبراء', 'المضيبي', 'بدية', 'القابل', 'وادي بني خالد', 'صحار', 'شناص', 'لوى', 'صحم', 'الخابورة', 'السويق', 'الرستاق', 'بركاء', 'المصنعة', 'نخل', 'وادي المعاول',
    'العوابي', 'عبري', 'ينقل', 'ضنك', 'البريمي', 'محضة', 'خصب', 'دبا', 'بخا', 'مدحاء', 'هيماء', 'الدقم', 'محوت', 'الجازر'];

const hero = document.querySelector('.ah2-hero');
if (hero) {
    if (reducedMotion()) {
        hero.classList.add('ah2-go');
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
        requestAnimationFrame(() => hero.classList.add('ah2-go'));
    }
}
