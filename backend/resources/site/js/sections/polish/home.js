/* الصفحة الرئيسية — لمسات معتمدة (٢٠٢٦-١١)، منقولة من معاينة «تصاميم جديدة». تعمل فقط في صفحتها. */
(function () {
  if (!document.querySelector('.home-hero')) return;
/* الصفحة الرئيسية — التصميم المختار (انظر chosen.css لمصدر كل جزء) */
(function () {
  var $ = function (s, r) { return (r || document).querySelector(s); };
  var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };
  var RM = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var AR = '٠١٢٣٤٥٦٧٨٩', ar = function (n) { return String(n).replace(/\d/g, function (d) { return AR[d]; }); };
  var S = 'https://s3.injazat.om/injazat-main/media/';
  var ornamentSrc = (document.querySelector('.sec-title img') || {}).src || '';
  var el = function (tag, cls, html) { var e = document.createElement(tag); if (cls) e.className = cls; if (html) e.innerHTML = html; return e; };

  // ===== دخول الشعار (أصلي ٢): عند أول فتح للصفحة في الجلسة وعند التحديث فقط،
  // ولا يظهر عند الرجوع بسهم المتصفح ولا عند العودة إليها من صفحة أخرى في الموقع =====
  var navType = (function () {
    try { var e = performance.getEntriesByType('navigation')[0]; if (e && e.type) return e.type; } catch (x) {}
    try { return ['navigate', 'reload', 'back_forward'][performance.navigation.type] || 'navigate'; } catch (x) { return 'navigate'; }
  })();
  var seenIntro = false; try { seenIntro = sessionStorage.getItem('t-intro-home') === '1'; } catch (x) {}
  var playIntro = !RM && (navType === 'reload' || (navType === 'navigate' && !seenIntro));
  try { sessionStorage.setItem('t-intro-home', '1'); } catch (x) {}
  // صفحة عائدة من ذاكرة المتصفح (bfcache): لا افتتاحية
  addEventListener('pageshow', function (e) { if (e.persisted) { var o = $('.t-intro'); if (o && o.parentNode) o.parentNode.removeChild(o); } });
  var logo = $('.site-header .logo img');
  if (playIntro) {
    var intro = el('div', 't-intro', '<div class="t-intro-in">' + (logo ? '<img src="' + logo.getAttribute('src') + '" alt="">' : '') + '<i></i></div>');
    intro.setAttribute('aria-hidden', 'true'); document.body.appendChild(intro);
    document.documentElement.classList.add('t-intro-on');
    setTimeout(function () { intro.classList.add('out'); document.documentElement.classList.remove('t-intro-on'); }, 1350);
    setTimeout(function () { intro.parentNode && intro.parentNode.removeChild(intro); }, 2600);
  }

  // ===== اللمعة على الصور (أصلي ١) =====
  $$('.latest-feature, .latest-card, .latest-row, .ha-pair > *, .ha-sq, .ha-bh, .inspiring-cover-art, .article-lead, .science-lead').forEach(function (card) {
    card.classList.add('t-host');
    var s = el('span', 't-shine'); s.setAttribute('aria-hidden', 'true'); card.appendChild(s);
    card.addEventListener('mousemove', function (e) { var r = card.getBoundingClientRect(); s.style.setProperty('--mx', (e.clientX - r.left) + 'px'); s.style.setProperty('--my', (e.clientY - r.top) + 'px'); });
  });

  // ===== رحلة عبر الزمن — بعد «أحدث الإنجازات» =====
  // الصورة هي البطل: تنكشف بستارة مع تقريب هادئ، وخلفها نسخة مموّهة منها تملأ الإطار مهما كان اتجاهها.
  // النص مختصر: السنة، عنوان قصير، صاحب الإنجاز، و«اقرأ القصة» صغيرة. نقاط الخط صور مصغّرة دائرية.
  // L = صور محلية أرسلها صاحب الموقع (مجلد tl-img)، وغيرها من مكتبة الموقع. p = نقطة التركيز في الصورة.
  var L = '/images/timeline/';
  var T = [
    { y: null, lbl: 'الرائدة', s: 'أول دكتوراه لامرأة خليجية', n: 'د. فاطمة بنت سالم المعمرية', f: 'العلم', img: L + 'fatma-wide.webp', p: '50% 20%', u: '' },
    { y: 1990, s: 'ذهبية آسيا في ٤٠٠ متر', n: 'محمد عامر المالكي', f: 'ألعاب القوى', img: L + 'mohammed-amer-almalki-wide.webp', p: '50% 22%', u: 'https://injazat.om/achievers/mohammed-almalki' },
    { y: 1995, s: 'رابع العالم للناشئين', n: 'منتخب عُمان للناشئين', f: 'كرة القدم', img: L + 'u17-1995-wide.webp', p: '50% 40%', u: 'https://injazat.om/achievers/mohammed-amer-al-kathiri' },
    { y: 1995, s: 'الكرة الذهبية للعالم', n: 'محمد بن عامر الكثيري', f: 'كرة القدم', img: L + 'kathiri-wide.webp', p: '50% 25%', u: 'https://injazat.om/achievers/mohammed-amer-al-kathiri' },
    { y: 2009, s: 'أول لقب خليجي لعُمان', n: 'منتخب عُمان', f: 'كرة القدم', img: L + 'gulf-cup-2009-wide.webp', p: '55% 35%', u: 'https://injazat.om/achievements/aly-alhbsy-03' },
    { y: 2010, s: 'أمام السلطان قابوس', n: 'مصطفى بارامي', f: 'الابتكار', img: L + 'barami-wide.webp', p: '50% 30%', u: 'https://injazat.om/achievers/mustafa-barami' },
    { y: 2019, s: 'على قمة إيفرست', n: 'نظيرة الحارثية', f: 'تسلق الجبال', img: L + 'nadhira-wide.webp', p: '45% 40%', u: '' },
    { y: 2019, s: 'مان بوكر العالمية', n: 'د. جوخة الحارثية', f: 'الأدب', img: L + 'jokha-booker-wide.webp', p: '50% 22%', u: 'https://injazat.om/achievements/man-booker-2019' },
    { y: 2021, s: 'شاعرة الرسول', n: 'بدرية البدري', f: 'الشعر', img: L + 'badri-wide.webp', p: '50% 25%', u: '' },
    { y: 2025, s: 'وصيف العالم في الغوص الحر', n: 'عمر الغيلاني', f: 'الغوص الحر', img: L + 'ghailani-wide.webp', p: '50% 25%', u: '' }
  ];
  var latest = $('.home-latest');
  if (latest) {
    var sec = el('section', 'section wrap t-time');
    sec.setAttribute('aria-labelledby', 'tt-title');
    sec.innerHTML = '<div class="tt-head"><div class="sec-title"><img src="' + ornamentSrc + '" alt=""><h2 id="tt-title">رحلة عبر الزمن</h2></div>' +
      '<p>محطات صنعها العُمانيون، من الرائدات الأوائل إلى منصات العالم اليوم.</p></div>' +
      '<div class="tt-box">' +
        '<div class="tt-stage" aria-hidden="true"></div>' +
        '<div class="tt-copy" aria-live="polite"><div class="tt-year"></div><h3 class="tt-title"></h3><p class="tt-who"></p><a class="tt-go"></a></div>' +
        '<div class="tt-rail" role="group" aria-label="محطات الرحلة"><span class="line"></span><span class="fill"></span></div>' +
        '<div class="tt-nav"><button type="button" class="prev" aria-label="المحطة السابقة"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="m9 6 6 6-6 6"/></svg></button>' +
        '<span class="count"></span>' +
        '<button type="button" class="next" aria-label="المحطة التالية"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="m15 6-6 6 6 6"/></svg></button></div>' +
      '</div>';
    latest.parentNode.insertBefore(sec, latest.nextSibling);
    var stage = $('.tt-stage', sec), rail = $('.tt-rail', sec), yr = $('.tt-year', sec), ttl = $('.tt-title', sec), who = $('.tt-who', sec), go = $('.tt-go', sec),
      fill = $('.fill', sec), cnt = $('.count', sec), copy = $('.tt-copy', sec);
    var label = function (m) { return m.y ? ar(m.y) : m.lbl; };
    // النقاط موزعة بالتساوي (المحطات متقاربة زمنيًا أحيانًا، والتساوي أوضح للصور المصغرة)
    var pos = T.map(function (m, k) { return k / (T.length - 1) * 100; });
    T.forEach(function (m, k) {
      var b = el('button', '', '<span class="th"><img src="' + m.img + '" alt="" loading="lazy" style="object-position:' + m.p + '"></span><small>' + label(m) + '</small>');
      b.type = 'button'; b.style.right = pos[k] + '%'; b.setAttribute('aria-label', label(m) + ': ' + m.s + '، ' + m.n);
      b.addEventListener('click', function () { user = true; show(k); }); rail.appendChild(b);
    });
    // تحميل الصور مسبقًا ليكون الانتقال سلسًا
    T.forEach(function (m) { var im = new Image(); im.src = m.img; });
    var ti = -1, timer, user = false;
    function show(k) {
      var prev = ti; ti = (k + T.length) % T.length; var m = T[ti], dir = prev < 0 || ti >= prev ? 1 : -1;
      $$('button', rail).forEach(function (b, j) { b.classList.toggle('on', j === ti); b.classList.toggle('past', j < ti); b.setAttribute('aria-pressed', String(j === ti)); });
      fill.style.setProperty('--p', pos[ti] + '%');
      // الصورة: طبقة جديدة تنكشف بستارة فوق السابقة
      var layer = el('div', 'tt-shot' + (dir < 0 ? ' back' : ''), '<span class="blur" style="background-image:url(\'' + m.img + '\')"></span><img src="' + m.img + '" alt="" style="object-position:' + m.p + '"><i class="frame"></i>');
      stage.appendChild(layer);
      setTimeout(function () { layer.classList.add('in'); }, 20);
      var old = $$('.tt-shot', stage); if (old.length > 2) old.slice(0, old.length - 2).forEach(function (o) { o.parentNode.removeChild(o); });
      // النص: يخرج ثم يدخل
      copy.classList.add('out');
      setTimeout(function () {
        yr.classList.toggle('word', !m.y);
        if (m.y) {
          var from = parseInt(yr.dataset.y || m.y, 10), to = m.y, t0 = Date.now();
          (function run() { var q = Math.min(1, (Date.now() - t0) / 700); yr.textContent = ar(Math.round(from + (to - from) * (1 - Math.pow(1 - q, 3)))); if (q < 1) setTimeout(run, 30); })();
          yr.dataset.y = to;
        } else { yr.textContent = m.lbl; }
        ttl.textContent = m.s; who.innerHTML = '<b class="tt-name">' + m.n + '</b><span class="tt-field">' + m.f + '</span>';
        if (m.u) { go.href = m.u; go.innerHTML = 'اقرأ القصة <i aria-hidden="true">←</i>'; go.hidden = false; } else { go.removeAttribute('href'); go.hidden = true; }
        cnt.textContent = ar(ti + 1) + ' / ' + ar(T.length);
        copy.classList.remove('out');
      }, 280);
      clearTimeout(timer); if (!RM && !user) timer = setTimeout(function () { show(ti + 1); }, 5200);
    }
    $('.prev', sec).addEventListener('click', function () { user = true; show(ti - 1); });
    $('.next', sec).addEventListener('click', function () { user = true; show(ti + 1); });
    sec.addEventListener('keydown', function (e) { if (e.key === 'ArrowLeft') { user = true; show(ti + 1); } if (e.key === 'ArrowRight') { user = true; show(ti - 1); } });
    user = true; show(0);
    if ('IntersectionObserver' in window) {
      var tio = new IntersectionObserver(function (es) { if (es[0].isIntersecting) { sec.classList.add('seen'); user = false; show(ti); tio.disconnect(); } }, { threshold: .3 });
      tio.observe(sec);
    } else sec.classList.add('seen');
  }

  // ===== ملهمون: مروحة البطاقات (تصميم مبتكر) =====
  var insp = $('.home-inspiring'), covers = insp && $$('.inspiring-cover', insp);
  if (covers && covers.length) {
    var n = covers.length, c = (n - 1) / 2;
    var deck = el('div', 't-deck');
    deck.innerHTML = covers.map(function (f, i) {
      var img = $('img', f), name = ($('figcaption h3', f) || {}).textContent || '', field = ($('figcaption span', f) || {}).textContent || '';
      return '<a class="t-pc" style="--i:' + i + ';--c:' + c + '" href="https://injazat.om/inspiring"><div class="cv"><img src="' + img.getAttribute('src') + '" alt="' + (img.getAttribute('alt') || '') + '" loading="lazy"></div>' +
        '<div class="nm"><b>' + name + '</b><span>' + field + '</span></div></a>';
    }).join('');
    $('.inspiring-covers', insp).parentNode.insertBefore(deck, $('.inspiring-covers', insp));
    insp.classList.add('t-fan');
    $$('.t-pc .cv', deck).forEach(function (cv) { cv.classList.add('t-host'); var s = el('span', 't-shine'); cv.appendChild(s); });
    if ('IntersectionObserver' in window && !RM) {
      var dio = new IntersectionObserver(function (es) { if (es[0].isIntersecting) { deck.classList.add('in'); dio.disconnect(); } }, { threshold: .25 });
      dio.observe(deck);
    } else deck.classList.add('in');
  }

  // ===== عائشة السيفي: خلفية أغنى وخط مرتب، والحركة الأصلية كما هي (رسم الخطين ودخول الصورة والنص) =====
  var aisha = $('.aisha-feature');
  if (aisha) {
    var bg = el('span', 't-ai-bg', '<i class="glow"></i><i class="grid"></i><i class="beam"></i><i class="ring"></i>' +
      '<span class="dust">' + [8, 17, 26, 34, 43, 52, 61, 70, 79, 88].map(function (x, i) { return '<b style="--x:' + x + '%;--d:' + (8 + (i % 3) * 2) + 's;--dl:' + (i * .9).toFixed(1) + 's"></b>'; }).join('') + '</span>');
    bg.setAttribute('aria-hidden', 'true'); aisha.insertBefore(bg, aisha.firstChild);
    var h2 = $('.aisha-copy h2', aisha);
    if (h2 && !h2.querySelector('.ln')) {
      var w = h2.textContent.trim().split(/\s+/);
      if (w.length > 1) h2.innerHTML = '<span class="ln">' + w.slice(0, -1).join(' ') + '</span> <span class="ln gold">' + w[w.length - 1] + '</span>';
    }
  }

  // ===== مستطيل «لديك إنجاز يستحق أن يُروى؟» (نموذج ١) — قبل التذييل =====
  var footer = $('.site-footer');
  if (footer) {
    // الشعار باهتًا، وذرات ذهبية صاعدة (أُزيلت الأقواس بطلب صاحب الموقع)
    var dust = '<span class="dust" aria-hidden="true">' + [6, 14, 22, 31, 38, 47, 55, 63, 72, 80, 88, 95].map(function (x, i) {
      return '<i style="--x:' + x + '%;--d:' + (7 + (i % 4) * 1.7) + 's;--dl:' + (i * .7).toFixed(1) + 's"></i>'; }).join('') + '</span>';
    var mark = logo ? '<img class="mark" src="' + logo.getAttribute('src') + '" alt="" aria-hidden="true">' : '';
    // كلمات ذهبية تصعد من الأسفل إلى الأعلى (على غرار بانر جوائز السلطان قابوس): عن الإنجاز ومشاركته.
    // ثلاث طبقات عمق: بعيدة (صغيرة، مموّهة، أبطأ)، ومتوسطة، وقريبة (أكبر وأوضح). المواضع موزعة بانتظام مع تفاوت بسيط.
    var WORDS = ['إنجاز', 'شاركنا', 'حكايتك', 'تتويج', 'ذهبية', 'ميدالية', 'رقم قياسي', 'أول عُماني', 'ابتكار', 'براءة اختراع', 'إبداع', 'عزيمة',
      'شغف', 'أثر', 'فخر', 'بصمة', 'قمة', 'منصة', 'لقب', 'وسام', 'جائزة', 'بطولة', 'طموح', 'إلهام', 'مثابرة', 'توثيق', 'مصادر', 'قصة تُروى',
      'اسمٌ عُماني', 'خطوة أولى', 'حلم تحقق', 'نوثّقه معك'];
    // البعيدة على العرض كله وخافتة جدًا؛ المتوسطة والقريبة في النصف الأيسر (جهة الزر) حتى لا تزاحم العنوان
    var layers = [{ c: 'far', s: [11, 13], d: [18, 24], o: .1, w: 96 }, { c: 'mid', s: [13, 16], d: [14, 18], o: .18, w: 58 }, { c: 'near', s: [16, 20], d: [11, 14], o: .28, w: 52 }];
    var rnd = function (a, b) { return a + Math.random() * (b - a); };
    var words = '<span class="words" aria-hidden="true">' + WORDS.map(function (w, i) {
      var L = layers[i % 3], x = ((i * 37) % L.w) + rnd(0, 3);
      return '<i class="' + L.c + '" style="--x:' + x.toFixed(1) + '%;--fs:' + rnd(L.s[0], L.s[1]).toFixed(0) + 'px;--dur:' + rnd(L.d[0], L.d[1]).toFixed(1) + 's;--dl:-' + rnd(0, L.d[1]).toFixed(1) + 's;--op:' + L.o + ';--sw:' + rnd(-14, 14).toFixed(0) + 'px">' + w + '</i>';
    }).join('') + '</span>';
    var cta = el('section', 't-cta', words + mark + dust + '<div class="in"><div><span class="kick">إنجازات شباب عُمان</span><h2 id="t-cta-h">لديك إنجاز يستحق أن يُروى؟</h2><p>شاركنا إنجازك أو إنجاز من تعرفه، وسنوثّقه بمصادره على المنصة.</p></div>' +
      '<a class="btn" href="https://injazat.om/contact">شاركنا إنجازًا ←</a></div>');
    cta.setAttribute('aria-labelledby', 't-cta-h');
    footer.parentNode.insertBefore(cta, footer);
  }

  // ===== شريط المنجزين: ضوء المسرح، وإطار دوّار ولمعة لكل صورة (يظهران على الوسطى فقط) =====
  var strip = $('.home-achievers .strip'), sframe = $('.home-achievers .strip-frame');
  if (strip && sframe) {
    var light = el('span', 't-stage-light'); light.setAttribute('aria-hidden', 'true'); sframe.insertBefore(light, sframe.firstChild);
    var dress = function () {
      $$('.strip-photo', strip).forEach(function (ph) {
        if (ph.querySelector('.t-sring')) return;
        var r = el('span', 't-sring'), w = el('span', 't-sweep'); r.setAttribute('aria-hidden', 'true'); w.setAttribute('aria-hidden', 'true');
        ph.appendChild(w); ph.appendChild(r);
      });
    };
    dress();
    new MutationObserver(dress).observe(strip, { childList: true, subtree: true });
  }

  // ===== جوائز وطنية: علامة مائية من شعار كل جائزة، إطار دوّار حول الشعار، إطار يُرسم في بلعرب، وظهور متتابع =====
  var aw = $('.home-awards');
  if (aw) {
    $$('.ha-sq', aw).forEach(function (card) {
      var lg = $('.ha-logo img', card), logoBox = $('.ha-logo', card);
      if (lg) { var wm = el('span', 't-aw-wm'); wm.setAttribute('aria-hidden', 'true'); wm.style.backgroundImage = 'url("' + lg.getAttribute('src') + '")'; card.insertBefore(wm, card.firstChild); }
      if (logoBox) { var rg = el('span', 't-aw-ring'); rg.setAttribute('aria-hidden', 'true'); logoBox.appendChild(rg); }
    });
    var bh = $('.ha-bh', aw);
    if (bh) {
      var NS2 = 'http://www.w3.org/2000/svg', fr = document.createElementNS(NS2, 'svg'); fr.setAttribute('class', 't-aw-frame'); fr.setAttribute('aria-hidden', 'true');
      fr.innerHTML = '<rect x="1" y="1" rx="20" ry="20"/>'; bh.appendChild(fr);
      var sizeFrame = function () { var r = fr.getBoundingClientRect(), rc = fr.querySelector('rect'); if (!r.width) return;
        rc.setAttribute('width', r.width - 2); rc.setAttribute('height', r.height - 2); fr.style.setProperty('--L', Math.ceil(2 * (r.width + r.height))); };
      sizeFrame(); addEventListener('resize', sizeFrame); setTimeout(sizeFrame, 800);
    }
    var cards = $$('.ha-sq, .ha-bh', aw);
    if ('IntersectionObserver' in window && !RM) {
      cards.forEach(function (c, i) { c.classList.add('t-aw-pre'); c.style.setProperty('--d', (i * 140) + 'ms'); });
      var aio = new IntersectionObserver(function (es) {
        es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('t-aw-in'); e.target.classList.remove('t-aw-pre'); aio.unobserve(e.target); } });
      }, { threshold: .2 });
      cards.forEach(function (c) { aio.observe(c); });
    } else cards.forEach(function (c) { c.classList.add('t-aw-in'); });
  }

  // ===== سهم العودة للأعلى (أصلي ٢) =====
  var top = el('button', 't-top', '<svg viewBox="0 0 54 54" aria-hidden="true"><circle class="bg" cx="27" cy="27" r="24"/><circle class="fg" cx="27" cy="27" r="24"/></svg><i aria-hidden="true">↑</i>');
  top.type = 'button'; top.setAttribute('aria-label', 'العودة إلى أعلى الصفحة'); document.body.appendChild(top);
  top.addEventListener('click', function () { window.scrollTo({ top: 0, behavior: RM ? 'auto' : 'smooth' }); });
  function onScroll() {
    var h = document.documentElement.scrollHeight - innerHeight, p = h > 0 ? Math.min(1, scrollY / h) : 0;
    top.style.setProperty('--p', p.toFixed(4)); top.classList.toggle('on', scrollY > innerHeight * .8);
  }
  addEventListener('scroll', onScroll, { passive: true }); onScroll();
})();

})();

/* الأرقام: ضوء يتبع المؤشر داخل كل بطاقة */
(function () {
  Array.prototype.forEach.call(document.querySelectorAll('.home-statistics .hs-i'), function (c) {
    c.addEventListener('mousemove', function (e) { var r = c.getBoundingClientRect(); c.style.setProperty('--mx', (e.clientX - r.left) + 'px'); c.style.setProperty('--my', (e.clientY - r.top) + 'px'); });
  });
})();
