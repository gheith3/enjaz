/* هيدر الإنجازات — توقيت موجة «الميداليات تنقلب» (الكمبيوتر فقط): كل صورة تتأخر بحسب بُعدها عن جهة العنوان (اليمين)،
   فتبدو الموجة منطلقة من العنوان عبر الجدار. الصور البعيدة خارج الشاشة تظهر مباشرة. التنسيق: css/achievements-hero-intro.css */
(function () {
  var hero = document.querySelector('.ah2-hero');
  if (!hero) return;
  if (matchMedia('(prefers-reduced-motion: reduce)').matches) { hero.classList.add('ah2-go'); return; }
  // ولايات من محافظات السلطنة كلها، تُوزَّع عشوائيًا على وجوه الميداليات
  var W = ['مسقط', 'مطرح', 'بوشر', 'السيب', 'العامرات', 'قريات', 'صلالة', 'طاقة', 'مرباط', 'ضلكوت', 'نزوى', 'بهلاء', 'منح', 'الحمراء', 'أدم', 'إزكي', 'سمائل', 'صور', 'جعلان بني بو علي',
    'الكامل والوافي', 'مصيرة', 'إبراء', 'المضيبي', 'بدية', 'القابل', 'وادي بني خالد', 'صحار', 'شناص', 'لوى', 'صحم', 'الخابورة', 'السويق', 'الرستاق', 'بركاء', 'المصنعة', 'نخل', 'وادي المعاول',
    'العوابي', 'عبري', 'ينقل', 'ضنك', 'البريمي', 'محضة', 'خصب', 'دبا', 'بخا', 'مدحاء', 'هيماء', 'الدقم', 'محوت', 'الجازر'];
  W.sort(function () { return Math.random() - .5; });
  var hr = hero.getBoundingClientRect(), tiles = hero.querySelectorAll('.ah2-tile');
  [].forEach.call(tiles, function (t, i) { t.setAttribute('data-w', W[i % W.length]); });
  [].forEach.call(tiles, function (t) {
    var r = t.getBoundingClientRect();
    var visible = r.right > hr.left && r.left < hr.right && r.bottom > hr.top && r.top < hr.bottom;
    // المسافة من جهة النص (اليمين) + قليل من الاختلاف بين الصفوف
    var d = (visible || r.right > hr.left) ? Math.max(0, (hr.right - r.right) / hr.width) * 1.6 + ((r.top - hr.top) / hr.height) * .25 + Math.random() * .08 : 0;
    t.style.setProperty('--fd', d.toFixed(2) + 's');
  });
  [].forEach.call(hero.querySelectorAll('.ah2-text > *'), function (el, i) { el.style.setProperty('--i', i); });
  hero.classList.add('ah2-intro');
  requestAnimationFrame(function () { hero.classList.add('ah2-go'); });
})();
