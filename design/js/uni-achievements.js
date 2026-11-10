/* صفحة «إنجازات الجامعة»: uni-achievements.html?org=squ&type=student&gender=أنثى
   كل إنجاز وُسم بالجامعة في لوحة التحكم (data-orgs) أو لأحد منتسبيها يظهر هنا، مصنّفًا:
   المؤسسة / الطلبة / الأكاديميون (js/org-links.js)، مع فلترة ذكر / أنثى. الروابط قابلة للمشاركة. */
(function () {
  var root = document.querySelector('[data-ua-page]');
  var list = window.INJAZ_UNIS || [];
  if (!root || !list.length || !window.InjazLinks) return;
  var q = new URLSearchParams(location.search);
  var slug = q.get('org') || 'squ';
  var u = list.filter(function (x) { return x.slug === slug; })[0];
  var $ = function (s) { return root.querySelector(s); };
  var ar = function (n) { return String(n).replace(/\d/g, function (d) { return '٠١٢٣٤٥٦٧٨٩'[d]; }); };
  var esc = function (s) { var d = document.createElement('div'); d.textContent = s == null ? '' : s; return d.innerHTML; };
  var pl = function (n) { return n === 0 ? 'لا إنجازات' : n === 1 ? 'إنجاز واحد' : n === 2 ? 'إنجازان' : ar(n) + (n <= 10 ? ' إنجازات' : ' إنجازًا'); };
  if (!u) { $('[data-ua-hero]').innerHTML = '<h1>المؤسسة غير موجودة</h1>'; return; }
  var kind = u.group === 'college' ? 'الكلية' : 'الجامعة';
  document.title = 'إنجازات ' + u.name + ' | إنجازات شباب عمان';
  // اسم الجامعة أو الكلية تحت «الجامعات والكليات» في الشريط العلوي (نفس شكل ملف المنجز)
  var crumbSub = function (name) {
    var c = document.querySelector('.crumb'); if (!c || !name) return;
    c.classList.add('has-sub');
    c.innerHTML = '<span class="crumb-title">الجامعات والكليات</span><small class="crumb-sub">' + esc(name) + '</small>';
  };
  crumbSub(u.name);

  var ICON = {
    all: '<path d="M4 5h7v6H4zM13 5h7v6h-7zM4 13h7v6H4zM13 13h7v6h-7z"/>',
    student: '<path d="M2 9l10-5 10 5-10 5z"/><path d="M6 11v5c3 2.5 9 2.5 12 0v-5"/>',
    staff: '<path d="M4 19V6a2 2 0 0 1 2-2h12v15H6a2 2 0 0 0-2 2"/><path d="M8 8h6M8 11h4"/>',
    org: '<path d="M3 20h18M5 20V10l7-5 7 5v10"/><path d="M9 20v-5h6v5"/>'
  };
  var svg = function (k) { return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' + ICON[k] + '</svg>'; };
  var TYPES = [['', 'الكل', 'all'], ['student', 'طلبة', 'student'], ['staff', 'أكاديميون', 'staff'], ['org', 'المؤسسة', 'org']];
  var st = { type: q.get('type') || '', gender: q.get('gender') || '' }, limit = 12, all = [];

  // ---------- الرأس ----------
  var cover = u.cover || '';
  $('[data-ua-hero]').innerHTML = (cover ? '<img class="uah-bg" src="' + esc(cover) + '" alt="">' : '') +
    '<span class="uah-in"><a class="uah-logo" href="university.html?org=' + encodeURIComponent(slug) + '">' + (u.logo ? '<img src="' + esc(u.logo) + '" alt="' + esc(u.name) + '">' : '') + '</a>' +
    '<span class="uah-k">أخبار وإنجازات</span><h1>إنجازات ' + esc(u.name) + '</h1>' +
    '<span class="uah-stats" data-ua-stats></span></span>';

  // ---------- الفلاتر ----------
  var types = $('[data-ua-types]'), genders = $('[data-ua-gender]'), grid = $('[data-ua-grid]'), more = $('[data-ua-more]'), count = $('[data-ua-count]');
  var matches = function (a, ignoreType) { return (ignoreType || !st.type || a.type === st.type) && (!st.gender || a.gender === st.gender); };
  function drawFilters() {
    types.innerHTML = TYPES.map(function (t) {
      var n = all.filter(function (a) { return (!t[0] || a.type === t[0]) && matches(a, true); }).length;
      return '<button type="button" data-type="' + t[0] + '" aria-pressed="' + (st.type === t[0]) + '"><span class="uat-ic">' + svg(t[2]) + '</span><b>' + t[1] + '</b><small>' + ar(n) + '</small></button>';
    }).join('');
    genders.innerHTML = '<span>فلترة</span>' + [['ذكر', 'ذكر'], ['أنثى', 'أنثى']].map(function (g) {
      return '<button type="button" data-g="' + g[0] + '" aria-pressed="' + (st.gender === g[0]) + '">' + g[1] + '</button>';
    }).join('');
  }
  function card(a) {
    return '<a class="uac" href="' + esc(a.href || '#') + '"><span class="uac-img"><img src="' + esc(a.img) + '" alt="" loading="lazy" style="' + esc(a.style) + '">' +
      '<span class="un-k un-' + a.type + '">' + esc(a.typeLabel) + '</span></span>' +
      '<span class="uac-b"><b>' + esc(a.title) + '</b><small>' + esc(a.who) + (a.when ? ' · ' + esc(a.when) : '') + '</small>' + (a.desc ? '<em>' + esc(a.desc) + '</em>' : '') + '</span></a>';
  }
  function draw() {
    drawFilters();
    var items = all.filter(function (a) { return matches(a); });
    count.textContent = pl(items.length);
    grid.innerHTML = items.length ? items.slice(0, limit).map(card).join('')
      : '<p class="u-soon">لا توجد إنجازات مطابقة' + (st.type === 'student' ? ' للطلبة بعد' : '') + '. جرّب فلترًا آخر.</p>';
    more.hidden = items.length <= limit;
    more.textContent = 'عرض المزيد (' + ar(Math.min(12, items.length - limit)) + ')';
    try {
      var p = new URLSearchParams(location.search);
      st.type ? p.set('type', st.type) : p.delete('type'); st.gender ? p.set('gender', st.gender) : p.delete('gender');
      history.replaceState(null, '', location.pathname + '?' + p.toString());
    } catch (e) {}
  }
  types.addEventListener('click', function (e) { var b = e.target.closest('[data-type]'); if (b) { st.type = b.dataset.type; limit = 12; draw(); } });
  genders.addEventListener('click', function (e) { var b = e.target.closest('[data-g]'); if (b) { st.gender = st.gender === b.dataset.g ? '' : b.dataset.g; limit = 12; draw(); } });
  more.addEventListener('click', function () { limit += 12; draw(); });
  $('[data-ua-back]').href = 'university.html?org=' + encodeURIComponent(slug);
  $('[data-ua-back]').textContent = '→ ملف ' + u.name;

  InjazLinks.achievementsFor(slug).then(function (l) {
    all = l;
    var n = function (t) { return l.filter(function (a) { return a.type === t; }).length; };
    $('[data-ua-stats]').innerHTML = [['org', 'المؤسسة'], ['student', 'الطلبة'], ['staff', 'الأكاديميون']].map(function (t) {
      return '<span><b>' + ar(n(t[0])) + '</b>' + t[1] + '</span>';
    }).join('');
    draw();
  }).catch(function () { grid.innerHTML = '<p class="u-soon">تظهر الإنجازات عند فتح الموقع من الخادم (Live Server).</p>'; });
})();
