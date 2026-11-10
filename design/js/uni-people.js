/* صفحة «منجزو الجامعة»: uni-people.html?org=squ — بطاقات مثل صفحة المنجزين، مع تصفية بالصفة (أكاديميون/طلبة وخريجون) والجنس.
   المنجزون من بطاقات صفحة المنجزين التي تحمل data-affil بهذه الجامعة (js/org-links.js). */
(function () {
  var root = document.querySelector('[data-up-page]');
  var list = window.INJAZ_UNIS || [];
  if (!root || !list.length || !window.InjazLinks) return;
  var q = new URLSearchParams(location.search), slug = q.get('org') || 'squ';
  var u = list.filter(function (x) { return x.slug === slug; })[0];
  var $ = function (s) { return root.querySelector(s); };
  var ar = function (n) { return String(n).replace(/\d/g, function (d) { return '٠١٢٣٤٥٦٧٨٩'[d]; }); };
  var esc = function (s) { var d = document.createElement('div'); d.textContent = s == null ? '' : s; return d.innerHTML; };
  if (!u) return;
  document.title = 'منجزو ' + u.name + ' | إنجازات شباب عمان';
  // اسم الجامعة أو الكلية تحت «الجامعات والكليات» في الشريط العلوي (نفس شكل ملف المنجز)
  var crumbSub = function (name) {
    var c = document.querySelector('.crumb'); if (!c || !name) return;
    c.classList.add('has-sub');
    c.innerHTML = '<span class="crumb-title">الجامعات والكليات</span><small class="crumb-sub">' + esc(name) + '</small>';
  };
  crumbSub(u.name);
  $('[data-up-hero]').innerHTML = '<div class="uh-top"><div class="uh-txt"><span class="uh-kick">منجزون منتسبون</span><h1>منجزو ' + esc(u.name) + '</h1>' +
    '<p class="uh-about">أعضاء هيئة تدريس وطلبة وخريجون حقّقوا إنجازات</p></div>' +
    '<a class="uh-emb' + ((u.r || 1) < 1.6 ? '' : ' is-plate') + '" href="university.html?org=' + encodeURIComponent(slug) + '">' + (u.logo ? '<img src="' + esc(u.logo) + '" alt="' + esc(u.name) + '">' : '') + '</a></div>';
  var st = { kind: '', gender: '' }, people = [];
  var KINDS = [['', 'الكل'], ['staff', 'أكاديميون'], ['student', 'طلبة وخريجون']];
  var grid = $('[data-up-grid]'), chips = $('[data-up-chips]'), count = $('[data-up-count]');
  var ok = function (p, k) { var kind = p.affil.kind === 'staff' ? 'staff' : 'student'; return (!k || kind === k) && (!st.gender || p.gender === st.gender); };
  function draw() {
    chips.innerHTML = KINDS.map(function (k) { return '<button type="button" class="chip' + (st.kind === k[0] ? ' active' : '') + '" aria-pressed="' + (st.kind === k[0]) + '" data-k="' + k[0] + '"><span>' + k[1] + '</span></button>'; }).join('') +
      ['ذكر', 'أنثى'].map(function (g) { return '<button type="button" class="chip up-g' + (st.gender === g ? ' active' : '') + '" aria-pressed="' + (st.gender === g) + '" data-g="' + g + '"><span>' + g + '</span></button>'; }).join('');
    var items = people.filter(function (p) { return ok(p, st.kind); });
    count.textContent = ar(items.length) + (items.length > 2 && items.length <= 10 ? ' منجزين' : ' منجزًا');
    grid.innerHTML = items.length ? items.map(function (p) {
      return '<article class="ach person-card"><a class="ach-link" href="' + esc(p.href || '#') + '" aria-label="' + esc(p.name) + '"></a>' +
        '<img class="ach-img" src="' + esc(p.img) + '" alt="' + esc(p.name) + '" width="236" height="236" loading="lazy" style="' + esc(p.style) + '">' +
        '<div class="ach-body"><p class="ach-cat">' + esc(p.kindLabel) + '</p><h3>' + esc(p.name) + '</h3><p class="ach-headline">' + esc(p.affil.role || p.headline) + '</p>' +
        '<p class="ach-meta">' + esc(p.meta) + '</p></div></article>';
    }).join('') : '<p class="u-soon">لا يوجد منجزون مطابقون.</p>';
  }
  chips.addEventListener('click', function (e) {
    var b = e.target.closest('button'); if (!b) return;
    if (b.dataset.g) st.gender = st.gender === b.dataset.g ? '' : b.dataset.g; else st.kind = b.dataset.k;
    draw();
  });
  $('[data-up-back]').href = 'university.html?org=' + encodeURIComponent(slug);
  $('[data-up-back]').textContent = '→ ملف ' + u.name;
  InjazLinks.forOrg(slug).then(function (d) {
    people = d.people.slice();
    draw();
  }).catch(function () { grid.innerHTML = '<p class="u-soon">تظهر القائمة عند فتح الموقع من الخادم (Live Server).</p>'; });
})();
