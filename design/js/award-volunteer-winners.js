/* جائزة العمل التطوعي — «الفائزون» على الكمبيوتر بعرض واضح بلا حاجة للضغط لمعرفة التفاصيل:
   تبويب لكل دورة، ثم عمودان (الأفراد / الجمعيات)، وكل فائز في بطاقة ظاهرة: المركز، الصورة، الاسم، المشروع، ونظير ماذا فاز.
   زر «التفاصيل والصور» يفتح البطاقة الكاملة الموجودة أصلًا (data-vw بنفس ترتيب js/award-volunteer.js). */
(function () {
  var V = window.INJAZ_VOL, root = document.querySelector('[data-vol]'), eds = root && root.querySelector('[data-v-eds]');
  if (!V || !eds) return;
  var ar = function (n) { return String(n).replace(/\d/g, function (d) { return '٠١٢٣٤٥٦٧٨٩'[d]; }); };
  var esc = function (s) { var d = document.createElement('div'); d.textContent = s == null ? '' : s; return d.innerHTML; };
  var ORD = ['', 'الأولى', 'الثانية', 'الثالثة', 'الرابعة', 'الخامسة', 'السادسة', 'السابعة'];
  var RANK = ['', 'المركز الأول', 'المركز الثاني', 'المركز الثالث'];
  var CAT = { ind: 'الأفراد', org: 'الجمعيات والمؤسسات الأهلية' };
  var ICON = { ind: '<circle cx="12" cy="7.5" r="3.5"/><path d="M5 20c.8-4 3.6-6 7-6s6.2 2 7 6"/>', org: '<path d="M4 20V9l8-5 8 5v11"/><path d="M9 20v-6h6v6"/><path d="M4 20h16"/>' };
  var svg = function (k) { return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' + ICON[k] + '</svg>'; };
  var all = []; V.editions.forEach(function (e) { e.winners.forEach(function (w) { if (!w.withheld) all.push(w); }); });
  var list = V.editions.filter(function (e) { return e.winners.length; });
  if (!list.length) return;

  var pic = function (w, k) {
    var src = w.photo || w.logo;
    return '<span class="lw-pic' + (w.photo ? '' : src ? ' is-logo' : ' is-icon') + '">' + (src ? '<img src="' + esc(src) + '" alt="" loading="lazy"' + (w.photo && w.pos ? ' style="object-position:' + esc(w.pos) + '"' : '') + '>' : svg(k)) + '</span>';
  };
  var row = function (w, k, r, partial) {
    if (!w) return partial ? '<li class="lw-w r' + r + ' is-off"><span class="lw-rank">' + ar(r) + '</span><span class="lw-txt"><b>' + RANK[r] + ' — قيد التوثيق</b><small>لم نجد مصدرًا منشورًا لهذا المركز بعد.</small></span></li>' : '';
    if (w.withheld) return '<li class="lw-w r' + r + ' is-off"><span class="lw-rank">' + ar(r) + '</span><span class="lw-txt"><b>' + RANK[r] + ' — حُجب</b><small>لم تمنح اللجنة هذا المركز في هذه الدورة.</small></span></li>';
    return '<li class="lw-w r' + r + '"><span class="lw-rank" title="' + RANK[r] + '">' + ar(r) + '</span>' + pic(w, k) +
      '<span class="lw-txt"><small class="lw-rk">' + RANK[r] + (w.place ? ' · ' + esc(w.place) : '') + '</small><b>' + esc(w.name) + '</b>' +
      '<span class="lw-proj">مشروع «' + esc(w.project) + '»</span>' +
      (w.why ? '<span class="lw-why"><em>نظير ماذا؟</em> ' + esc(w.why) + '</span>' : '<span class="lw-why is-none">لم نجد وصفًا منشورًا لهذا المشروع بعد.</span>') +
      '<button type="button" class="lw-more" data-vw="' + all.indexOf(w) + '">التفاصيل والصور <span aria-hidden="true">←</span></button></span></li>';
  };
  var col = function (e, k) {
    var ws = e.winners.filter(function (w) { return w.cat === k; });
    var get = function (r) { return ws.filter(function (w) { return w.rank === r; })[0]; };
    if (!ws.length && e.partial) return '<div class="lw-col"><h4>' + svg(k) + CAT[k] + '</h4><p class="lw-none">لم نجد أسماء فائزي هذه الفئة في مصدر منشور بعد.</p></div>';
    return '<div class="lw-col lw-' + k + '"><h4>' + svg(k) + 'فئة ' + CAT[k] + '</h4><ol>' + [1, 2, 3].map(function (r) { return row(get(r), k, r, e.partial); }).join('') + '</ol></div>';
  };
  var html = '<div class="lw" data-lw>' +
    '<p class="lw-hint"><span aria-hidden="true">✦</span> اختر الدورة لعرض فائزيها</p>' +
    '<div class="lw-tabs" role="tablist" aria-label="دورات الجائزة">' + list.map(function (e, i) {
      return '<button type="button" role="tab" id="lw-t' + i + '" aria-controls="lw-p' + i + '" aria-selected="' + (i ? 'false' : 'true') + '"' + (i ? ' tabindex="-1"' : '') + '><b>' + e.year + '</b><span>الدورة ' + ORD[e.n] + '</span><small>' + ar(e.entries) + ' مشروعًا</small></button>';
    }).join('') + '</div>' +
    list.map(function (e, i) {
      return '<section class="lw-p" role="tabpanel" id="lw-p' + i + '" aria-labelledby="lw-t' + i + '"' + (i ? ' hidden' : '') + '>' +
        '<header class="lw-ph">' + (e.cover ? '<figure><img src="' + esc(e.cover[0]) + '" alt="' + esc(e.cover[1]) + '" loading="lazy"><figcaption>الصورة: ' + esc(e.cover[2]) + '</figcaption></figure>' : '') +
        '<div><span class="lw-kick">الدورة ' + ORD[e.n] + ' · ٥ ديسمبر ' + ar(e.year) + '</span><h3>' + ar(e.entries) + ' مشروعًا تطوعيًا تنافست، وهؤلاء الفائزون</h3>' +
        (e.note ? '<p>' + esc(e.note) + '</p>' : '') + (e.partial ? '<p class="lw-partial">وثّقنا من هذه الدورة ما نشرته الجهات الفائزة نفسها، وبقية المراكز قيد البحث.</p>' : '') + '</div></header>' +
        '<div class="lw-cols">' + col(e, 'ind') + col(e, 'org') + '</div></section>';
    }).join('') + '</div>';
  eds.insertAdjacentHTML('afterend', html);
  var box = root.querySelector('[data-lw]'), tabs = box.querySelectorAll('[role="tab"]'), panels = box.querySelectorAll('[role="tabpanel"]');
  var pick = function (k, focus) {
    [].forEach.call(tabs, function (t, i) { t.setAttribute('aria-selected', i === k ? 'true' : 'false'); t.tabIndex = i === k ? 0 : -1; });
    [].forEach.call(panels, function (p, i) { p.hidden = i !== k; });
    if (focus) tabs[k].focus();
  };
  [].forEach.call(tabs, function (t, i) {
    t.addEventListener('click', function () { pick(i); });
    t.addEventListener('keydown', function (ev) { var d = ev.key === 'ArrowLeft' ? 1 : ev.key === 'ArrowRight' ? -1 : 0; if (d) { ev.preventDefault(); pick((i + d + tabs.length) % tabs.length, true); } });
  });
})();
