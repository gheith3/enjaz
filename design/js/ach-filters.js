/* صفحة الإنجازات: زر «فلترة» يفتح مربعًا صغيرًا فيه الجنس، السنة (من – إلى)، المستوى، والترتيب.
   النتائج تتغير فورًا. الاختيارات المفعّلة تظهر رقائق تحت السطر عليها ✕، وعددها على الزر.
   التصفية نفسها في main.js (window.injazFilter). */
(function () {
  var btn = document.querySelector('[data-flt-btn]');
  var box = document.querySelector('[data-flt-box]');
  var chipsEl = document.querySelector('[data-flt-chips]');
  var badge = document.querySelector('[data-flt-n]');
  var results = document.querySelector('[data-results]');
  var F = window.injazFilter;
  if (!btn || !box || !results || !F || !F.set) return;

  var ar = function (n) { return String(n).replace(/\d/g, function (d) { return '٠١٢٣٤٥٦٧٨٩'[d]; }); };
  var GENDERS = [['', 'الكل'], ['ذكر', 'ذكر'], ['أنثى', 'أنثى']];
  var SCOPES = [['', 'الكل'], ['local', 'محلي'], ['gulf', 'خليجي'], ['arab', 'عربي'], ['international', 'دولي']];
  var SORTS = [['new', 'الأحدث'], ['old', 'الأقدم']];
  var label = function (list, v) { return (list.filter(function (x) { return x[0] === v; })[0] || ['', ''])[1]; };
  var years = [];
  [].forEach.call(results.children, function (el) { var y = (el.dataset.date || '').slice(0, 4); if (y && years.indexOf(y) < 0) years.push(y); });
  years.sort().reverse();

  var st = { gender: '', from: '', to: '', scope: '', sort: 'new' };

  // ---------- المربع ----------
  var seg = function (key, list) {
    return '<div class="flt-seg" role="group">' + list.map(function (o) {
      return '<button type="button" data-k="' + key + '" data-v="' + o[0] + '" aria-pressed="' + (st[key] === o[0]) + '">' + o[1] + '</button>';
    }).join('') + '</div>';
  };
  var yearSel = function (key, first) {
    return '<select data-y="' + key + '" aria-label="' + first + '"><option value="">' + first + '</option>' +
      years.map(function (y) { return '<option value="' + y + '"' + (st[key] === y ? ' selected' : '') + '>' + ar(y) + '</option>'; }).join('') + '</select>';
  };
  // المجموعات الظاهرة في المربع: data-groups="gender year scope sort" (صفحة المنجزين: الجنس فقط)
  var groups = (box.dataset.groups || 'gender year scope sort').split(/\s+/);
  var has = function (g) { return groups.indexOf(g) > -1; };
  // data-no-all: الجنس «ذكر / أنثى» فقط بلا «الكل»؛ الضغط على المختار مرة ثانية يلغيه
  var noAll = box.hasAttribute('data-no-all');
  function drawBox() {
    box.innerHTML =
      (has('gender') ? '<div class="flt-row"><span class="flt-l">الجنس</span>' + seg('gender', noAll ? GENDERS.slice(1) : GENDERS) + '</div>' : '') +
      (has('year') && years.length ? '<div class="flt-row"><span class="flt-l">السنة</span><div class="flt-years">' + yearSel('from', 'من') + '<span>–</span>' + yearSel('to', 'إلى') + '</div></div>' : '') +
      (has('scope') ? '<div class="flt-row"><span class="flt-l">المستوى</span>' + seg('scope', SCOPES) + '</div>' : '') +
      (has('sort') ? '<div class="flt-row"><span class="flt-l">الترتيب</span>' + seg('sort', SORTS) + '</div>' : '') +
      '<div class="flt-foot"><button type="button" class="flt-clear" data-clear>مسح الكل</button><button type="button" class="flt-done" data-done>تم</button></div>';
  }

  // ---------- تطبيق ----------
  function apply(sortChanged) {
    if (st.from && st.to && st.from > st.to) { var t = st.from; st.from = st.to; st.to = t; }
    F.set({ gender: st.gender, from: st.from, to: st.to, scope: st.scope });
    if (sortChanged && F.sort) F.sort(st.sort);
    paint();
    document.dispatchEvent(new CustomEvent('injaz:filters')); // يحدّث أعداد الاتحادات
  }
  function paint() {
    var active = [];
    if (st.gender) active.push(['gender', st.gender]);
    if (st.from || st.to) active.push(['years', st.from && st.to ? (st.from === st.to ? ar(st.from) : ar(st.from) + ' – ' + ar(st.to)) : st.from ? 'من ' + ar(st.from) : 'حتى ' + ar(st.to)]);
    if (st.scope) active.push(['scope', label(SCOPES, st.scope)]);
    if (st.sort === 'old') active.push(['sort', 'الأقدم أولًا']);
    badge.hidden = !active.length; badge.textContent = ar(active.length);
    btn.classList.toggle('on', active.length > 0);
    if (chipsEl) {
      chipsEl.hidden = !active.length;
      chipsEl.innerHTML = active.map(function (a) {
        return '<button type="button" class="flt-chip" data-rm="' + a[0] + '" aria-label="إلغاء ' + a[1] + '">' + a[1] + '<span aria-hidden="true">✕</span></button>';
      }).join('');
    }
    if (!box.hidden) {
      [].forEach.call(box.querySelectorAll('[data-k]'), function (b) { b.setAttribute('aria-pressed', String(st[b.dataset.k] === b.dataset.v)); });
      [].forEach.call(box.querySelectorAll('[data-y]'), function (s) { s.value = st[s.dataset.y]; });
    }
  }
  function reset() { var sortChanged = st.sort !== 'new'; st = { gender: '', from: '', to: '', scope: '', sort: 'new' }; apply(sortChanged); }

  // ---------- فتح وإغلاق ----------
  function open() { drawBox(); box.hidden = false; btn.setAttribute('aria-expanded', 'true'); var f = box.querySelector('button'); if (f) f.focus({ preventScroll: true }); }
  function close(focusBtn) { box.hidden = true; btn.setAttribute('aria-expanded', 'false'); if (focusBtn) btn.focus(); }
  btn.addEventListener('click', function () { if (box.hidden) open(); else close(); });
  document.addEventListener('click', function (e) { if (!box.hidden && !box.contains(e.target) && !btn.contains(e.target)) close(); });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !box.hidden) close(true); });

  box.addEventListener('click', function (e) {
    var b = e.target.closest('button'); if (!b) return;
    if (b.dataset.k) {
      var sortChanged = b.dataset.k === 'sort' && st.sort !== b.dataset.v;
      st[b.dataset.k] = (noAll && b.dataset.k === 'gender' && st.gender === b.dataset.v) ? '' : b.dataset.v;
      apply(sortChanged);
    }
    else if (b.hasAttribute('data-clear')) reset();
    else if (b.hasAttribute('data-done')) close(true);
  });
  box.addEventListener('change', function (e) { var s = e.target.closest('[data-y]'); if (s) { st[s.dataset.y] = s.value; apply(false); } });
  if (chipsEl) chipsEl.addEventListener('click', function (e) {
    var b = e.target.closest('[data-rm]'); if (!b) return;
    var k = b.dataset.rm, sortChanged = false;
    if (k === 'years') { st.from = ''; st.to = ''; } else if (k === 'sort') { st.sort = 'new'; sortChanged = true; } else st[k] = '';
    apply(sortChanged);
  });
  // زر «مسح» العام في الصفحة (main.js) يعيد كل شيء
  document.addEventListener('injaz:filters-reset', function () { if (st.sort !== 'new' && F.sort) F.sort('new'); st = { gender: '', from: '', to: '', scope: '', sort: 'new' }; paint(); });
  paint();
})();
