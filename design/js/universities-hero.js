/* هيدر «الجامعات والكليات»: موشن جرافيك عملي.
   - خريطة عُمان المعتمدة تُرسم بخط ذهبي، ثم تظهر مدن المؤسسات وتنطلق منها خطوط ضوء من مسقط.
   - بطاقة متنقلة تعرّف بمدينة كل بضع ثوانٍ (عدد مؤسساتها)، والضغط عليها أو على النقطة يبحث عن مؤسسات المدينة.
   - أعداد تتصاعد، والضغط عليها يصفّي القائمة (جامعات/كليات/التقنية).
   البيانات: js/universities-data.js، والخريطة: js/oman-outline.js */
(function () {
  var hero = document.querySelector('[data-uh]');
  var list = window.INJAZ_UNIS || [], O = window.OMAN_OUTLINE;
  if (!hero || !list.length) return;
  var reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  var ar = function (n) { return String(n).replace(/\d/g, function (d) { return '٠١٢٣٤٥٦٧٨٩'[d]; }); };
  var esc = function (s) { var d = document.createElement('div'); d.textContent = s == null ? '' : s; return d.innerHTML; };
  var by = function (g) { return list.filter(function (u) { return u.group === g; }); };
  var unis = by('uni'), cols = by('college'), tech = by('tech');
  var branches = tech[0] && tech[0].branches || [];
  var chipsBar = document.querySelector('[data-u-chips]'), q = document.querySelector('[data-u-q]'), tools = document.querySelector('.ul-tools');

  function goList(g, city) {
    if (chipsBar) { var b = chipsBar.querySelector('[data-g="' + (g || '') + '"]'); if (b) b.click(); }
    if (q) { q.value = city || ''; q.dispatchEvent(new Event('input', { bubbles: true })); }
    if (tools) tools.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'start' });
  }

  // ---------- الأعداد ----------
  var stats = [['uni', unis.length, 'جامعات'], ['college', cols.length, 'كلية'], ['tech', branches.length, 'فروع للتقنية']];
  var statsEl = hero.querySelector('[data-uh-stats]');
  statsEl.innerHTML = stats.filter(function (s) { return s[1]; }).map(function (s) {
    return '<button type="button" data-g="' + s[0] + '" aria-label="اعرض ' + esc(s[2]) + ' (' + ar(s[1]) + ')"><b data-to="' + s[1] + '">' + ar(reduce ? s[1] : 0) + '</b><span>' + esc(s[2]) + '</span></button>';
  }).join('');
  statsEl.addEventListener('click', function (e) { var b = e.target.closest('[data-g]'); if (b) goList(b.dataset.g); });
  if (!reduce) {
    var t0 = 0, nums = [].slice.call(statsEl.querySelectorAll('[data-to]'));
    var tick = function (t) {
      if (!t0) t0 = t;
      var k = Math.min(1, (t - t0 - 500) / 1300); if (k < 0) k = 0;
      var e = 1 - Math.pow(1 - k, 3);
      nums.forEach(function (n) { n.textContent = ar(Math.round(+n.dataset.to * e)); });
      if (k < 1) requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
  }

  // ---------- المدن ----------
  var C = { 'مسقط': [58.38, 23.59], 'بركاء': [57.89, 23.68], 'المصنعة': [57.63, 23.75], 'الرستاق': [57.42, 23.39], 'صحار': [56.74, 24.36],
    'شناص': [56.47, 24.74], 'البريمي': [55.79, 24.25], 'عبري': [56.52, 23.23], 'نزوى': [57.53, 22.93], 'إبراء': [58.53, 22.69],
    'صور': [59.53, 22.57], 'صلالة': [54.09, 17.02] };
  var count = {};
  list.forEach(function (u) {
    if (u.group === 'tech') return;
    Object.keys(C).forEach(function (c) { if ((u.city || '').indexOf(c) === 0) count[c] = (count[c] || 0) + 1; });
  });
  branches.forEach(function (c) { if (C[c]) count[c] = (count[c] || 0) + 1; });
  var cities = Object.keys(C).filter(function (c) { return count[c]; });
  var plural = function (n) { return n === 1 ? 'مؤسسة واحدة' : n === 2 ? 'مؤسستان' : ar(n) + (n <= 10 ? ' مؤسسات' : ' مؤسسة'); };

  var mapEl = hero.querySelector('[data-uh-map]'), tag = hero.querySelector('[data-uh-tag]');
  if (O && mapEl) {
    var hub = O.pt.apply(null, C['مسقط']);
    var svg = '<svg class="uh-svg" viewBox="' + O.vb + '" role="img" aria-label="خريطة سلطنة عُمان ومدن مؤسسات التعليم العالي">' +
      '<defs><linearGradient id="uhGold" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#FFF1CC"/><stop offset=".5" stop-color="#E6C98A"/><stop offset="1" stop-color="#B98E48"/></linearGradient>' +
      '<radialGradient id="uhDot"><stop offset="0" stop-color="#FFF6DD"/><stop offset="1" stop-color="#E6C98A"/></radialGradient></defs>' +
      '<path class="uh-land" d="' + O.d + '"/>' +
      '<path class="uh-coast" d="' + O.d + '" pathLength="1"/>';
    // خطوط الضوء من مسقط
    cities.forEach(function (c, i) {
      if (c === 'مسقط') return;
      var p = O.pt.apply(null, C[c]), mx = (hub[0] + p[0]) / 2, my = (hub[1] + p[1]) / 2;
      var dx = p[0] - hub[0], dy = p[1] - hub[1], len = Math.sqrt(dx * dx + dy * dy) || 1, bend = Math.min(16, len * .28);
      var cx = mx - dy / len * bend, cy = my + dx / len * bend;
      var d = 'M' + hub[0].toFixed(1) + ' ' + hub[1].toFixed(1) + ' Q' + cx.toFixed(1) + ' ' + cy.toFixed(1) + ' ' + p[0].toFixed(1) + ' ' + p[1].toFixed(1);
      svg += '<path class="uh-arc" id="uhA' + i + '" d="' + d + '" pathLength="1" style="--i:' + i + '"/>';
      // الشرارة مخفية حتى تبدأ رحلتها (وإلا ظهرت في زاوية الخريطة)، وتظهر وتخفت في كل دورة
      var dur = (2.4 + (i % 4) * .5) + 's', beg = (2.2 + i * .35) + 's';
      if (!reduce) svg += '<circle class="uh-spark" r="1.1" opacity="0"><animateMotion dur="' + dur + '" begin="' + beg + '" repeatCount="indefinite" calcMode="linear"><mpath href="#uhA' + i + '"/></animateMotion>' +
        '<animate attributeName="opacity" values="0;1;1;0" keyTimes="0;.12;.8;1" dur="' + dur + '" begin="' + beg + '" repeatCount="indefinite"/></circle>';
    });
    cities.forEach(function (c, i) {
      var p = O.pt.apply(null, C[c]), big = c === 'مسقط';
      svg += '<g class="uh-city' + (big ? ' is-hub' : '') + '" data-city="' + esc(c) + '" style="--i:' + i + '" transform="translate(' + p[0].toFixed(1) + ' ' + p[1].toFixed(1) + ')">' +
        '<circle class="ring" r="' + (big ? 3 : 2.2) + '"/><circle class="dot" r="' + (big ? 2.4 : 1.6) + '"/><circle class="hit" r="5"/></g>';
    });
    svg += '</svg>';
    mapEl.insertAdjacentHTML('afterbegin', svg);
    var cityEls = [].slice.call(mapEl.querySelectorAll('.uh-city'));

    // بطاقة المدينة المتنقلة
    var cur = -1, timer = 0, visible = true;
    function spot(i) {
      cur = (i + cities.length) % cities.length;
      var c = cities[cur], p = O.pt.apply(null, C[c]);
      cityEls.forEach(function (g, k) { g.classList.toggle('on', k === cur); });
      tag.hidden = false; tag.classList.remove('in'); void tag.offsetWidth;
      tag.innerHTML = '<b>' + esc(c) + '</b><span>' + plural(count[c]) + '</span>';
      tag.style.left = (p[0] / 104 * 100) + '%'; tag.style.top = (p[1] / 137.06 * 100) + '%';
      tag.classList.toggle('below', p[1] < 34);
      tag.classList.add('in');
    }
    function loop() { clearTimeout(timer); if (!visible || reduce) return; timer = setTimeout(function () { spot(cur + 1); loop(); }, 2800); }
    // الترتيب: الأكبر أولًا
    cities.sort(function (a, b) { return count[b] - count[a]; });
    cityEls.sort(function (a, b) { return cities.indexOf(a.dataset.city) - cities.indexOf(b.dataset.city); });
    mapEl.addEventListener('click', function (e) {
      var g = e.target.closest('.uh-city');
      if (g) { var k = cities.indexOf(g.dataset.city); if (k !== cur) { spot(k); loop(); return; } goList('', g.dataset.city); return; }
      if (e.target.closest('[data-uh-tag]') && cur > -1) goList('', cities[cur]);
    });
    tag.setAttribute('role', 'button'); tag.tabIndex = 0; tag.title = 'اعرض مؤسسات المدينة';
    tag.addEventListener('keydown', function (e) { if ((e.key === 'Enter' || e.key === ' ') && cur > -1) { e.preventDefault(); goList('', cities[cur]); } });
    setTimeout(function () { spot(0); loop(); }, reduce ? 0 : 2000);
    if ('IntersectionObserver' in window) new IntersectionObserver(function (en) { visible = en[0].isIntersecting; hero.classList.toggle('paused', !visible); loop(); }).observe(hero);
  }

})();
