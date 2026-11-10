/* إشارة النزول أسفل الهيدر (كلمات تتبدّل ثم سهم): تُضاف إلى بانر جلالة السلطان (الرئيسية)، وهيدر جوائز السلطان قابوس، وهيدر جائزة بلعرب.
   الضغط ينزل إلى القسم التالي (مع حساب ارتفاع الشريط العلوي)، وتختفي الإشارة بعد بدء التمرير. التنسيق: css/scroll-cue.css */
(function () {
  function init() {
  var hero = document.querySelector('.home .hero-cinema, .aws-page .aws-hero, [data-bf] .c-hero');
  if (!hero) return false;
  if (hero.querySelector('.scroll-cue')) return true;
  var btn = document.createElement('button');
  btn.type = 'button'; btn.className = 'scroll-cue'; btn.setAttribute('aria-label', 'انزل إلى بقية الصفحة');
  // ثلاث كلمات خاصة بكل هيدر تظهر واحدة بعد الأخرى، ثم سهم أسفلها
  var words = hero.matches('.aws-hero') ? ['إبداع', 'عطاء', 'تكريم'] : hero.matches('.c-hero') ? ['فكرة', 'تصميم', 'تنفيذ'] : ['حلم', 'عزيمة', 'إنجاز'];
  btn.innerHTML = '<span class="sc-w" aria-hidden="true"><span>' + words.join('</span><span>') + '</span></span>' +
    '<svg class="sc-a" viewBox="0 0 24 22" aria-hidden="true"><path d="M12 2v17M3 11.5l9 8 9-8"/></svg>';
  hero.appendChild(btn);
  btn.addEventListener('click', function () {
    var next = hero.nextElementSibling, head = document.querySelector('.site-header');
    var y = (next ? next.getBoundingClientRect().top : hero.getBoundingClientRect().bottom) + window.scrollY - (head ? head.offsetHeight : 0);
    window.scrollTo({ top: y, behavior: matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth' });
  });
  var onScroll = function () { btn.classList.toggle('gone', window.scrollY > 60); };
  addEventListener('scroll', onScroll, { passive: true }); onScroll();
  return true;
  }
  // بعض الهيدرات تُبنى بالسكربت بعد التحميل (مثل جائزة بلعرب)، فنعيد المحاولة حتى يظهر الهيدر
  if (!init()) { var tries = 0, t = setInterval(function () { if (init() || ++tries > 40) clearInterval(t); }, 100); }
})();
