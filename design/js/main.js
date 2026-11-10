/* إنجازات — تفاعلات نسخة الموبايل */
(function () {
  'use strict';
  var $ = function (s, r) { return (r || document).querySelector(s); };
  var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };

  /* ---------- القائمة بملء الشاشة ---------- */
  var menu = $('#menu');
  var openBtn = $('[data-menu-open]');
  function setMenu(open) {
    if (!menu) return;
    menu.classList.toggle('open', open);
    document.body.classList.toggle('menu-open', open);
    if (openBtn) openBtn.setAttribute('aria-expanded', String(open));
    if (open) { var f = $('input', menu); setTimeout(function () { ($('[data-menu-close]', menu) || f).focus(); }, 60); }
    else if (openBtn) openBtn.focus();
  }
  if (openBtn) openBtn.addEventListener('click', function () { setMenu(true); });
  $$('[data-menu-close]').forEach(function (b) { b.addEventListener('click', function () { setMenu(false); }); });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && menu && menu.classList.contains('open')) setMenu(false); });
  $$('#menu nav a').forEach(function (a) { a.addEventListener('click', function () { setMenu(false); }); });
  if (location.hash === '#menu') setMenu(true);

  /* ---------- شريط المنجزين (الرئيسية) ---------- */
  var strip = $('[data-strip]');
  if (strip) {
    var row = $('.strip', strip);
    var items = $$('button', row);
    var motion = window.matchMedia('(prefers-reduced-motion: reduce)');
    var paused = motion.matches;
    var timer;
    function show() {
      items.forEach(function (button, i) {
        button.hidden = i >= 5;
        button.classList.toggle('center', i === 2);
        button.classList.toggle('edge', i === 0 || i === 4);
        button.setAttribute('aria-current', i === 2 ? 'true' : 'false');
        row.appendChild(button);
      });
    }
    var moving = false;
    var pendingMove = null;
    function reorder(request) {
      // Finish the current movement before honoring the latest interaction.
      if (moving) { pendingMove = request; return; }
      var shift = typeof request === 'number' ? request : items.indexOf(request) - 2;
      if (!shift) return;
      var previous = items.slice();
      var oldSlots = previous.slice(0, 5).map(function (button) { return button.getBoundingClientRect(); });
      var copies = previous.slice(0, 5).map(function (button) { return button.cloneNode(true); });
      var count = items.length;
      items = previous.slice((shift + count) % count).concat(previous.slice(0, (shift + count) % count));
      show();
      if (motion.matches) return;
      moving = true;
      var newSlots = items.slice(0, 5).map(function (button) { return button.getBoundingClientRect(); });
      var rowRect = row.getBoundingClientRect();
      var animations = [];
      var ghosts = [];
      // Extend the actual grid beyond either edge, preserving portrait spacing.
      function slot(rects, index) {
        if (index >= 0 && index < 5) return rects[index];
        var edge = index < 0 ? 0 : 4;
        var neighbor = index < 0 ? 1 : 3;
        var step = (rects[edge].left - rects[neighbor].left) / (edge - neighbor);
        return { left: rects[edge].left + (index - edge) * step,
          top: rects[edge].top, width: rects[edge].width, height: rects[edge].height };
      }
      function animate(button, from, to) {
        if (!to.width || !to.height) return;
        animations.push(button.animate([
          { transform: 'translate(' + (from.left - to.left) + 'px,' + (from.top - to.top) + 'px) scale(' + (from.width / to.width) + ',' + (from.height / to.height) + ')' },
          { transform: 'translate(0,0) scale(1,1)' }
        ], { duration: 650, easing: 'cubic-bezier(.22,.61,.36,1)' }));
      }
      previous.slice(0, 5).forEach(function (button, i) {
        if (!button.hidden) return;
        var from = oldSlots[i];
        var to = slot(newSlots, i - shift);
        var ghost = copies[i];
        ghost.hidden = false;
        ghost.classList.add('strip-ghost');
        ghost.setAttribute('aria-hidden', 'true');
        ghost.tabIndex = -1;
        ghost.inert = true;
        Object.assign(ghost.style, { left: (to.left - rowRect.left) + 'px', top: (to.top - rowRect.top) + 'px', width: to.width + 'px', height: to.height + 'px' });
        row.appendChild(ghost);
        ghosts.push(ghost);
        animate(ghost, from, to);
      });
      items.slice(0, 5).forEach(function (button, i) {
        animate(button, slot(oldSlots, i + shift), newSlots[i]);
      });
      Promise.all(animations.map(function (animation) { return animation.finished.catch(function () {}); })).then(function () {
        ghosts.forEach(function (ghost) { ghost.remove(); });
        moving = false;
        var next = pendingMove;
        pendingMove = null;
        if (next !== null) reorder(next);
      });
    }
    function moveNext() { reorder(1); }
    function movePrev() { reorder(-1); }
    function schedule() {
      clearInterval(timer);
      if (!paused) timer = setInterval(function () {
        if (!document.hidden && !(strip.contains(document.activeElement) && document.activeElement.matches(':focus-visible'))) moveNext();
      }, 3000);
    }
    $('[data-strip-next]', strip).addEventListener('click', function () { moveNext(); schedule(); });
    $('[data-strip-prev]', strip).addEventListener('click', function () { movePrev(); schedule(); });
    items.forEach(function (button) {
      // Tapping a portrait opens the achiever's page.
      button.addEventListener('click', function () {
        location.href = button.getAttribute('data-href') || 'profile.html';
      });
    });
    show();
    schedule();
  }

  /* ---------- قصتي: اقرأ المزيد ---------- */
  $$('[data-story]').forEach(function (card) {
    var btn = $('.toggle', card);
    btn.addEventListener('click', function () {
      var open = card.classList.toggle('open');
      btn.setAttribute('aria-expanded', String(open));
      $('span', btn).textContent = open ? 'إخفاء' : 'اقرأ المزيد';
    });
  });

  /* ---------- اسم القسم بجانب الشعار (يتغير أثناء التمرير) ---------- */
  var crumb = $('.crumb');
  if (crumb) {
    // صفحة المنجز: اسمه بخط صغير تحت «المنجزين» — يُقرأ تلقائيًا من عنوان الصفحة
    // (أو من data-sub على .crumb إن وُجد)، فيعمل لكل منجز يُضاف لاحقًا.
    // صفحة المنجز: عنوانها، وصفحة الإنجاز: العنصر المعلَّم بـ data-crumb-sub (اسم المنجز)
    var subHead = $('.profile-hero h1') || $('[data-crumb-sub]');
    var sub = crumb.dataset.sub || (subHead && subHead.textContent.trim());
    if (sub) {
      var title = crumb.textContent.trim();
      crumb.classList.add('has-sub');
      crumb.textContent = '';
      var t1 = document.createElement('span'); t1.className = 'crumb-title'; t1.textContent = title;
      var t2 = document.createElement('small'); t2.className = 'crumb-sub'; t2.textContent = sub;
      crumb.appendChild(t1); crumb.appendChild(t2);
    }
    var heads = $$('main .sec-title h2');
    var current = crumb.textContent;
    var setCrumb = function (t) {
      if (t === current) return;
      current = t; crumb.classList.add('swap');
      setTimeout(function () { crumb.textContent = t; crumb.classList.remove('swap'); }, 160);
    };
    var spy = function () {
      var t = crumb.dataset.page;
      heads.forEach(function (h) { if (h.getBoundingClientRect().top < 110) t = h.textContent.trim(); });
      setCrumb(t);
    };
    var ticking = false;
    // الاسم ثابت بجانب الشعار (لا يتغير أثناء التمرير)

    // الضغط على اسم القسم يرجع إلى صفحة القسم الرئيسية (في كل صفحات الموقع)
    var HOME = { 'الرئيسية': 'index.html', 'الإنجازات': 'achievements.html', 'المنجزين': 'achievers.html',
      'المؤسسات': 'institutions.html', 'جوائز السلطان قابوس': 'awards.html', 'جائزة بلعرب': 'bilarab-award.html', 'الجامعات والكليات': 'universities.html', 'المقالات': 'articles.html',
      'ملهمون': 'inspiring.html', 'أقلام إنجازات': 'writers.html', 'الأخبار': 'news.html', 'المختبر': 'science.html',
      'تواصل معنا': 'contact.html', 'من نحن': 'about.html' };
    var secName = (crumb.dataset.page || ($('.crumb-title', crumb) || crumb).textContent).trim();
    var home = HOME[secName];
    var here = (location.pathname.split('/').pop() || 'index.html');
    if (home && crumb.tagName !== 'A' && home !== here) {
      crumb.classList.add('is-link');
      crumb.setAttribute('role', 'link');
      crumb.setAttribute('tabindex', '0');
      crumb.setAttribute('title', 'العودة إلى ' + secName);
      var go = function () { location.href = home; };
      crumb.addEventListener('click', go);
      crumb.addEventListener('keydown', function (e) { if (e.key === 'Enter') go(); });
    }
  }

  /* ---------- التصفية: المجال ← القسم، الجنس، السنة / الترتيب في ملف المنجز: السنة، نوع الإنجاز ---------- */
  // أقسام تجريبية لكل مجال (تُستبدل ببيانات الموقع)
  var SECTIONS = {
    'الرياضة': ['كرة القدم', 'كرة السلة', 'ألعاب القوى', 'السباحة', 'الكرة الطائرة', 'الجولف'],
    'الثقافة': ['الأدب', 'الشعر', 'القراءة'],
    'العلوم': ['البحث العلمي', 'الاختراعات', 'الأولمبياد العلمي'],
    'العسكرية': ['الرماية', 'التميز العسكري'],
    'الطبي': ['البحث الطبي', 'التميز الطبي'],
    'الإبتكارات': ['الاختراعات', 'المشاريع الابتكارية'],
    'الأدب': ['الرواية', 'القصة', 'الشعر'],
    'طلبة المدارس': ['المسابقات', 'الأولمبياد'],
    'الفنون': ['الفنون التشكيلية', 'الموسيقى', 'التصوير'],
    'ريادة الأعمال': ['الشركات الناشئة', 'المشاريع']
  };
  var FIRST_YEAR = 1970, LAST_YEAR = new Date().getFullYear();
  var ft = $('[data-filter-toggle]'), fp = $('[data-filter-panel]');
  if (ft && fp) {
    var cats = $('[data-cats]'), sheet = $('[data-sheet]', fp);
    var catChips = $$('[data-chips] .chip');
    var boxes = {}; $$('.f-box', fp).forEach(function (b) { boxes[b.dataset.key] = b; });
    var st = { field: '', section: '', gender: '', type: '', from: '', to: '' }, openKey = null;

    var yearText = function () {
      if (!st.from && !st.to) return '';
      if (st.from && st.to) return st.from === st.to ? st.from : st.from + '–' + st.to;
      return st.from ? 'من ' + st.from : 'حتى ' + st.to;
    };
    var paint = function () {
      var vals = { field: st.field, section: st.section, gender: st.gender, type: st.type, year: yearText() };
      Object.keys(boxes).forEach(function (k) {
        $('.f-val', boxes[k]).textContent = vals[k] || 'الكل';
        boxes[k].classList.toggle('set', !!vals[k]);
        boxes[k].setAttribute('aria-expanded', String(openKey === k));
      });
      if (boxes.section) boxes.section.classList.toggle('disabled', !st.field);
    };
    var opts = function (list, cur, onPick) {
      sheet.innerHTML = '<div class="f-opts">' + list.map(function (o) {
        return '<button type="button" class="f-opt" aria-pressed="' + (o === cur) + '">' + o + '</button>';
      }).join('') + '</div>';
      $$('.f-opt', sheet).forEach(function (b) { b.addEventListener('click', function () { onPick(b.textContent === cur ? '' : b.textContent); }); });
    };
    var yearOptions = function (sel) {
      var h = '<option value="">—</option>';
      for (var y = LAST_YEAR; y >= FIRST_YEAR; y--) h += '<option' + (String(y) === sel ? ' selected' : '') + '>' + y + '</option>';
      return h;
    };
    var showSheet = function (key) {
      openKey = openKey === key ? null : key;
      paint();
      if (!openKey) { sheet.hidden = true; return; }
      sheet.hidden = false;
      if (key === 'field') opts(Object.keys(SECTIONS), st.field, function (v) { st.field = v; st.section = ''; applyField(); openKey = null; showSheet(v ? 'section' : null); });
      if (key === 'section') {
        if (!st.field) { sheet.innerHTML = '<p class="f-hint">اختر المجال أولاً</p>'; return; }
        opts(SECTIONS[st.field], st.section, function (v) { st.section = v; openKey = key; showSheet(key); });
      }
      if (key === 'type') opts(['خليجي', 'عربي', 'إقليمي', 'دولي'], st.type, function (v) { st.type = v; openKey = key; showSheet(key); });
      if (key === 'gender') opts(['ذكر', 'أنثى'], st.gender, function (v) { st.gender = v; openKey = key; showSheet(key); });
      if (key === 'year') {
        sheet.innerHTML = '<div class="f-years"><label>من سنة<select data-y="from">' + yearOptions(st.from) + '</select></label>' +
          '<label>إلى سنة<select data-y="to">' + yearOptions(st.to) + '</select></label></div>';
        $$('select', sheet).forEach(function (sel) {
          sel.addEventListener('change', function () {
            st[sel.dataset.y] = sel.value;
            if (st.from && st.to && +st.from > +st.to) { var t = st.from; st.from = st.to; st.to = t; openKey = null; showSheet('year'); return; }
            paint();
          });
        });
      }
    };
    var applyField = function () { activeCat = st.field || '*'; if (typeof applyFilter === 'function') applyFilter(); };
    Object.keys(boxes).forEach(function (k) { boxes[k].addEventListener('click', function () { showSheet(k); }); });

    ft.addEventListener('click', function () {
      var open = !fp.classList.contains('open');
      fp.classList.toggle('open', open);
      ft.setAttribute('aria-expanded', String(open));
      if (cats) cats.classList.toggle('hide', open);
      if (!boxes.field) { if (!open) { openKey = null; sheet.hidden = true; paint(); } return; }
      if (open) {
        // المجال المختار ينتقل من الرقائق إلى مربع «المجال»
        var act = catChips.filter(function (c) { return c.classList.contains('active') && c.dataset.filter !== '*'; })[0];
        st.field = act ? $('span', act).textContent : st.field;
        catChips.forEach(function (c) { c.classList.remove('active'); c.setAttribute('aria-pressed', 'false'); });
        paint();
      } else {
        openKey = null; sheet.hidden = true; paint();
        var match = catChips.filter(function (c) { return $('span', c).textContent === st.field; })[0] ||
                    catChips.filter(function (c) { return c.dataset.filter === '*'; })[0];
        if (match) { match.classList.add('active'); match.setAttribute('aria-pressed', 'true'); }
      }
    });
    var reset = $('[data-filter-reset]', fp);
    if (reset) reset.addEventListener('click', function () {
      st = { field: '', section: '', gender: '', type: '', from: '', to: '' }; openKey = null; sheet.hidden = true; paint(); applyField();
    });
    paint();
  }

  /* ---------- كلمة المؤسس: عرض كامل ---------- */
  $$('[data-expand]').forEach(function (box) {
    var btn = $('.more', box);
    btn.addEventListener('click', function () {
      var open = box.classList.toggle('open');
      btn.setAttribute('aria-expanded', String(open));
      $('span', btn).textContent = open ? 'إخفاء' : 'اقرأ الكلمة كاملة';
    });
  });

  /* ---------- رقائق التصنيف + البحث ---------- */
  var results = $('[data-results]');
  var empty = $('.empty');
  var activeCat = '*';
  var activeOrg = ''; // تصفية حسب الاتحاد/المؤسسة (js/federations.js)
  var activeGender = ''; // تصفية حسب الجنس (data-gender على البطاقة)
  var yearFrom = '', yearTo = ''; // تصفية حسب السنة: من – إلى (من data-date على البطاقة)
  var activeScope = ''; // المستوى: local / gulf / arab / international (data-scope)
  var query = '';
  // ترقيم فعلي: يعمل عندما تحمل القائمة data-page-size ويوجد [data-pager]
  var pageSize = results ? +results.dataset.pageSize || 0 : 0;
  // على الكمبيوتر يمكن تحديد عدد مختلف للصفحة (data-page-size-desktop) حتى تمتلئ الأسطر بلا فراغ؛ الهاتف لا يتغير
  if (results && results.dataset.pageSizeDesktop && window.matchMedia && matchMedia('(min-width: 1100px)').matches) pageSize = +results.dataset.pageSizeDesktop || pageSize;
  var pagerEl = $('[data-pager]');
  var page = 1;
  var toArabic = function (n) { return String(n).replace(/\d/g, function (d) { return '٠١٢٣٤٥٦٧٨٩'[d]; }); };
  function renderPager(pages) {
    if (!pagerEl) return;
    pagerEl.hidden = pages <= 1;
    if (pages <= 1) { pagerEl.innerHTML = ''; return; }
    var h = '<a class="arr" href="#" data-go="' + (page - 1) + '" aria-label="الصفحة السابقة"' + (page === 1 ? ' aria-disabled="true"' : '') + '><img src="assets/svg/arrow-right.svg" alt=""></a>';
    for (var i = 1; i <= pages; i++) h += '<a href="#" data-go="' + i + '"' + (i === page ? ' aria-current="page"' : '') + ' aria-label="الصفحة ' + i + '">' + toArabic(i) + '</a>';
    h += '<a class="arr" href="#" data-go="' + (page + 1) + '" aria-label="الصفحة التالية"' + (page === pages ? ' aria-disabled="true"' : '') + '><img src="assets/svg/arrow-left.svg" alt=""></a>';
    pagerEl.innerHTML = h;
  }
  if (pagerEl) pagerEl.addEventListener('click', function (e) {
    var a = e.target.closest('a[data-go]'); if (!a) return;
    e.preventDefault();
    if (a.getAttribute('aria-disabled') === 'true') return;
    page = +a.dataset.go; applyFilter(true);
    results.scrollIntoView({ behavior: matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth', block: 'start' });
  });
  function norm(t) {
    return String(t || '').toLowerCase().replace(/[\u064B-\u0652\u0640]/g, '').replace(/[أإآٱ]/g, 'ا').replace(/ة/g, 'ه').replace(/ى/g, 'ي').replace(/\s+/g, ' ');
  }
  function applyFilter(keepPage) {
    if (!results) return;
    if (!keepPage) page = 1;
    var matches = [];
    $$(':scope > *', results).forEach(function (el) {
      var cat = el.dataset.cat || '';
      // البحث يشمل الاسم والمجال ونص البطاقة، مع توحيد الهمزات والتاء المربوطة والألف المقصورة والتشكيل
      var txt = norm([el.dataset.name, cat, el.dataset.affil, el.textContent].join(' '));
      var ok = (activeCat === '*' || cat === activeCat) && (!query || txt.indexOf(norm(query)) > -1)
        && (!activeOrg || (' ' + (el.dataset.orgs || '') + ' ').indexOf(' ' + activeOrg + ' ') > -1)
        && (!activeGender || el.dataset.gender === activeGender)
        && (!yearFrom || (el.dataset.date || '').slice(0, 4) >= yearFrom)
        && (!yearTo || (el.dataset.date || '0000').slice(0, 4) <= yearTo)
        && (!activeScope || el.dataset.scope === activeScope);
      // الإنجاز المعروض في لوحة الشرف لا يتكرر في القائمة عند عرض «الكل» بلا بحث
      if (el.hasAttribute('data-featured') && activeCat === '*' && !query) ok = false;
      el.hidden = true; if (ok) matches.push(el);
    });
    var pages = pageSize ? Math.max(1, Math.ceil(matches.length / pageSize)) : 1;
    if (page > pages) page = pages;
    matches.forEach(function (el, i) { el.hidden = pageSize ? Math.floor(i / pageSize) + 1 !== page : false; });
    if (pageSize) renderPager(matches.length ? pages : 0);
    // بطاقة عريضة واحدة في ترتيب محدد من كل صفحة (مثلاً السابعة: سطران ثم عريضة ثم سطران)
    var wideAt = +results.dataset.wideAt || 0;
    if (wideAt) matches.filter(function (el) { return !el.hidden; }).forEach(function (el, i) { el.classList.toggle('wide', i === wideAt - 1); });
    if (empty) empty.hidden = matches.length > 0;
    var countEl = $('[data-res-count]');
    if (countEl) {
      var n = matches.length;
      countEl.textContent = countEl.dataset.unit === 'achievers'
        ? (n === 0 ? 'لا يوجد منجزون' : n === 1 ? 'منجز واحد' : n === 2 ? 'منجزان' : toArabic(n) + (n <= 10 ? ' منجزين' : ' منجزًا'))
        : (n === 0 ? 'لا توجد إنجازات' : n === 1 ? 'إنجاز واحد' : n === 2 ? 'إنجازان' : toArabic(n) + (n <= 10 ? ' إنجازات' : ' إنجازًا'));
    }
  }
  // واجهة صغيرة لملف الاتحادات: تعيين الاتحاد المختار وقراءة التصنيف الحالي
  window.injazFilter = { setOrg: function (slug) { activeOrg = slug || ''; applyFilter(); }, setGender: function (g) { activeGender = g || ''; applyFilter(); }, cat: function () { return activeCat; } };
  // مربع «فلترة» في صفحة الإنجازات (js/ach-filters.js): الجنس، السنة من – إلى، المستوى، الترتيب
  window.injazFilter.get = function () { return { gender: activeGender, from: yearFrom, to: yearTo, scope: activeScope }; };
  window.injazFilter.set = function (o) {
    if ('gender' in o) activeGender = o.gender || '';
    if ('from' in o) yearFrom = o.from || '';
    if ('to' in o) yearTo = o.to || '';
    if ('scope' in o) activeScope = o.scope || '';
    applyFilter();
  };
  // الترتيب: تُعاد البطاقات في الصفحة حسب التاريخ (الأحدث أو الأقدم)
  window.injazFilter.sort = function (order) {
    if (!results) return;
    $$(':scope > *', results).sort(function (a, b) {
      var x = a.dataset.date || '', y = b.dataset.date || '';
      return order === 'old' ? (x < y ? -1 : x > y ? 1 : 0) : (x > y ? -1 : x < y ? 1 : 0);
    }).forEach(function (el) { results.appendChild(el); });
    applyFilter();
  };
  $$('[data-chips]').forEach(function (group) {
    $$('.chip', group).forEach(function (chip) {
      chip.addEventListener('click', function () {
        var f = chip.dataset.filter;
        if (f === '*filter') { chip.classList.toggle('active'); chip.setAttribute('aria-pressed', String(chip.classList.contains('active'))); return; }
        $$('.chip', group).forEach(function (c) { if (c.dataset.filter !== '*filter') { c.classList.remove('active'); c.setAttribute('aria-pressed', 'false'); } });
        chip.classList.add('active'); chip.setAttribute('aria-pressed', 'true');
        if (results && f && f.charAt(0) !== '*' || f === '*') { activeCat = f; applyFilter(); }
      });
    });
  });
  $$('main input[type=search]').forEach(function (inp) {
    inp.addEventListener('input', function () { query = inp.value.trim(); applyFilter(); });
  });
  var menuSearch = $('#menu form');
  if (menuSearch) menuSearch.addEventListener('submit', function (e) {
    e.preventDefault();
    var v = $('input', menuSearch).value.trim();
    location.href = 'achievers.html' + (v ? '?q=' + encodeURIComponent(v) : '');
  });
  var qp = new URLSearchParams(location.search).get('q');
  if (qp) { var mi = $('main input[type=search]'); if (mi) { mi.value = qp; query = qp; applyFilter(); } }
  if (pageSize) applyFilter();
  $$('[data-clear-all]').forEach(function (b) {
    b.addEventListener('click', function () {
      query = ''; $$('main input[type=search]').forEach(function (i) { i.value = ''; });
      var all = $('[data-chips] .chip[data-filter="*"]'); if (all) all.click(); else { activeCat = '*'; applyFilter(); }
      var r = $('[data-filter-reset]'); if (r) r.click();
      activeGender = ''; yearFrom = ''; yearTo = ''; activeScope = ''; applyFilter();
      document.dispatchEvent(new CustomEvent('injaz:filters-reset'));
    });
  });

  /* ---------- ترقيم الصفحات ---------- */
  $$('.pager:not([data-pager])').forEach(function (p) {
    var nums = $$('a:not(.arr)', p);
    var cur = function () { return nums.findIndex(function (a) { return a.getAttribute('aria-current'); }); };
    var go = function (i) { if (i < 0 || i >= nums.length) return; nums.forEach(function (a) { a.removeAttribute('aria-current'); }); nums[i].setAttribute('aria-current', 'page'); };
    nums.forEach(function (a, i) { a.addEventListener('click', function (e) { e.preventDefault(); go(i); }); });
    var arr = $$('a.arr', p);
    arr[0].addEventListener('click', function (e) { e.preventDefault(); go(cur() - 1); });
    arr[1].addEventListener('click', function (e) { e.preventDefault(); go(cur() + 1); });
  });

  /* ---------- قالوا عن إنجازات ---------- */
  $$('[data-slider]').forEach(function (s) {
    var view = $('.slider-viewport', s), track = $('.slider-track', s), slides = $$('.slide', s), dots = $$('.dots button', s), i = 0;
    if (!track || !view) return;
    var place = function () {
      var w = view.clientWidth;
      slides.forEach(function (el) { el.style.flexBasis = w + 'px'; el.style.width = w + 'px'; });
      track.style.transform = 'translate3d(' + (-i * w) + 'px,0,0)';
    };
    var go = function (n) {
      i = (n + slides.length) % slides.length;
      place();
      slides.forEach(function (el, k) { el.classList.toggle('active', k === i); });
      dots.forEach(function (d, k) { d.classList.toggle('active', k === i); d.setAttribute('aria-current', k === i ? 'true' : 'false'); });
    };
    $('[data-next]', s).addEventListener('click', function () { go(i + 1); });
    $('[data-prev]', s).addEventListener('click', function () { go(i - 1); });
    dots.forEach(function (d, k) { d.addEventListener('click', function () { go(k); }); });
    place();
    addEventListener('resize', place);
    var startX = 0, drag = 0, dragging = false;
    view.addEventListener('touchstart', function (e) {
      if (e.touches.length !== 1) return;
      dragging = true;
      startX = e.touches[0].clientX;
      drag = 0;
      track.style.transition = 'none';
    }, { passive: true });
    view.addEventListener('touchmove', function (e) {
      if (!dragging) return;
      drag = e.touches[0].clientX - startX;
      if (Math.abs(drag) > 10) e.preventDefault();
      track.style.transform = 'translate3d(' + (-i * view.clientWidth + drag) + 'px,0,0)';
    }, { passive: false });
    var end = function () {
      if (!dragging) return;
      dragging = false;
      track.style.transition = '';
      if (drag <= -48) go(i + 1);
      else if (drag >= 48) go(i - 1);
      else place();
      drag = 0;
    };
    view.addEventListener('touchend', end);
    view.addEventListener('touchcancel', end);
  });

  /* ---------- مشاركة ---------- */
  $$('[data-share]').forEach(function (b) {
    b.addEventListener('click', function () {
      if (navigator.share) navigator.share({ title: document.title, url: location.href }).catch(function () {});
      else if (navigator.clipboard) navigator.clipboard.writeText(location.href);
    });
  });

  /* ---------- نموذج التواصل ---------- */
  var form = $('[data-form]');
  if (form) form.addEventListener('submit', function (e) {
    e.preventDefault();
    var bad = $$('input, textarea', form).filter(function (f) { var v = !f.checkValidity() || !f.value.trim(); f.classList.toggle('err', v); return v; });
    var note = $('.note', form);
    if (bad.length) { note.style.color = '#F2B8B5'; note.textContent = 'يرجى تعبئة جميع الحقول بشكل صحيح'; bad[0].focus(); return; }
    note.style.color = ''; note.textContent = 'تم إرسال رسالتك، شكراً لتواصلك معنا'; form.reset();
  });

  /* روابط تجريبية بدون وجهة */
  $$('a[href="#"]').forEach(function (a) { if (!a.closest('.pager')) a.addEventListener('click', function (e) { e.preventDefault(); }); });
})();
