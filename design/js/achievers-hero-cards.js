/* المنجزين — هيدر «بطاقات الأبطال» (الكمبيوتر): بطاقات فاخرة مفرودة كيدٍ من الأوراق،
   كل ٣ ثوانٍ تتقدّم بطاقة ويمرّ عليها بريق ذهبي، والضغط على بطاقة يقدّمها. التنسيق: css/achievers-hero-cards.css */
(function () {
  var deck = document.querySelector('[data-hero-deck]');
  var H0 = document.querySelector('.ap-hero'); if (H0 && matchMedia('(prefers-reduced-motion: reduce)').matches) H0.classList.add('ap-go');
  if (!deck) return; var M = matchMedia('(max-width: 1099px)').matches ? .62 : 1; /* نسخة المراجعة: الهاتف أيضًا */
  var C = [
    ['assets/img/inspiring-safiya.jpg', '40% 30%', 'صفية البهلاني'],
    ['assets/img/mohammed-amer-almalki.jpg', '55% 25%', 'محمد عامر المالكي'],
    ['assets/img/aisha-alsaifi.jpg', '50% 20%', 'عائشة السيفي'],
    ['assets/img/ali-alhabsi.jpg', '52% 15%', 'علي الحبسي'],
    ['assets/img/jokha-profile-hq.webp', '62% 12%', 'د. جوخة الحارثية'],
    ['assets/img/inspiring-mohammed.jpg', '50% 12%', 'محمد المشايخي'],
    ['assets/img/sports-hamed-portrait-v2.jpg', '50% 15%', 'حمد الوهيبي'],
    ['assets/img/inspiring-zainab.jpg', '50% 25%', 'زينب الحراصي']
  ];
  // بطاقات للعرض فقط: الصورة والاسم
  var n = C.length, front = 3;
  deck.innerHTML = C.map(function (c) { return '<div class="hc"><img src="' + c[0] + '" alt="" style="object-position:' + c[1] + '"><div class="hc-i"><b>' + c[2] + '</b></div></div>'; }).join('');
  var cards = deck.querySelectorAll('.hc');
  var lay = function () {
    [].forEach.call(cards, function (el, i) {
      var k = ((i - front) % n + n) % n, s = k === 0 ? 0 : (k <= n / 2 ? k : k - n);
      el.style.setProperty('--r', (s * -6) + 'deg'); el.style.setProperty('--x', (s * -46 * M * (M < 1 ? .8 : 1)) + 'px'); el.style.setProperty('--y', (Math.abs(s) * 6) + 'px'); el.style.zIndex = 10 - Math.abs(s);
      el.classList.toggle('front', k === 0);
    });
  };
  [].forEach.call(cards, function (el, i) { el.addEventListener('click', function () { front = i; lay(); }); });
  lay();
  // ظهور البطاقات كأوراق تُوزَّع: الأبعد عن الأمامية أولًا، والأمامية آخرًا (الكمبيوتر)
  var hero = deck.closest('.ap-hero');
  [].forEach.call(cards, function (el, i) { var k = ((i - front) % n + n) % n, s = k === 0 ? 0 : (k <= n / 2 ? k : k - n); el.style.setProperty('--dd', (.05 + (n / 2 - Math.abs(s)) * .08).toFixed(2) + 's'); });
  if (hero) requestAnimationFrame(function () { hero.classList.add('ap-go'); });
  if (!matchMedia('(prefers-reduced-motion: reduce)').matches) setInterval(function () { if (!deck.matches(':hover')) { front = (front + 1) % n; lay(); } }, 3000);
})();
