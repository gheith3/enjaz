/* المنجزين — «كوكبات المجالات» (الكمبيوتر، معتمد ٨ نوفمبر؛ الهيدر السابق «بطاقات الأبطال» عبر ?hero=1):
   جدار وجوه مائل في الخلفية مثل هيدر الإنجازات (٢٠ منجزًا)، وكل ٤ ثوانٍ تضيء ٥ وجوه معًا في ٨ وصلات مختلفة بالتناوب (كوكبة مجال ثم كوكبة مختلطة)
   وتصل بينهم خيوط ذهبية كالكوكبة، وتضيء شارة المجال تحت العنوان. الضغط على شارة يعرض مجالها. التنسيق: css/achievers-hero-wall.css */
(function () {
  var root = document.documentElement;
  if (!root.classList.contains('aph-wall')) return;
  var hero = document.querySelector('.ap-hero');
  if (!hero) return;
  var still = matchMedia('(prefers-reduced-motion: reduce)').matches;

  // ٢٠ منجزًا في ٤ مجالات (التوزيع هنا للمعاينة، ويُربط لاحقًا ببيانات المنجزين الفعلية)
  var G = [
    { n: 'الرياضة', f: ['t06', 't10', 't17', 't21', 't23'] },
    { n: 'العلوم والابتكار', f: ['t11', 't16', 't25', 't20', 't05'] },
    { n: 'الثقافة والأدب', f: ['t03', 't13', 't24', 't26', 't18'] },
    { n: 'الريادة والمجتمع', f: ['t02', 't08', 't14', 't27', 't29'] }
  ];
  var ALL = []; G.forEach(function (g) { ALL = ALL.concat(g.f); });
  var AR = '٠١٢٣٤٥٦٧٨٩'; function ar(n) { return String(n).replace(/\d/g, function (d) { return AR[d]; }); }
  var TW, TH, SX, SY, phone;

  // شارات المجالات تحت السطر التعريفي
  var sub = hero.querySelector('.ap-sub');
  var bar = document.createElement('div'); bar.className = 'apw-fields'; bar.setAttribute('aria-label', 'مجالات المنجزين');
  bar.innerHTML = G.map(function (g, i) { return '<button type="button" class="apw-chip" data-g="' + i + '"><i aria-hidden="true"></i>' + g.n + '</button>'; }).join('');
  if (sub) sub.insertAdjacentElement('afterend', bar);
  var chips = bar.querySelectorAll('.apw-chip');

  var wall = document.createElement('div'); wall.className = 'apw'; wall.setAttribute('aria-hidden', 'true');
  hero.insertBefore(wall, hero.firstChild);
  var grid, svg, tiles, groups, cur = -1, timer, offT;

  function build() {
    phone = !matchMedia('(min-width: 1100px)').matches;
    TW = phone ? 50 : 74; TH = phone ? 56 : 82; SX = TW + (phone ? 8 : 11); SY = TH + (phone ? 8 : 11);
    var ww = wall.clientWidth, wh = wall.clientHeight;
    var cols = Math.ceil(ww / SX) + 3, rows = Math.ceil(wh / SY) + 2;
    var W = cols * SX + SX / 2, H = rows * SY;
    wall.innerHTML = '<div class="apw-g" style="width:' + W + 'px;height:' + H + 'px;margin:' + (-H / 2) + 'px 0 0 ' + (-W / 2) + 'px"><svg class="apw-s" viewBox="0 0 ' + W + ' ' + H + '" width="' + W + '" height="' + H + '"></svg></div>';
    grid = wall.firstChild; svg = grid.firstChild;
    var cells = [];
    for (var r = 0; r < rows; r++) for (var c = 0; c < cols; c++) {
      var x = c * SX + (r % 2 ? SX / 2 : 0), y = r * SY;
      var t = document.createElement('span'); t.className = 'apw-t'; t.style.left = x + 'px'; t.style.top = y + 'px';
      grid.insertBefore(t, svg); cells.push({ el: t, x: x + TW / 2, y: y + TH / 2, r: r, c: c });
    }
    // أوضح الخانات (داخل الهيدر وبعيدة عن الكلام) تأخذ العشرين، موزّعة على المجالات بالتناوب حتى تنتشر كل كوكبة على الجدار
    wall.classList.add('apw-measure'); void wall.offsetWidth;
    var hr = (phone ? wall : hero).getBoundingClientRect(), fx = hr.width * (phone ? .5 : .27), fy = hr.height * .5;
    cells.forEach(function (k) {
      var b = k.el.getBoundingClientRect(), cx = b.left + b.width / 2 - hr.left, cy = b.top + b.height / 2 - hr.top;
      k.vis = phone ? (b.top - hr.top > -10 && hr.bottom - b.bottom > -4 && cx > 26 && cx < hr.width - 26)
        : (b.top - hr.top > -6 && hr.bottom - b.bottom > -6 && cx > 55 && cx < Math.max(hr.width * .5, 690));
      k.score = Math.abs(cx - fx) / hr.width + Math.abs(cy - fy) / hr.height * .6;
      k.hx = cx;
    });
    wall.classList.remove('apw-measure');
    var vis = cells.filter(function (k) { return k.vis; });
    // ٨ وصلات، كل وصلة ٥ وجوه متباعدة (لا يجاور وجهٌ وجهًا آخر في نفس الوصلة) حتى يُرسم خيط بين كل وجهين.
    // الوصلات ١، ٣، ٥، ٧ كوكبات المجالات الأربعة (منفصلة عن بعضها)، و٢، ٤، ٦، ٨ كوكبات مختلطة بأشكال أخرى.
    var seed = 7; function rnd() { seed = (seed * 9301 + 49297) % 233280; return seed / 233280; }
    var MIN = SX * 1.7;
    function far(set, k) { return set.every(function (o) { var dx = o.x - k.x, dy = o.y - k.y; return dx * dx + dy * dy >= MIN * MIN; }); }
    function grab(pool, avoid) {
      for (var t = 0; t < 300; t++) {
        var order = pool.slice().sort(function () { return rnd() - .5; }), set = [];
        order.forEach(function (k) { if (set.length < 5 && far(set, k) && !(avoid && avoid.indexOf(k) > -1 && t < 200)) set.push(k); });
        if (set.length === 5) return set;
      }
      return null;
    }
    if (phone) MIN = SX * 1.6;
    var fields = null, phoneAll = null;
    // الهاتف: الجدار أصغر، فتتشارك الوصلات بعض الوجوه، لكن وجوه الوصلة الواحدة تبقى متباعدة دائمًا
    if (phone) { fields = []; var seen = [];
      for (var pq = 0; pq < 8; pq++) { var gp = grab(vis, seen.slice(-10)) || grab(vis) || vis.slice(0, 5); fields.push(gp); seen = seen.concat(gp); }
      phoneAll = fields; fields = fields.slice(0, 4); }
    for (var tries = 0; tries < 40 && !fields; tries++) {
      var left = vis.slice(), out = [];
      for (var q = 0; q < 4; q++) { var g5 = grab(left); if (!g5) { out = null; break; }
        out.push(g5); left = left.filter(function (k) { return g5.indexOf(k) < 0; }); }
      if (out) fields = out;
    }
    if (!fields) fields = [0, 1, 2, 3].map(function () { return grab(vis) || vis.slice(0, 5); });
    fields.forEach(function (set, q) { set.forEach(function (k, i) { k.p = G[q].f[i]; }); });
    groups = [];
    for (var q2 = 0; q2 < 4; q2++) { groups.push(fields[q2]); groups.push(grab(vis, fields[q2].concat(fields[(q2 + 1) % 4])) || fields[(q2 + 2) % 4]); }
    if (phoneAll) groups = phoneAll;
    // بقية الخانات تكرر نفس العشرين دون تجاور الوجه نفسه
    var j = 0;
    cells.forEach(function (k) {
      if (!k.p) { k.p = ALL[(k.r * 7 + k.c * 3 + j++) % ALL.length]; }
      k.el.innerHTML = '<img src="assets/img/ah-tiles/' + k.p + '.jpg?v=2" alt="" width="216" height="240" decoding="async">';
      // ظهور عند الفتح: من جهة العنوان نحو اليسار
      k.el.style.setProperty('--d', (.15 + Math.max(0, (hr.width * (phone ? 1 : .62) - k.hx)) / hr.width * 1.1 + k.r * .04).toFixed(2) + 's');
    });
    tiles = cells;
  }

  // خيوط ذهبية بين وجوه المجال: من الحافة إلى الحافة، ترتيب الأقرب فالأقرب
  function edge(dx, dy, hw, hh) { var ax = Math.abs(dx) || 1e-6, ay = Math.abs(dy) || 1e-6; return Math.min(hw / ax, hh / ay); }
  function near(list) {
    var rest = list.slice().sort(function (a, b) { return a.y - b.y || a.x - b.x; }), out = [rest.shift()];
    while (rest.length) { var l = out[out.length - 1], bi = 0, bd = 1e9;
      rest.forEach(function (k, i) { var d = (k.x - l.x) * (k.x - l.x) + (k.y - l.y) * (k.y - l.y); if (d < bd) { bd = d; bi = i; } });
      out.push(rest.splice(bi, 1)[0]); }
    return out;
  }
  function lines(g) {
    var pts = g % 2 ? near(groups[g]) : groups[g].slice().sort(function (a, b) { return a.x - b.x; }), out = '', hw = TW * .57 + 7, hh = TH * .57 + 7;
    for (var i = 0; i < pts.length - 1; i++) {
      var a = pts[i], b = pts[i + 1], dx = b.x - a.x, dy = b.y - a.y, L = Math.sqrt(dx * dx + dy * dy);
      var t = edge(dx, dy, hw, hh); if (2 * t >= 1) continue;
      var x1 = a.x + dx * t, y1 = a.y + dy * t, x2 = b.x - dx * t, y2 = b.y - dy * t;
      out += '<line pathLength="1" x1="' + x1.toFixed(1) + '" y1="' + y1.toFixed(1) + '" x2="' + x2.toFixed(1) + '" y2="' + y2.toFixed(1) + '" style="--ld:' + (.15 + i * .18).toFixed(2) + 's"/>' +
        '<circle r="2.4" cx="' + x1.toFixed(1) + '" cy="' + y1.toFixed(1) + '" style="--ld:' + (.15 + i * .18).toFixed(2) + 's"/><circle r="2.4" cx="' + x2.toFixed(1) + '" cy="' + y2.toFixed(1) + '" style="--ld:' + (.4 + i * .18).toFixed(2) + 's"/>';
    }
    svg.innerHTML = out;
  }

  function show(g) {
    cur = g; clearTimeout(offT);
    tiles.forEach(function (k) { k.el.classList.remove('on'); });
    groups[g].forEach(function (k, i) { k.el.style.setProperty('--od', (i * .09).toFixed(2) + 's'); k.el.classList.add('on'); });
    var f = g % 2 ? -1 : g / 2; [].forEach.call(chips, function (c, i) { c.classList.toggle('is-on', i === f); c.setAttribute('aria-pressed', i === f ? 'true' : 'false'); });
    wall.classList.add('has-on'); wall.classList.remove('lines-on'); lines(g);
    requestAnimationFrame(function () { requestAnimationFrame(function () { wall.classList.add('lines-on'); }); });
    if (!still) offT = setTimeout(function () { wall.classList.remove('lines-on'); }, 3300);
  }
  function next() { show((cur + 1) % groups.length); }
  function loop() { clearInterval(timer); if (!still) timer = setInterval(function () { if (!hero.matches(':hover')) next(); }, 4200); }

  if (getComputedStyle(wall).display === 'none') return;
  build();
  [].forEach.call(chips, function (c) { c.addEventListener('click', function () { show(2 * c.getAttribute('data-g')); loop(); }); });
  setTimeout(function () { show(0); loop(); }, still ? 0 : 1500);
  var rt; addEventListener('resize', function () { clearTimeout(rt); rt = setTimeout(function () { if (getComputedStyle(wall).display === 'none') return; build(); show(cur < 0 ? 0 : cur); }, 250); });
})();
