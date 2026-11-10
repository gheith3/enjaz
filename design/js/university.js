/* صفحة الجامعة أو الكلية: university.html?org=squ
   البيانات من js/universities-data.js، والمنجزون والإنجازات المرتبطة من js/org-links.js.
   السنوات بالأرقام اللاتينية (1986) لأن الصفر العربي «٠» يشبه النقطة. */
(function () {
  var root = document.querySelector('[data-uni-page]');
  var list = window.INJAZ_UNIS || [];
  if (!root || !list.length) return;
  var slug = new URLSearchParams(location.search).get('org') || 'squ';
  var u = list.filter(function (x) { return x.slug === slug; })[0];
  var $ = function (s) { return root.querySelector(s); };
  var ar = function (n) { return String(n).replace(/\d/g, function (d) { return '٠١٢٣٤٥٦٧٨٩'[d]; }); };
  var esc = function (s) { var d = document.createElement('div'); d.textContent = s == null ? '' : s; return d.innerHTML; };
  var pl = function (n, one, two, few, many) { return n === 1 ? one : n === 2 ? two : n + ' ' + (n <= 10 ? few : many); };
  var NOW = new Date().getFullYear();
  var ORD = ['الأول', 'الثاني', 'الثالث', 'الرابع', 'الخامس', 'السادس', 'السابع', 'الثامن', 'التاسع', 'العاشر', 'الحادي عشر', 'الثاني عشر'];
  var hide = function (s) { var el = $(s); if (el) el.hidden = true; };

  if (!u) { $('[data-u-hero]').innerHTML = '<h1>المؤسسة غير موجودة</h1><p class="uh-en"><a href="universities.html">العودة إلى الجامعات والكليات</a></p>'; return; }
  root.dataset.org = slug;
  var kind = u.group === 'college' ? 'الكلية' : 'الجامعة';
  document.title = u.name + ' | إنجازات شباب عمان';
  // اسم الجامعة أو الكلية تحت «الجامعات والكليات» في الشريط العلوي (نفس شكل ملف المنجز)
  var crumbSub = function (name) {
    var c = document.querySelector('.crumb'); if (!c || !name) return;
    c.classList.add('has-sub');
    c.innerHTML = '<span class="crumb-title">الجامعات والكليات</span><small class="crumb-sub">' + esc(name) + '</small>';
  };
  crumbSub(u.name);
  [].forEach.call(root.querySelectorAll('[data-kind]'), function (el) { el.textContent = el.textContent.replace('الجامعة', kind); });

  // ---------- الرأس ----------
  var chips = [u.sector && (u.group === 'college' ? 'كلية ' : 'جامعة ') + u.sector, u.founded && 'تأسست ' + u.founded, u.city].filter(Boolean);
  // مثل رأس ملف المنجز: الاسم بالذهبي في جهة، والشعار في الجهة الأخرى مكان الصورة
  var emblem = u.logo && (u.r || 1) < 1.6;
  $('[data-u-hero]').innerHTML =
    '<div class="uh-top"><div class="uh-txt"><span class="uh-kick">' + chips.slice(0, 2).map(esc).join(' · ') + '</span>' +
    '<h1>' + esc(u.name) + '</h1><p class="uh-en" dir="ltr">' + esc(u.en) + '</p>' +
    (u.tagline || u.city ? '<p class="uh-about">' + esc([u.tagline, u.city].filter(Boolean).join(' · ')) + '</p>' : '') + '</div>' +
    '<span class="uh-emb' + (emblem ? '' : ' is-plate') + '">' + (u.logo ? '<img src="' + esc(u.logo) + '" alt="شعار ' + esc(u.name) + '">' : '<b>' + esc(u.abbr || u.en.replace(/[^A-Z]/g, '')) + '</b>') + '</span></div>' +
    (u.stats ? '<div class="uh-stats">' + u.stats.map(function (s) { return '<span><b>' + esc(s[0]) + '</b><small>' + esc(s[1]) + '</small></span>'; }).join('') + '</div>' : '') +
    '<nav class="uh-jump" aria-label="أقسام الصفحة">' + [['#u-about', 'نبذة'], (u.presidents || u.leaders) && ['#u-pres', u.presidents ? 'الرؤساء' : 'القيادة'], ['#u-ach', 'الإنجازات'], ['#u-colleges', 'الكليات'], ['#u-people', 'المنجزون']]
      .filter(Boolean).map(function (x) { return '<a href="' + x[0] + '">' + x[1] + '</a>'; }).join('') + '</nav>';

  // ---------- نبذة ----------
  $('[data-u-about]').innerHTML = (u.about ? '<p class="u-text">' + esc(u.about) + '</p>' : '<p class="u-soon">الملف التعريفي لهذه المؤسسة يُستكمل قريبًا.</p>') +
    (u.centers ? '<div class="u-ctr"><b>مراكز وكراسٍ بحثية</b><ul>' + u.centers.map(function (c) { return '<li>' + esc(c) + '</li>'; }).join('') + '</ul></div>' : '') +
    (u.branches ? '<div class="u-br"><b>الفروع</b><div>' + u.branches.map(function (b, i) { return '<span style="--i:' + i + '">' + esc(b) + '</span>'; }).join('') + '</div></div>' : '') +
    (u.site ? '<a class="u-site" href="' + esc(u.site) + '" target="_blank" rel="noopener">الموقع الرسمي <span aria-hidden="true">↗</span></a>' : '');

  // ---------- نمو الكليات: سطر لكل سنة، والعدد التراكمي ----------
  if (u.colleges && u.colleges.length) {
    var total = 0;
    $('[data-u-colleges]').innerHTML = u.colleges.map(function (c) {
      total += c.names.length;
      return '<li><span class="ug-y">' + c.year + '</span><span class="ug-body"><span class="ug-chips">' + c.names.map(function (n) { return '<span>' + esc(n) + '</span>'; }).join('') + '</span>' +
        (c.note ? '<small>' + esc(c.note) + '</small>' : '') + '</span><span class="ug-n" title="عدد الكليات"><b>' + total + '</b>' + (total > 10 ? 'كلية' : 'كليات') + '</span></li>';
    }).join('');
  } else if (u.units && u.units.length) {
    // جامعة بلا تسلسل زمني للكليات: كلياتها ومعاهدها في مجموعات
    $('#u-col-h').textContent = 'كلياتها ومعاهدها';
    var colBox = $('[data-u-colleges]'); colBox.className = 'uu';
    colBox.innerHTML = u.units.map(function (g) {
      return '<li><b class="uu-l">' + esc(g.label) + ' <small>' + g.names.length + '</small></b><span class="uu-list">' + g.names.map(function (n) { return '<span>' + esc(n) + '</span>'; }).join('') + '</span></li>';
    }).join('');
  } else hide('#u-colleges');

  // ---------- القيادة (بدل سجل الرؤساء حين لا يتوفر سجل كامل) ----------
  if (!(u.presidents || []).length && (u.leaders || []).length) {
    var sec = $('#u-pres');
    sec.querySelector('.up-eyebrow').textContent = 'قيادة ' + kind;
    sec.querySelector('h2').textContent = 'مجلس الأمناء والإدارة';
    $('[data-u-ribbon]').hidden = true;
    var initials = function (n) { return n.split(' ')[0].charAt(0) + (n.split(' ').slice(-1)[0] || '').replace(/^ال/, '').charAt(0); };
    $('[data-u-pres]').className = 'up-rail ul-lead';
    $('[data-u-pres]').innerHTML = u.leaders.map(function (p) {
      return '<li class="up-card' + (p.current ? ' is-now' : '') + '">' +
        '<span class="up-ph">' + (p.photo ? '<img src="' + esc(p.photo) + '" alt="' + esc(p.name) + '" loading="lazy">' : '<i class="up-ini" aria-hidden="true">' + esc(initials(p.name)) + '</i>') + '</span>' +
        '<span class="up-k">' + esc(p.role) + '</span><small class="up-t">' + esc(p.title || '') + '</small><b class="up-n">' + esc(p.name) + '</b>' +
        (p.note ? '<small class="up-note">' + esc(p.note) + '</small>' : '') + '</li>';
    }).join('');
    // أعضاء مجلس الأمناء: دوائر صغيرة، 4 في كل سطر
    if ((u.board || []).length) {
      $('[data-u-pres]').insertAdjacentHTML('afterend', '<div class="ub"><h3 class="ub-h">أعضاء مجلس الأمناء <small>' + u.board.length + '</small></h3><div class="ub-grid">' +
        u.board.map(function (m) {
          return '<div class="ub-m"><span class="ub-ph">' + (m.photo ? '<img src="' + esc(m.photo) + '" alt="' + esc(m.name) + '" loading="lazy">' : '<i>' + esc(initials(m.name)) + '</i>') + '</span>' +
            '<small class="ub-t">' + esc(m.title) + '</small><b>' + esc(m.name) + '</b><span class="ub-r">' + esc(m.role) + '</span></div>';
        }).join('') + '</div></div>');
    }
  }

  // ---------- الرؤساء: شريط زمني نسبي + بطاقات صور ----------
  var P = u.presidents || [];
  if (P.length) {
    var y0 = P[0].from, y1 = NOW, span = Math.max(1, y1 - y0);
    var dur = function (p) { return (p.to || NOW) - p.from; };
    var durTxt = function (p) { var n = dur(p); return (n <= 0 ? 'أقل من سنة' : n === 1 ? 'سنة واحدة' : n === 2 ? 'سنتان' : n + (n <= 10 ? ' سنوات' : ' سنة')) + (p.current ? ' حتى الآن' : ''); };
    var longest = P.reduce(function (m, p, i) { return dur(p) > dur(P[m]) ? i : m; }, 0);
    $('[data-u-ribbon]').innerHTML =
      '<div class="ur-bar">' + P.map(function (p, i) {
        return '<button type="button" class="ur-seg' + (p.current ? ' is-now' : '') + '" data-p="' + i + '" style="flex:' + Math.max(dur(p), .6) + '" aria-label="' + esc(ORD[i] + ': ' + p.name) + '"><span>' + (i + 1) + '</span></button>';
      }).join('') + '</div><div class="ur-scale"><span>' + y0 + '</span><span>' + Math.round(y0 + span / 2) + '</span><span>' + y1 + '</span></div>';
    $('[data-u-pres]').innerHTML = P.map(function (p, i) {
      return '<li class="up-card' + (p.current ? ' is-now' : '') + '" data-p="' + i + '" tabindex="0">' +
        '<span class="up-ord" aria-hidden="true">' + (i + 1) + '</span>' +
        '<span class="up-ph">' + (p.photo ? '<img src="' + esc(p.photo) + '" alt="' + esc(p.name) + '" loading="lazy">' : '') + '</span>' +
        '<span class="up-k">الرئيس ' + ORD[i] + (p.current ? ' · الحالي' : '') + '</span>' +
        '<small class="up-t">' + esc(p.title || '') + '</small><b class="up-n">' + esc(p.name) + '</b>' +
        '<span class="up-y">' + p.from + ' – ' + (p.to || 'الآن') + '</span><span class="up-d">' + durTxt(p) + (i === longest && !p.current ? ' · الأطول' : '') + '</span>' +
        (p.note ? '<small class="up-note">' + esc(p.note) + '</small>' : '') + '</li>';
    }).join('');
    var rail = $('[data-u-pres]');
    var activePresident = 0, presidentTimer, manual = false, dragged = false;
    var pausePresidents = function () { manual = true; clearInterval(presidentTimer); };
    var pick = function (i, scroll) {
      activePresident = i;
      [].forEach.call(root.querySelectorAll('#u-pres [data-p]'), function (el) { el.classList.toggle('is-on', +el.dataset.p === i); });
      var c = rail.querySelector('.up-card[data-p="' + i + '"]');
      // سحب الصف أفقيًا فقط حتى تتوسط البطاقة (يعمل مع اتجاه الصفحة العربي)
      if (scroll && c && rail.scrollWidth > rail.clientWidth) rail.scrollBy({ left: c.getBoundingClientRect().left + c.offsetWidth / 2 - (rail.getBoundingClientRect().left + rail.clientWidth / 2), behavior: 'smooth' });
    };
    $('#u-pres').addEventListener('click', function (e) { var t = e.target.closest('[data-p]'); if (t && !dragged) { pausePresidents(); pick(+t.dataset.p, true); } });
    $('#u-pres').addEventListener('keydown', function (e) { var t = e.target.closest('.up-card'); if (t && (e.key === 'Enter' || e.key === ' ')) { e.preventDefault(); pausePresidents(); pick(+t.dataset.p, true); } });
    // الانتقال التلقائي يتوقف عند اختيار بطاقة أو بدء السحب اليدوي.
    pick(0, false);
    presidentTimer = setInterval(function () {
      if (!document.hidden) pick((activePresident + 1) % P.length, true);
    }, 3000);
    rail.addEventListener('pointerdown', function () { pausePresidents(); dragged = false; });
    rail.addEventListener('wheel', pausePresidents, { passive: true });
    rail.addEventListener('dragstart', function (e) { e.preventDefault(); });
    var dragStart = null;
    rail.addEventListener('pointerdown', function (e) {
      if (e.pointerType !== 'mouse' || e.button !== 0) return;
      dragStart = { x: e.clientX, left: rail.scrollLeft, id: e.pointerId };
    });
    rail.addEventListener('pointermove', function (e) {
      if (!dragStart) return;
      var delta = e.clientX - dragStart.x;
      if (Math.abs(delta) > 5) {
        dragged = true;
        rail.setPointerCapture(e.pointerId);
        rail.style.scrollSnapType = 'none';
        rail.style.userSelect = 'none';
      }
      if (dragged) rail.scrollLeft = dragStart.left - delta;
    });
    var endDrag = function () {
      dragStart = null;
      rail.style.scrollSnapType = '';
      rail.style.userSelect = '';
      setTimeout(function () { dragged = false; }, 0);
    };
    window.addEventListener('pointerup', endDrag);
    rail.addEventListener('pointercancel', endDrag);
    var scrollTimer;
    rail.addEventListener('scroll', function () {
      if (!manual) return;
      clearTimeout(scrollTimer);
      scrollTimer = setTimeout(function () {
        var center = rail.getBoundingClientRect().left + rail.clientWidth / 2;
        var nearest = 0, distance = Infinity;
        [].forEach.call(rail.children, function (card, i) {
          var r = card.getBoundingClientRect(), d = Math.abs(r.left + r.width / 2 - center);
          if (d < distance) { distance = d; nearest = i; }
        });
        pick(nearest, false);
      }, 150);
    }, { passive: true });
  } else if (!(u.leaders || []).length) hide('#u-pres');

  // ---------- كلمة الرئيس الحالي: أسفل الرؤساء، بتصميم «كلمة الوزير» (js/leader-word.js) ----------
  // البيانات في u.word: { title, name, role, photo (PNG مفرّغ), paras: [...], source: [نص, رابط] }
  if (u.word && window.InjazWord) {
    var ws = $('#u-word');
    ws.hidden = false;
    $('[data-u-word-h]').textContent = 'كلمة رئيس ' + (u.group === 'college' ? 'الكلية' : 'الجامعة');
    InjazWord.mount($('[data-u-word]'), { title: u.word.title, name: u.word.name, role: u.word.role, photo: u.word.photo, paras: u.word.paras, source: u.word.source });
  }

  // ---------- التصنيف: أعمدة، الأقصر رقمًا أعلى ----------
  if (u.ranks && u.ranks.items.length) {
    var R = u.ranks.items, worst = Math.max.apply(null, R.map(function (x) { return x[1]; })), best = Math.min.apply(null, R.map(function (x) { return x[1]; }));
    $('[data-u-ranks]').innerHTML = '<div class="uk-cols">' + R.map(function (x, i) {
      var h = 38 + 62 * (worst === best ? 1 : (worst - x[1]) / (worst - best)), d = i ? R[i - 1][1] - x[1] : 0;
      return '<span class="uk-col' + (i === R.length - 1 ? ' is-last' : '') + '"><span class="uk-bar" style="height:' + h.toFixed(0) + '%"><b>' + x[1] + '</b>' + (d > 0 ? '<em>▲ ' + d + '</em>' : '') + '</span><small>' + x[0] + '</small></span>';
    }).join('') + '</div><p class="uk-l">' + esc(u.ranks.label) + ' (كلما قلّ الرقم كان أفضل)</p>' +
      (u.ranks.highlights ? '<div class="uk-hl">' + u.ranks.highlights.map(function (h) { return '<span><b>' + esc(h[1]) + '</b>' + esc(h[0]) + '</span>'; }).join('') + '</div><small class="uk-note">' + esc(u.ranks.hlNote || '') + '</small>' : '');
  } else hide('#u-ranks');

  // ---------- المحطات ----------
  if (u.milestones && u.milestones.length) {
    $('[data-u-ms]').innerHTML = u.milestones.map(function (m) { return '<li><span>' + m.year + '</span><b>' + esc(m.title) + '</b></li>'; }).join('');
  } else hide('#u-ms');

  // ---------- المصادر ----------
  var src = $('[data-u-src]');
  if (u.sources && u.sources.length) src.innerHTML = 'المصادر: ' + u.sources.map(function (s) { return '<a href="' + esc(s[1]) + '" target="_blank" rel="noopener">' + esc(s[0]) + '</a>'; }).join(' · ');
  else src.hidden = true;

  // ---------- المنجزون (ديناميكي) ----------
  var peopleBox = $('[data-u-people]');
  // ---------- مربع أخبار الإنجازات: إنجاز رئيسي وست بطاقات، وشريط إلى صفحة «إنجازات الجامعة» ----------
  var news = $('[data-u-news]'), newsList = $('[data-un-list]');
  var allUrl = 'uni-achievements.html?org=' + encodeURIComponent(slug);
  $('[data-u-news-title]').textContent = 'إنجازات ' + u.name;
  $('[data-un-more]').href = allUrl;
  $('[data-un-more] span').textContent = 'كل إنجازات ' + kind;
  var newsRow = function (a, i) {
    var meta = esc(a.who) + (a.when ? ' · ' + esc(a.when) : '');
    if (i === 0) return '<a class="un-top" href="' + esc(a.href || '#') + '"><img src="' + esc(a.img) + '" alt="" style="' + esc(a.style) + '">' +
      '<span class="un-top-b"><span class="un-k un-' + a.type + '">' + esc(a.typeLabel) + '</span><b>' + esc(a.title) + '</b><small>' + meta + '</small></span></a>';
    return '<a class="un-row" href="' + esc(a.href || '#') + '"><span class="un-th"><img src="' + esc(a.img) + '" alt="" loading="lazy" style="' + esc(a.style) + '"></span>' +
      '<span class="un-b"><span class="un-k un-' + a.type + '">' + esc(a.typeLabel) + '</span><b>' + esc(a.title) + '</b><small>' + meta + '</small></span></a>';
  };

  if (!window.InjazLinks) return;
  InjazLinks.forOrg(slug).then(function (d) {
    $('[data-u-pcount]').textContent = d.people.length ? pl(d.people.length, 'منجز واحد', 'منجزان', 'منجزين', 'منجزًا') : '';
    // عرض حتى ثمانية منجزين حقيقيين، مع رابط مستقل للقائمة الكاملة.
    var more = 'uni-people.html?org=' + encodeURIComponent(slug);
    peopleBox.innerHTML = d.people.length ? '<div class="us-grid">' + d.people.slice(0, 8).map(function (p) {
      return '<a class="us" href="' + esc(p.href || '#') + '"><span class="us-ring"><span class="us-ph"><img src="' + esc(p.img) + '" alt="" loading="lazy" style="' + esc(p.style) + '"></span></span>' +
        '<b>' + esc(p.name) + '</b><small>' + esc(p.kindLabel) + '</small></a>';
    }).join('') + '</div><a class="us-more" href="' + more + '"><span>عرض جميع المنجزين</span><b>' + ar(d.people.length) + '</b><i aria-hidden="true">←</i></a>'
      : '<p class="u-soon">لا يوجد منجزون مرتبطون بهذه المؤسسة بعد.</p>';
  }).catch(function () { peopleBox.innerHTML = '<p class="u-soon">تظهر الروابط عند فتح الموقع من الخادم (Live Server).</p>'; });

  InjazLinks.achievementsFor(slug).then(function (list) {
    var n = function (t) { return list.filter(function (a) { return a.type === t; }).length; };
    $('[data-un-types]').innerHTML = [['org', 'المؤسسة'], ['student', 'الطلبة'], ['staff', 'الأكاديميون']].map(function (t) {
      return '<a href="' + allUrl + '&type=' + t[0] + '"><b>' + ar(n(t[0])) + '</b>' + t[1] + '</a>';
    }).join('');
    $('[data-un-total]').textContent = ar(list.length);
    newsList.innerHTML = list.length ? list.slice(0, 7).map(newsRow).join('') : '<p class="u-soon">لا توجد إنجازات مرتبطة بهذه المؤسسة بعد.</p>';
    $('[data-un-more]').hidden = !list.length;
  }).catch(function () { newsList.innerHTML = '<p class="u-soon">تظهر الإنجازات عند فتح الموقع من الخادم (Live Server).</p>'; });
})();
