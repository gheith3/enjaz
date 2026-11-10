/* صفحة «جائزة السلطان قابوس للثقافة والفنون والآداب» (award.html)
   البيانات من js/award-data.js. السنوات بالأرقام اللاتينية (كبقية الموقع)، والأعداد بالعربية. */
(function () {
  var A = window.INJAZ_AWARD, root = document.querySelector('[data-award]');
  if (!A || !root) return;
  var $ = function (s) { return root.querySelector(s); };
  var ar = function (n) { return String(n).replace(/\d/g, function (d) { return '٠١٢٣٤٥٦٧٨٩'[d]; }); };
  var esc = function (s) { var d = document.createElement('div'); d.textContent = s == null ? '' : s; return d.innerHTML; };
  var B = A.branches, ORD = ['', 'الأولى', 'الثانية', 'الثالثة', 'الرابعة', 'الخامسة', 'السادسة', 'السابعة', 'الثامنة', 'التاسعة', 'العاشرة', 'الحادية عشرة', 'الثانية عشرة', 'الثالثة عشرة'];
  var photo = function (w) { return 'assets/img/award/winners/' + w.photo + '.jpg'; };
  var ICON = {
    culture: '<path d="M4 5.5C4 4.7 4.7 4 5.5 4H11v15H5.5A1.5 1.5 0 0 1 4 17.5z"/><path d="M20 5.5c0-.8-.7-1.5-1.5-1.5H13v15h5.5c.8 0 1.5-.7 1.5-1.5z"/>',
    arts: '<path d="M12 3a9 9 0 1 0 0 18c1.1 0 1.6-.8 1.6-1.6 0-1.3-1.1-1.6-1.1-2.6 0-.9.7-1.6 1.6-1.6H16a5 5 0 0 0 5-5C21 6.4 17 3 12 3z"/><circle cx="7.5" cy="11" r="1.2"/><circle cx="10.5" cy="7.3" r="1.2"/><circle cx="15" cy="7.8" r="1.2"/>',
    lit: '<path d="M19 4c-6 1-10.5 5.5-12 12l-2 4"/><path d="M19 4c.5 5-2.5 10-9 11.5"/><path d="M9 15.5 7 16"/>'
  };
  var svg = function (k) { return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' + ICON[k] + '</svg>'; };

  // كل الفائزين العُمانيين في قائمة واحدة
  var all = [];
  A.editions.forEach(function (e) { e.winners.forEach(function (w) { w.ed = e; all.push(w); }); });
  var count = function (b) { return all.filter(function (w) { return w.branch === b; }).length; };
  var inArab = all.filter(function (w) { return w.ed.type === 'arab'; }).length;
  var done = A.editions.length;

  // ---------- الأرقام ----------
  $('[data-aw-stats]').innerHTML = [
    [ar(all.length), 'تتويجًا عُمانيًا'], [ar(done), 'دورة منذ 2012'], [ar(inArab), 'في الدورات العربية'], ['٣', 'فروع']
  ].map(function (s) { return '<span><b>' + s[0] + '</b><small>' + s[1] + '</small></span>'; }).join('');

  // ---------- نبذة والأهداف والفروع الثلاثة (أشرطة أوسمة معلّقة) ----------
  $('[data-aw-about]').textContent = A.about;
  $('[data-aw-goals]').innerHTML = A.goals.map(function (g, i) { return '<li><b>' + ar(i + 1) + '</b>' + esc(g) + '</li>'; }).join('');
  $('[data-aw-pillars]').innerHTML = ['culture', 'arts', 'lit'].map(function (k) {
    return '<button type="button" class="aw-pil b-' + k + '" data-jump-branch="' + k + '"><span class="aw-pil-ic">' + svg(k) + '</span>' +
      '<b>' + B[k].name + '</b><small>' + esc(B[k].hint) + '</small><em><strong>' + ar(count(k)) + '</strong> فائزًا عُمانيًا</em></button>';
  }).join('');

  // ---------- كلمة رئيس مجلس الأمناء ----------
  if (A.word && window.InjazWord) InjazWord.mount($('[data-aw-word]'), A.word); else $('#aw-word').hidden = true;

  // ---------- سجل الأوسمة: خيط ذهبي بالدورات، ويتدلى من كل دورة أوسمة فائزيها ----------
  var st = { branch: '' };
  function medal(w) {
    var i = all.indexOf(w);
    return '<li class="aw-m b-' + w.branch + (w.ed.type === 'arab' ? ' is-arab' : '') + '"><button type="button" data-w="' + i + '" aria-label="' + esc(w.name) + '">' +
      '<span class="aw-rib" aria-hidden="true"></span>' +
      '<span class="aw-disc"><img src="' + photo(w) + '" alt="" loading="lazy"' + (w.group ? ' class="is-group"' : '') + '></span>' +
      '<b>' + esc(w.name) + '</b><small>' + esc(w.field) + '</small>' +
      '<i class="aw-chip">' + B[w.branch].name + '</i></button></li>';
  }
  // الرابط اختياري لكل فائز، وليس مرتبطًا باسم أو فرع محدد.
  function winnerProfileLink(w) {
    var href = typeof w.profile === 'string' ? w.profile.trim() : '';
    return href ? '<a class="aw-card-go" href="' + esc(href) + '" aria-label="ملف المنجز: ' + esc(w.name) + '">ملف المنجز ←</a>' : '';
  }
  // نلائم المحتوى مع ارتفاع الشاشة مع إبقاء البطاقة بلا تمرير داخلي.
  function fitWinnerPreview() {
    var pane = $('[data-aw-preview]'), content = pane && pane.querySelector('.aw-preview-content');
    if (!content || !matchMedia('(min-width: 1100px)').matches) return;
    var available = Math.max(1, window.innerHeight - 100);
    function setScale(scale) {
      content.style.zoom = scale;
      content.style.width = (100 / scale) + '%';
    }
    setScale(1);
    if (pane.getBoundingClientRect().height <= available) return;
    var low = 0.1, high = 1;
    for (var i = 0; i < 12; i++) {
      var mid = (low + high) / 2;
      setScale(mid);
      if (pane.getBoundingClientRect().height <= available) low = mid; else high = mid;
    }
    setScale(low);
  }
  // تفاصيل الفائز في المساحة اليسرى على الكمبيوتر، مع بقاء الأوسمة كما هي.
  function previewWinner(w) {
    var pane = $('[data-aw-preview]'); if (!pane) return;
    if (!w) { pane.innerHTML = '<p>لا يوجد فائزون في هذا الفرع.</p>'; return; }
    var prize = A.prizes[w.ed.type], index = all.indexOf(w);
    pane.className = 'aw-winner-preview b-' + w.branch;
    pane.innerHTML = '<div class="aw-preview-content"><div class="aw-preview-heading">' +
      '<span class="aw-preview-label">تفاصيل الوسام المختار</span>' +
      '<span class="aw-disc big"><img src="' + photo(w) + '" alt=""' + (w.group ? ' class="is-group"' : '') + '></span>' +
      '<div class="aw-preview-identity"><h3>' + esc(w.name) + '</h3><p class="aw-card-k">' + svg(w.branch) + 'فرع ' + esc(B[w.branch].name) + ' · ' + esc(w.field) + '</p></div></div>' +
      (w.why ? '<div class="aw-why"><span>نظير ماذا؟</span><p>' + esc(w.why) + '</p></div>' : '') +
      '<dl>' + (w.work ? '<div><dt>العمل الفائز</dt><dd>' + esc(w.work) + '</dd></div>' : '') +
      '<div><dt>الدورة</dt><dd>' + ORD[w.ed.n] + ' · ' + w.ed.year + ' (' + esc(prize.label) + ')</dd></div>' +
      '<div><dt>التكريم</dt><dd>' + esc(prize.medal) + '</dd></div>' +
      (w.ed.type === 'arab' ? '<div><dt>لماذا هو لافت؟</dt><dd>فاز في دورة مفتوحة للعرب جميعًا، ' + esc(prize.basis) + '</dd></div>' : '') + '</dl>' +
      winnerProfileLink(w) + '</div>';
    fitWinnerPreview();
    [].forEach.call(root.querySelectorAll('[data-w]'), function (b) { b.setAttribute('aria-pressed', String(+b.dataset.w === index)); });
  }
  function drawTl() {
    var eds = A.editions.filter(function (e) { return e.winners.some(function (w) { return !st.branch || w.branch === st.branch; }); });
    $('[data-aw-tl]').innerHTML = eds.map(function (e) {
      var ws = e.winners.filter(function (w) { return !st.branch || w.branch === st.branch; });
      return '<li class="aw-ed' + (e.type === 'arab' ? ' is-arab' : '') + '"><div class="aw-knot"><b>' + e.year + '</b></div>' +
        '<div class="aw-ed-h"><strong>الدورة ' + ORD[e.n] + '</strong><span class="aw-type">' + (e.type === 'arab' ? 'دورة عربية · تنافسٌ مع العرب' : 'دورة عُمانية') + '</span>' +
        (e.note ? '<small>' + esc(e.note) + '</small>' : '') + '</div>' +
        '<ul class="aw-ms">' + ws.map(medal).join('') + '</ul></li>';
    }).join('');
    previewWinner(all.filter(function (w) { return !st.branch || w.branch === st.branch; })[0]);
    $('[data-aw-count]').textContent = ar(eds.reduce(function (n, e) { return n + e.winners.filter(function (w) { return !st.branch || w.branch === st.branch; }).length; }, 0));
    [].forEach.call(root.querySelectorAll('[data-aw-f]'), function (b) { b.setAttribute('aria-pressed', String(b.dataset.awF === st.branch)); });
  }
  $('[data-aw-filters]').innerHTML = [['', 'الكل']].concat(['culture', 'arts', 'lit'].map(function (k) { return [k, B[k].name]; })).map(function (f) {
    return '<button type="button" class="' + (f[0] ? 'b-' + f[0] : '') + '" data-aw-f="' + f[0] + '" aria-pressed="false">' + (f[0] ? svg(f[0]) : '') + f[1] + '</button>';
  }).join('');
  $('[data-aw-filters]').addEventListener('click', function (e) { var b = e.target.closest('[data-aw-f]'); if (b) { st.branch = b.dataset.awF; drawTl(); } });
  root.addEventListener('click', function (e) {
    var b = e.target.closest('[data-jump-branch]'); if (!b) return;
    st.branch = b.dataset.jumpBranch; drawTl();
    $('#aw-winners').scrollIntoView({ behavior: 'smooth', block: 'start' });
  });
  drawTl();
  addEventListener('resize', fitWinnerPreview);
  if (document.fonts && document.fonts.ready) document.fonts.ready.then(fitWinnerPreview);

  // ---------- بطاقة الفائز (تنزلق من الأسفل) ----------
  var sheet = $('[data-aw-sheet]'), card = sheet.querySelector('.aw-card'), lastBtn = null;
  function openW(w, btn) {
    if (matchMedia('(min-width: 1100px)').matches) { previewWinner(w); return; }
    var p = A.prizes[w.ed.type];
    lastBtn = btn;
    card.className = 'aw-card b-' + w.branch;
    card.innerHTML = '<button type="button" class="aw-x" data-aw-close aria-label="إغلاق"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 6l12 12M18 6 6 18"/></svg></button>' +
      '<span class="aw-card-rib" aria-hidden="true"></span>' +
      '<span class="aw-disc big"><img src="' + photo(w) + '" alt=""' + (w.group ? ' class="is-group"' : '') + '></span>' +
      '<h3 id="aw-card-h">' + esc(w.name) + '</h3>' +
      '<p class="aw-card-k">' + svg(w.branch) + 'فرع ' + B[w.branch].name + ' · ' + esc(w.field) + '</p>' +
      (w.why ? '<div class="aw-why"><span>نظير ماذا؟</span><p>' + esc(w.why) + '</p></div>' : '') +
      '<dl>' + (w.work ? '<div><dt>العمل الفائز</dt><dd>' + esc(w.work) + '</dd></div>' : '') +
      '<div><dt>الدورة</dt><dd>' + ORD[w.ed.n] + ' · ' + w.ed.year + ' (' + p.label + ')</dd></div>' +
      '<div><dt>التكريم</dt><dd>' + esc(p.medal) + '</dd></div>' +
      (w.ed.type === 'arab' ? '<div><dt>لماذا هو لافت؟</dt><dd>فاز في دورة مفتوحة للعرب جميعًا، ' + esc(p.basis) + '</dd></div>' : '') + '</dl>' +
      winnerProfileLink(w);
    sheet.hidden = false;
    requestAnimationFrame(function () { sheet.classList.add('on'); });
    document.documentElement.style.overflow = 'hidden';
    card.querySelector('[data-aw-close]').focus();
  }
  function closeW() {
    sheet.classList.remove('on');
    document.documentElement.style.overflow = '';
    setTimeout(function () { sheet.hidden = true; }, 320);
    if (lastBtn) lastBtn.focus();
  }
  root.addEventListener('click', function (e) { var b = e.target.closest('[data-w]'); if (b) openW(all[+b.dataset.w], b); });
  sheet.addEventListener('click', function (e) { if (e.target === sheet || e.target.closest('[data-aw-close]')) closeW(); });
  addEventListener('keydown', function (e) { if (e.key === 'Escape' && !sheet.hidden) closeW(); });

  // ---------- كيف تُمنح: شريط التناوب والوسامان ----------
  var years = [];
  A.editions.slice().reverse().forEach(function (e) { years.push({ y: e.year, t: e.type, n: e.n }); if (e.year === 2019) years.push({ y: '20–21', t: 'pause' }); });
  years.push({ y: A.current.year, t: A.current.type, n: A.current.n, now: true });
  $('[data-aw-alt]').innerHTML = years.map(function (x) {
    return '<li class="t-' + x.t + (x.now ? ' is-now' : '') + '"><b>' + x.y + '</b><small>' + (x.t === 'pause' ? 'توقّف' : x.t === 'arab' ? 'عربية' : 'عُمانية') + '</small></li>';
  }).join('');
  $('[data-aw-prizes]').innerHTML = ['oman', 'arab'].map(function (k) {
    var p = A.prizes[k];
    return '<div class="aw-prize p-' + k + '"><span class="aw-seal" aria-hidden="true"><i></i></span><b>' + p.label + '</b>' +
      '<p>' + esc(p.medal) + '</p><em>' + esc(p.basis) + '</em></div>';
  }).join('');

  // ---------- الدورة الجارية ----------
  var c = A.current;
  $('[data-aw-now]').innerHTML = '<div class="aw-now-h"><span class="aw-live"><i></i>الدورة الجارية</span><h3>الدورة ' + ORD[c.n] + ' · ' + c.year + '</h3><small>دورة عُمانية</small></div>' +
    '<ul>' + c.fields.map(function (f) { return '<li class="b-' + f[0] + '"><span class="aw-pil-ic">' + svg(f[0]) + '</span><small>فرع ' + B[f[0]].name + '</small><b>' + esc(f[1]) + '</b></li>'; }).join('') + '</ul>' +
    '<p class="aw-now-s">' + esc(c.status) + '</p>';

  // ---------- المصادر ----------
  $('[data-aw-src]').innerHTML = A.sources.map(function (s) { return '<li><a href="' + esc(s[1]) + '" target="_blank" rel="noopener">' + esc(s[0]) + '</a></li>'; }).join('');
})();
