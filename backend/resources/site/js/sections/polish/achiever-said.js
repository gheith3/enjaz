/* صفحة المنجز — «قالوا عن…» بتصميم «غبار ذهبي» المعتمد (تصاميم ٢٠٥٠ / معتمد / قالوا عن - غبار ذهبي - معتمد).
   الذرّات تخرج من صورة القائل وتكتب قوله ذهبيًا (بحركة انطلاق مختلفة كل مرة)، ثم يثبت أبيض في سطر واحد،
   ويتبدّل القول كل ٧ ثوانٍ تقريبًا. الأقوال تُقرأ من القائمة المخفية التي يرسمها القالب. */
(function () {
  var root = document.querySelector('[data-gd]');
  if (!root) return;
  var Q = [].slice.call(root.querySelectorAll('.gd-sr li')).map(function (li) { return { q: li.dataset.q, n: li.dataset.n, r: li.dataset.r || '' }; });
  if (!Q.length) return;
  var reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  var AD = '٠١٢٣٤٥٦٧٨٩';
  function two(n) { return String(n < 10 ? '0' + n : n).replace(/\d/g, function (d) { return AD[d]; }); }
  var HOLD = 4800;
  var main = root.querySelector('.gd-main'), cv = root.querySelector('canvas'), ctx = cv.getContext('2d');
  var bEl = root.querySelector('.gd-by b'), sEl = root.querySelector('.gd-by small'), av = root.querySelector('.gd-av'), ring = root.querySelector('.gd-ring');
  var noB = root.querySelector('.gd-no b'); root.querySelector('.gd-no i').textContent = '/ ' + two(Q.length);
  root.style.setProperty('--hold', HOLD + 'ms');
  var avs = [].slice.call(av.querySelectorAll('img'));
  avs.forEach(function (img) { img.loading = 'eager'; });
  var pbs = [].slice.call(root.querySelectorAll('.picks button'));
  pbs.forEach(function (b, i) { b.addEventListener('click', function () { if (i !== cur) show(i); }); });

  var dpr = Math.min(2, window.devicePixelRatio || 1), W, H, FONT, FONT0, LH, PADX, BYH, MAXP = 4600;
  var P = [], lines = [], box = null, textA = 0, phase = 'idle', phaseAt = 0, holdAt = 0, cur = -1, timers = [], running = false;
  var mouse = { x: -1e3, y: -1e3, on: false }, AV = { x: 0, y: 0, r: 50 };
  var font = getComputedStyle(root).fontFamily || 'Cairo';

  function rects() {
    var R = root.getBoundingClientRect(), M = main.getBoundingClientRect(), A = av.getBoundingClientRect();
    AV = { x: A.left - R.left + A.width / 2, y: A.top - R.top + A.height / 2, r: A.width / 2 + 6 };
    return { l: M.left - R.left, t: M.top - R.top, w: M.width, h: M.height };
  }
  function size() {
    W = root.clientWidth; H = root.clientHeight; cv.width = W * dpr; cv.height = H * dpr;
    var small = W < 560; FONT0 = small ? 18 : 26; FONT = FONT0; LH = FONT * 1.7; PADX = small ? 18 : 34; BYH = small ? 46 : 44;
    P = [];
    for (var i = 0; i < MAXP; i++) P.push({ x: Math.random() * W, y: Math.random() * H, vx: 0, vy: 0, tx: 0, ty: 0, has: 0, rel: 0, ph: Math.random() * 6.28, r: Math.random() });
  }
  // يرسم القول خارج الشاشة ويأخذ نقاط حروفه أهدافًا للذرّات؛ سطر واحد ما أمكن (لا يقل الخط عن ١٦)
  function layout(k) {
    var m = rects(), o = document.createElement('canvas'); o.width = W; o.height = H; var c = o.getContext('2d');
    var raw = Q[k].q.trim(), txt = /^[«"“]/.test(raw) ? raw : '«' + raw + '»', words = txt.split(' '), maxW = m.w - PADX * 2 - (W < 560 ? 0 : 40);
    c.font = '800 ' + FONT0 + 'px ' + font; var w0 = c.measureText(txt).width;
    FONT = Math.max(16, Math.min(FONT0, Math.floor(FONT0 * maxW / w0 * 10) / 10)); LH = FONT * 1.7;
    c.font = '800 ' + FONT + 'px ' + font; c.direction = 'rtl'; c.textAlign = 'right'; c.textBaseline = 'middle';
    var total = c.measureText(txt).width;
    var nl = Math.max(1, Math.ceil(total / maxW)), target = nl === 1 ? maxW + 1 : Math.min(maxW, total / nl * 1.08), ls = [], line = '';
    words.forEach(function (w) { var t = line ? line + ' ' + w : w; if (c.measureText(t).width > target && line) { ls.push(line); line = w; } else line = t; });
    if (line) ls.push(line);
    var top = m.t + (m.h - BYH) / 2 - (ls.length - 1) * LH / 2 + 11, right = m.l + m.w - PADX;
    lines = ls.map(function (t, i) { return { t: t, x: right, y: top + i * LH }; });
    var wMax = Math.max.apply(null, ls.map(function (t) { return c.measureText(t).width; }));
    box = { r: right, l: right - wMax, t: top - LH / 2, b: top + (ls.length - .5) * LH, cy: top + (ls.length - 1) * LH / 2 };
    c.fillStyle = '#fff'; lines.forEach(function (L) { c.fillText(L.t, L.x, L.y); });
    var d = c.getImageData(0, 0, W, H).data, pts = [], step = FONT < 22 ? 2 : 3;
    for (var y = 0; y < H; y += step) for (var x = 0; x < W; x += step) if (d[(y * W + x) * 4 + 3] > 130) pts.push([x, y]);
    for (var i = pts.length - 1; i > 0; i--) { var j = (Math.random() * (i + 1)) | 0, t = pts[i]; pts[i] = pts[j]; pts[j] = t; }
    return pts.slice(0, MAXP);
  }
  /* حركة الانطلاق من الصورة تختلف في كل انتقال: ٠ تيار، ١ دوّامة، ٢ انفجار، ٣ موجة، ٤ نافورة */
  var MODES = 5, mode = 0, shown = 0;
  function assign(pts, now) {
    for (var i = P.length - 1; i > 0; i--) { var j = (Math.random() * (i + 1)) | 0, t = P[i]; P[i] = P[j]; P[j] = t; }
    var bw = (box.r - box.l) || 1;
    P.forEach(function (p, i) {
      if (i < pts.length) {
        p.tx = pts[i][0]; p.ty = pts[i][1]; p.has = 1;
        var ord = (box.r - p.tx) / bw;
        p.rel = now + (reduce ? 0 : mode === 2 ? Math.random() * 140 : mode === 4 ? Math.random() * 900 : ord * 800 + Math.random() * 220);
        p.kick = 0;
        var a = Math.random() * 6.28, rr = AV.r + Math.random() * 10;
        p.x = AV.x + Math.cos(a) * rr; p.y = AV.y + Math.sin(a) * rr; p.vx = p.vy = 0; p.ph = a;
      } else {
        p.has = 0;
      }
    });
  }
  function toHold() { if (phase !== 'gather') return; phase = 'hold'; holdAt = performance.now(); kick(); }
  function later(fn, ms) { timers.push(setTimeout(fn, ms)); }

  // التشغيل التلقائي + حلقة المؤقّت (يتوقف أثناء وجود الماوس)
  var tm = null, hover = false, live = false;
  function ringStop() { ring.classList.remove('run'); }
  function kick() {
    clearTimeout(tm); ringStop();
    if (!live || hover || reduce || phase !== 'hold' || Q.length < 2) return;
    void ring.offsetWidth; ring.classList.add('run');
    tm = setTimeout(function () { show((cur + 1) % Q.length); }, HOLD);
  }
  root.addEventListener('mouseenter', function () { hover = true; clearTimeout(tm); ringStop(); });
  root.addEventListener('mouseleave', function () { hover = false; kick(); });

  function show(k) {
    timers.forEach(clearTimeout); timers = []; clearTimeout(tm); ringStop();
    var first = cur < 0; cur = k; mode = shown++ % MODES;
    pbs.forEach(function (b, i) { b.classList.toggle('on', i === k); b.setAttribute('aria-pressed', i === k); });
    root.classList.add('swap');
    var it = Q[k];
    var go = function () {
      avs.forEach(function (a, i) { a.classList.toggle('on', i === k); });
      av.classList.remove('pulse'); void av.offsetWidth; av.classList.add('pulse');
      var now = performance.now();
      assign(layout(k), now); phase = 'gather'; phaseAt = now;
      later(function () { bEl.textContent = it.n; sEl.textContent = it.r; noB.textContent = two(k + 1); root.classList.remove('swap'); }, reduce ? 0 : 900);
      later(toHold, reduce ? 50 : 2200);
    };
    if (first || reduce) go();
    else { avs.forEach(function (a) { a.classList.remove('on'); }); phase = 'wind'; phaseAt = performance.now(); later(go, 700); }
  }

  function frame(now) {
    if (!running) return;
    var t = now / 1000;
    var wantText = phase === 'hold' ? 1 : 0;
    textA += (wantText - textA) * (wantText ? .2 : .22);
    var windP = phase === 'wind' ? Math.min(1, (now - phaseAt) / 700) : 0;
    var fadeIn = phase === 'gather' ? Math.min(1, (now - phaseAt) / 450) : 1;
    var gx = -1e4;
    if (phase === 'hold' && box) { var gp = (now - holdAt - 250) / 1500; if (gp > 0 && gp < 1) gx = box.r + 40 - gp * (box.r - box.l + 120); }

    ctx.setTransform(dpr, 0, 0, dpr, 0, 0); ctx.clearRect(0, 0, W, H);
    ctx.globalCompositeOperation = 'lighter';
    if (mouse.on) {
      var lg = ctx.createRadialGradient(mouse.x, mouse.y, 0, mouse.x, mouse.y, 90);
      lg.addColorStop(0, 'rgba(245,207,122,.16)'); lg.addColorStop(1, 'rgba(245,207,122,0)');
      ctx.fillStyle = lg; ctx.fillRect(mouse.x - 90, mouse.y - 90, 180, 180);
    }
    ctx.fillStyle = '#F5CF7A';
    var nHas = 0, nIn = 0;
    for (var i = 0; i < P.length; i++) {
      var p = P[i], waiting = false;
      if (phase === 'wind') {
        p.vx += -(.35 + p.r * .6); p.vy += Math.sin(p.x * .018 + t * 4 + p.ph) * .5 - .05;
        p.vx *= .93; p.vy *= .93;
      } else if (p.has) {
        if (now < p.rel) {
          waiting = true; var ang = p.ph + t * 1.6, rr = AV.r + 3 + p.r * 8;
          p.x = AV.x + Math.cos(ang) * rr; p.y = AV.y + Math.sin(ang) * rr;
          p.vx = -Math.sin(ang) * 2.2; p.vy = Math.cos(ang) * 2.2;
        } else {
          var age = now - p.rel, k = .01 + .055 * Math.min(1, age / 800), damp = .82, tx = p.tx - p.x, ty = p.ty - p.y;
          if (!p.kick) {
            p.kick = 1;
            if (mode === 2) { var ba = Math.random() * 6.28, bs = 7 + Math.random() * 9; p.vx = Math.cos(ba) * bs; p.vy = Math.sin(ba) * bs; }
            else if (mode === 4) { p.vx = -(1.5 + Math.random() * 4); p.vy = -(7 + Math.random() * 6); }
          }
          if (mode === 1 && phase === 'gather') {
            var dl = Math.sqrt(tx * tx + ty * ty) || 1, sw = 2.4 * Math.max(0, 1 - age / 1000) * Math.min(1, dl / 40);
            p.vx += -ty / dl * sw; p.vy += tx / dl * sw; damp = .86;
          } else if (mode === 2 && age < 320) { k = 0; damp = .9; }
          else if (mode === 3 && phase === 'gather') { p.vy += Math.sin(age * .013 + p.tx * .025) * 2.4 * Math.max(0, 1 - age / 1000); }
          else if (mode === 4 && age < 380) { k = .004; damp = .97; p.vy += .35; }
          p.vx += tx * k; p.vy += ty * k; p.vx *= damp; p.vy *= damp;
          if (phase === 'hold') { p.vx += (Math.random() - .5) * .25; p.vy += (Math.random() - .5) * .25; }
        }
      } else {
        p.vx += (Math.random() - .5) * .06 - .01; p.vy += (Math.random() - .5) * .06; p.vx *= .97; p.vy *= .97;
      }
      if (mouse.on && !waiting) { var dx = p.x - mouse.x, dy = p.y - mouse.y, d2 = dx * dx + dy * dy; if (d2 < 4200) { var f = (1 - d2 / 4200) * 2.6, dd = Math.sqrt(d2) || 1; p.vx += dx / dd * f; p.vy += dy / dd * f; } }
      if (!waiting) { p.x += p.vx; p.y += p.vy; }
      if (p.has) { nHas++; if (!waiting && Math.abs(p.tx - p.x) + Math.abs(p.ty - p.y) < 4) nIn++; }
      if (p.x < -10) p.x += W + 20; else if (p.x > W + 10) p.x -= W + 20;
      if (p.y < -10) p.y += H + 20; else if (p.y > H + 10) p.y -= H + 20;
      var sp = Math.abs(p.vx) + Math.abs(p.vy), a, z = sp > 3 ? 2.1 : 1.4;
      if (phase === 'wind') a = (.18 + Math.min(.6, sp * .08)) * (1 - windP * .6);
      else if (!p.has) a = (.12 + Math.min(.5, sp * .08)) * (.4 + .6 * fadeIn);
      else if (waiting) a = .6 * fadeIn;
      else a = (.85 - textA * .6) * (.75 + .25 * Math.sin(t * 5 + p.ph)) + Math.min(.3, sp * .05);
      if (p.has && gx > -1e3) { var gd = Math.abs(p.x - gx + (p.y - box.cy) * .45); if (gd < 26) { var g = 1 - gd / 26; a += g * .95; z += g * 1.2; } }
      if (a < .02) continue;
      ctx.globalAlpha = Math.min(1, a); ctx.fillRect(p.x, p.y, z, z);
    }
    ctx.globalCompositeOperation = 'source-over'; ctx.globalAlpha = 1;
    if (phase === 'gather' && nHas && nIn / nHas > .94 && now - phaseAt > 600) toHold();
    if (textA > .01) {
      ctx.globalAlpha = textA; ctx.font = '800 ' + FONT + 'px ' + font; ctx.direction = 'rtl'; ctx.textAlign = 'right'; ctx.textBaseline = 'middle';
      ctx.fillStyle = '#FFFFFF'; ctx.shadowColor = 'rgba(245,207,122,.32)'; ctx.shadowBlur = 12;
      lines.forEach(function (L) { ctx.fillText(L.t, L.x, L.y); });
      ctx.shadowBlur = 0; ctx.globalAlpha = 1;
    }
    requestAnimationFrame(frame);
  }
  main.addEventListener('pointermove', function (e) { var r = root.getBoundingClientRect(); mouse.x = e.clientX - r.left; mouse.y = e.clientY - r.top; mouse.on = true; });
  main.addEventListener('pointerleave', function () { mouse.on = false; mouse.x = mouse.y = -1e3; });

  // يعمل فقط وهو ظاهر على الشاشة (توفير للمعالج)
  function setRun(v) { if (v && !running) { running = true; requestAnimationFrame(frame); } else if (!v) running = false; }
  var fontsReady = Promise.race([document.fonts ? document.fonts.load('800 26px ' + font) : Promise.resolve(), new Promise(function (r) { setTimeout(r, 2500); })]);
  fontsReady.then(function () {
    size(); rects();
    if (!('IntersectionObserver' in window)) { setRun(true); live = true; show(0); return; }
    new IntersectionObserver(function (es) { setRun(es[0].isIntersecting); }, { threshold: 0 }).observe(root);
    new IntersectionObserver(function (es, ob) { if (es[0].isIntersecting) { ob.disconnect(); live = true; show(0); } }, { threshold: .3 }).observe(root);
  });
  var rw; addEventListener('resize', function () { clearTimeout(rw); rw = setTimeout(function () { size(); if (cur >= 0) assign(layout(cur), performance.now() - 5000); }, 200); });
})();
