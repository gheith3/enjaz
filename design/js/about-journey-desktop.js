/* من نحن — «خارطة العمل» على الكمبيوتر (رحلتنا ٢): رحلة واحدة كاملة من البداية إلى النهاية بلا اختيار.
   خط ذهبي في المنتصف يمتلئ بالضوء مع التمرير، والمحطات تتناوب يمينًا ويسارًا بترتيبها الزمني مع محطات السنوات،
   وفي النهاية نجمة «الرؤية» فتتضح الغاية من الرحلة. البيانات من js/about-roadmap.js (window.INJAZ_ROADMAP).
   النسخة السابقة محفوظة باسم «رحلتنا ١»: about-journey-1.html */
(function () {
  // نسخة المراجعة: نفس الرحلة على الهاتف والكمبيوتر.
  var M = window.INJAZ_ROADMAP, sec = document.querySelector('#journey');
  if (!M || !sec) return;
  var IMG = sec.dataset.img || 'assets/img/';
  var ar = function (n) { return String(n).replace(/\d/g, function (d) { return '٠١٢٣٤٥٦٧٨٩'[d]; }); };
  var html = '<div class="jv"><p class="jv-lead">من فكرةٍ صغيرة إلى منصّة وطنية: تابع رحلتنا من أولها إلى آخرها.</p><div class="jv-track"><span class="jv-line" aria-hidden="true"><i></i></span><ol class="jv-list">';
  // كل بطاقة تمتد صفّين وتبدأ في الصف التالي للبطاقة السابقة، فتتداخل البطاقات يمينًا ويسارًا بلا تراكب
  var last = '', side = 0, row = 1, end = 1;
  M.forEach(function (m, i) {
    if (m.y !== last) { row = Math.max(row, end); html += '<li class="jv-year" style="grid-row:' + row + '"><span>' + m.y + '</span></li>'; row++; end = row; last = m.y; side = 0; }
    html += '<li class="jv-item ' + (side++ % 2 ? 'jv-l' : 'jv-r') + '"><span class="jv-dot" aria-hidden="true"></span><article class="jv-card" style="grid-row:' + row + ' / span 2">' +
      '<span class="jv-img"><img src="' + IMG + m.img + '" alt="" loading="lazy"></span><div><small>' + m.date + '</small><p>' + m.t + '</p></div></article></li>';
    end = row + 2; row++;
  });
  html += '<li class="jv-end" style="grid-row:' + Math.max(row, end) + '"><span class="jv-star" aria-hidden="true"><svg viewBox="0 0 40 40"><path d="M20 2c1.6 9.6 8.4 16.4 18 18-9.6 1.6-16.4 8.4-18 18-1.6-9.6-8.4-16.4-18-18C11.6 18.4 18.4 11.6 20 2z"/></svg></span>' +
    '<div class="jv-vision"><small>وإلى أين؟ رؤيتنا</small><b>أن نكون المنصّة الأساسية والمرجع الأول لتوثيق إنجازات الشباب العُماني في سلطنة عُمان.</b><span>واليوم: +' + ar(2000) + ' منجز ومنجزة عُمانيين في قاعدة بياناتنا، والرحلة مستمرة.</span></div></li>';
  html += '</ol></div></div>';
  var tl = sec.querySelector('[data-rm-tl]'); tl.insertAdjacentHTML('beforebegin', html);
  var box = sec.querySelector('.jv'), track = box.querySelector('.jv-track'), fill = box.querySelector('.jv-line i');
  // إطار الرؤية: خيط ينزل من أسفل النجمة ثم ينقسم ليرسم إطارًا مستطيلًا مقوّسًا حول جملة الرؤية بلمعة ذهبية فخمة (بلا مخطط مسبق)
  var vis = box.querySelector('.jv-vision'), NS = 'http://www.w3.org/2000/svg';
  var fr = document.createElementNS(NS, 'svg'); fr.setAttribute('class', 'jv-frame'); fr.setAttribute('aria-hidden', 'true');
  vis.appendChild(fr);
  var frame = function () {
    var pad = 26, w = vis.offsetWidth + pad * 2, h = vis.offsetHeight + pad * 2, r = 26, c = w / 2, top = 40;
    fr.setAttribute('viewBox', '0 0 ' + w + ' ' + (h + top)); fr.style.width = w + 'px'; fr.style.height = (h + top) + 'px';
    var y0 = top, y1 = top + h;
    var right = 'M' + c + ' ' + y0 + 'H' + (w - r) + 'A' + r + ' ' + r + ' 0 0 1 ' + w + ' ' + (y0 + r) + 'V' + (y1 - r) + 'A' + r + ' ' + r + ' 0 0 1 ' + (w - r) + ' ' + y1 + 'H' + c;
    var left = 'M' + c + ' ' + y0 + 'H' + r + 'A' + r + ' ' + r + ' 0 0 0 0 ' + (y0 + r) + 'V' + (y1 - r) + 'A' + r + ' ' + r + ' 0 0 0 ' + r + ' ' + y1 + 'H' + c;
    fr.innerHTML = '<defs><linearGradient id="jvGold" gradientUnits="userSpaceOnUse" x1="0" y1="0" x2="' + w + '" y2="' + (h + top) + '">' +
      '<stop offset="0" stop-color="#8A6424"/><stop offset=".22" stop-color="#E9C77A"/><stop offset=".35" stop-color="#FFF4D2"/><stop offset=".48" stop-color="#C99A45"/>' +
      '<stop offset=".7" stop-color="#F7DFA3"/><stop offset=".85" stop-color="#FFFBEA"/><stop offset="1" stop-color="#9C7330"/>' +
      '<animateTransform attributeName="gradientTransform" type="translate" values="-' + w + ' 0; ' + w + ' 0" dur="4.5s" repeatCount="indefinite"/></linearGradient>' +
      '<filter id="jvGlow" filterUnits="userSpaceOnUse" x="-20" y="-20" width="' + (w + 40) + '" height="' + (h + top + 40) + '"><feGaussianBlur stdDeviation="2.2" result="b"/><feMerge><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge></filter></defs>' +
      '<path class="jv-f0" pathLength="1" d="M' + c + ' 0V' + y0 + '"/>' +
      '<path class="jv-f1" pathLength="1" d="' + right + '"/><path class="jv-f1" pathLength="1" d="' + left + '"/>';
  };
  // خط الرحلة ينتهي عند مركز النجمة، والإطار يكمل من أسفلها
  var star = box.querySelector('.jv-star'), lineEl = box.querySelector('.jv-line'), list = box.querySelector('.jv-list');
  var fit = function () { frame(); var lr = list.getBoundingClientRect(), sr = star.getBoundingClientRect(); lineEl.style.bottom = Math.max(0, lr.bottom - (sr.top + sr.height / 2)) + 'px'; };
  fit(); addEventListener('resize', fit); setTimeout(fit, 800);

  var items = [].slice.call(box.querySelectorAll('.jv-year, .jv-item, .jv-end'));
  var reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (reduce) { items.forEach(function (el) { el.classList.add('in'); }); fill.style.height = '100%'; box.classList.add('jv-reached'); return; }
  box.classList.add('jv-anim');
  var tick = function () {
    var vh = innerHeight, r = lineEl.getBoundingClientRect();
    var p = Math.max(0, Math.min(1, (vh * .6 - r.top) / r.height));
    fill.style.height = (p * 100) + '%';
    items.forEach(function (el) { if (!el.classList.contains('in') && el.getBoundingClientRect().top < vh * .82) el.classList.add('in'); });
    // عندما يصل الضوء إلى نجمة الرؤية ينبثق الخط الذهبي الجديد ويرسم الإطار
    if (p > .985) box.classList.add('jv-reached');
  };
  addEventListener('scroll', tick, { passive: true }); addEventListener('resize', tick);
  setInterval(tick, 300); tick();
})();
