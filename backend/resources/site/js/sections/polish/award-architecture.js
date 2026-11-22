/* جائزة بلعرب — التصميم المعتمد (تصاميم ٢٠٥٠ / معتمد / صفحة جائزة السيد بلعرب - ملف 2). الحركة فقط؛ المحتوى يرسمه القالب.
   كل جزء معزول في دالته ويعمل فقط إذا وجد عناصره. */
(function () {
  var page = document.querySelector('[data-bf] [data-ax]');
  if (!page) return;
  var reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  var IO = 'IntersectionObserver' in window;
  var AD = '٠١٢٣٤٥٦٧٨٩';
  var cz = document.querySelector('[data-cz]');
  var L = {};
  try { L = JSON.parse((cz && cz.dataset.labels) || '{}'); } catch (e) { L = {}; }
  function toAr(n, sep) { var t = String(n); if (sep && n >= 1000) t = t.replace(/\B(?=(\d{3})+(?!\d))/g, '٬'); return t.replace(/\d/g, function (d) { return AD[d]; }); }
  function fromAr(t) { return t.replace(/[٠-٩]/g, function (d) { return AD.indexOf(d); }).replace(/[٬,]/g, ''); }
  function two(n) { return toAr(n < 10 ? '0' + n : n); }
  function once(el, fn, th) {
    if (!IO || reduce) return fn();
    new IntersectionObserver(function (es, ob) { if (es[0].isIntersecting) { ob.disconnect(); fn(); } }, { threshold: th || .3 }).observe(el);
  }
  function reveal(sel, th) { var e = document.querySelector(sel); if (e) once(e, function () { e.classList.add('in'); }, th); }
  reveal('[data-ax]', .08); reveal('[data-px]', .3); reveal('[data-mx]', .1);

  // الهيدر: الصورة تتحرك قليلًا مع الماوس
  (function () {
    var hero = document.querySelector('[data-bf] .c-hero');
    if (!hero || reduce) return;
    hero.addEventListener('pointermove', function (e) {
      var r = hero.getBoundingClientRect();
      hero.style.setProperty('--hx', ((e.clientX - r.left) / r.width - .5).toFixed(3));
      hero.style.setProperty('--hy', ((e.clientY - r.top) / r.height - .5).toFixed(3));
    });
    hero.addEventListener('pointerleave', function () { hero.style.setProperty('--hx', 0); hero.style.setProperty('--hy', 0); });
  })();

  // صورة العرض بجانب «عن الجائزة»: كل ٣ ثوانٍ صورة، بانتقال «البناء» (أعمدة الصورة الجديدة تصعد من اليمين إلى اليسار)
  (function () {
    var box = document.querySelector('[data-axs]');
    if (!box) return;
    var D = [].slice.call(box.querySelectorAll('.ax-data li')).map(function (li) { return li.dataset; });
    if (D.length < 2) return;
    var stage = box.querySelector('.ax-stage'), base = stage.querySelector('.ax-base'), ttl = box.querySelector('.ax-ttl'), tb = ttl.querySelector('b'), ts = ttl.querySelector('small');
    var capBar = box.querySelector('.ax-capbar'), cap = capBar.querySelector('.ax-cap'), cnt = capBar.querySelector('.ax-cnt b');
    var MS = 3000, COLS = 7, k = 0, tm = null, vis = false, busy = false;
    addEventListener('load', function () { D.forEach(function (d) { var im = new Image(); im.src = d.src; }); });
    function texts(d, n) {
      if (tb.textContent !== d.t) { ttl.classList.add('swap'); setTimeout(function () { tb.textContent = d.t; ts.textContent = d.s; ttl.classList.remove('swap'); }, 420); }
      capBar.classList.add('swap');
      setTimeout(function () { cap.textContent = d.c; cnt.textContent = two(n + 1); capBar.classList.remove('swap'); }, 380);
    }
    function settle(d) { base.src = d.src; base.style.objectPosition = d.pos; base.alt = (d.t + ': ' + d.c).replace(/:\s*$/, ''); }
    function show(n) {
      if (busy || n === k) return;
      var d = D[n]; busy = true; k = n; texts(d, n); plan();
      if (reduce) { settle(d); busy = false; return; }
      var W = stage.clientWidth, cw = W / COLS, cols = [];
      stage.classList.add('building');
      for (var i = 0; i < COLS; i++) {
        var c = document.createElement('span');
        c.className = 'ax-col'; c.style.right = (i * cw) + 'px'; c.style.width = Math.ceil(cw + 1) + 'px'; c.style.setProperty('--dl', (i * 60) + 'ms');
        var img = document.createElement('img'); img.alt = ''; img.src = d.src; img.style.width = W + 'px'; img.style.right = (-i * cw) + 'px'; img.style.objectPosition = d.pos;
        c.appendChild(img); stage.appendChild(c); cols.push(c);
      }
      void stage.offsetWidth;
      cols.forEach(function (c) { c.classList.add('up'); });
      var total = 700 + (COLS - 1) * 60;
      setTimeout(function () { cols.forEach(function (c) { c.classList.add('done'); }); stage.classList.remove('building'); }, total);
      setTimeout(function () {
        settle(d);
        var fin = function () { cols.forEach(function (c) { c.remove(); }); busy = false; };
        if (base.decode) base.decode().then(fin, fin); else fin();
      }, total + 300);
    }
    function next() { show((k + 1) % D.length); }
    function plan() { clearTimeout(tm); if (vis && !reduce) tm = setTimeout(function () { if (busy) return setTimeout(next, 200); next(); }, MS); }
    if (IO) new IntersectionObserver(function (es) { vis = es[0].isIntersecting; plan(); }, { threshold: .2 }).observe(box);
    else { vis = true; plan(); }
  })();

  // الفائزون: الصور المصغّرة، و«اقرأ المزيد» فقط حين يكون النص أطول من ثلاثة أسطر
  (function () {
    if (!cz) return;
    [].forEach.call(cz.querySelectorAll('.cz-thumbs'), function (t) {
      var big = t.parentNode.querySelector('.cz-img'), bi = big.querySelector('img');
      [].forEach.call(t.children, function (b) {
        b.addEventListener('click', function () {
          [].forEach.call(t.children, function (x) { x.classList.toggle('on', x === b); });
          bi.src = b.dataset.src; bi.alt = b.dataset.cap; big.dataset.lb = b.dataset.src; big.dataset.cap = b.dataset.cap;
        });
      });
    });
    function fitMore() {
      [].forEach.call(cz.querySelectorAll('.cz-w'), function (w) {
        var p = w.querySelector('.cz-why p'), b = w.querySelector('.cz-more');
        if (p && b && !w.classList.contains('open')) b.hidden = p.scrollHeight <= p.clientHeight + 2;
      });
    }
    [].forEach.call(cz.querySelectorAll('.cz-more'), function (b) {
      b.addEventListener('click', function () { var w = b.closest('.cz-w'), o = w.classList.toggle('open'); b.setAttribute('aria-expanded', o); });
    });
    addEventListener('load', fitMore); addEventListener('resize', fitMore); setTimeout(fitMore, 300);
  })();

  // عارض الصور: صور القسم نفسه مجموعة واحدة
  (function () {
    var lb = document.createElement('div'); lb.className = 'lbx'; lb.setAttribute('role', 'dialog'); lb.setAttribute('aria-modal', 'true'); lb.setAttribute('aria-label', L.viewer || '');
    lb.innerHTML = '<span class="ct"></span><button type="button" class="x">✕</button><button type="button" class="pv">›</button><button type="button" class="nx">‹</button><figure><img alt=""><figcaption></figcaption></figure>';
    lb.querySelector('.x').setAttribute('aria-label', L.close || ''); lb.querySelector('.pv').setAttribute('aria-label', L.vprev || ''); lb.querySelector('.nx').setAttribute('aria-label', L.vnext || '');
    document.body.appendChild(lb);
    var G = [], gi = 0, lbI = lb.querySelector('img'), lbC = lb.querySelector('figcaption'), lbN = lb.querySelector('.ct'), last = null;
    function paint() {
      var b = G[gi]; lbI.src = b.dataset.lb; lbI.alt = b.dataset.cap || ''; lbC.textContent = b.dataset.cap || '';
      if (b.dataset.cr) { var sm = document.createElement('small'); sm.textContent = (L.credit || ':credit').replace(':credit', b.dataset.cr); lbC.appendChild(sm); }
      lbN.textContent = toAr(gi + 1) + ' / ' + toAr(G.length);
    }
    function openLb(b) {
      var scope = b.closest('.cz-panel, .mx, .ax') || document;
      G = [].slice.call(scope.querySelectorAll('.cz-img')).filter(function (x) { return x.offsetParent !== null; });
      gi = Math.max(0, G.indexOf(b)); last = b; paint(); lb.classList.add('on'); lb.querySelector('.x').focus();
    }
    function closeLb() { lb.classList.remove('on'); if (last) last.focus(); }
    function step(d) { gi = (gi + d + G.length) % G.length; paint(); }
    document.addEventListener('click', function (e) { var b = e.target.closest('[data-bf] .cz-img'); if (b) openLb(b); });
    lb.querySelector('.x').addEventListener('click', closeLb);
    lb.querySelector('.pv').addEventListener('click', function () { step(-1); });
    lb.querySelector('.nx').addEventListener('click', function () { step(1); });
    lb.addEventListener('click', function (e) { if (e.target === lb) closeLb(); });
    document.addEventListener('keydown', function (e) {
      if (!lb.classList.contains('on')) return;
      if (e.key === 'Escape') closeLb(); else if (e.key === 'ArrowLeft') step(1); else if (e.key === 'ArrowRight') step(-1);
    });
  })();

  // شريط تقدّم القراءة
  (function () {
    var bar = document.createElement('div'); bar.className = 'bx-prog'; bar.setAttribute('aria-hidden', 'true'); document.body.appendChild(bar);
    function prog() { var h = document.documentElement.scrollHeight - innerHeight; bar.style.setProperty('--p', h > 0 ? (scrollY / h).toFixed(4) : 0); }
    addEventListener('scroll', prog, { passive: true }); prog();
  })();

  // شريط الأقسام: يضيء القسم الحالي، وملتصق بأسفل الشريط الأخضر
  (function () {
    var hdr = document.querySelector('.site-header');
    function hh() { if (hdr) document.documentElement.style.setProperty('--hh', Math.round(hdr.getBoundingClientRect().height) + 'px'); }
    hh(); addEventListener('resize', hh); addEventListener('load', hh);
    var jump = document.querySelector('[data-bf] .c-jump');
    if (!jump || !IO) return;
    var JA = [].slice.call(jump.querySelectorAll('a[href^="#"]')), map = {}, ratios = {};
    JA.forEach(function (a) { var t = document.querySelector(a.getAttribute('href')); if (t) map[t.id] = a; });
    var io = new IntersectionObserver(function (es) {
      es.forEach(function (e) { ratios[e.target.id] = e.isIntersecting ? e.intersectionRatio : 0; });
      var best = null, bv = 0; Object.keys(ratios).forEach(function (id) { if (ratios[id] > bv) { bv = ratios[id]; best = id; } });
      JA.forEach(function (a) { a.classList.toggle('on', !!best && map[best] === a); });
      var on = best && map[best]; if (on && jump.scrollWidth > jump.clientWidth) jump.scrollTo({ left: on.offsetLeft - jump.clientWidth / 2 + on.offsetWidth / 2, behavior: 'smooth' });
    }, { rootMargin: '-35% 0px -55% 0px', threshold: [0, .01, .5, 1] });
    Object.keys(map).forEach(function (id) { io.observe(document.getElementById(id)); });
  })();

  // عناوين الأقسام: الخط يُرسم عند الوصول
  [].forEach.call(document.querySelectorAll('[data-bf] .c-head'), function (h) { once(h, function () { h.classList.add('bx-in'); }, .6); });

  // الأرقام تعدّ من الصفر عند ظهورها
  [].forEach.call(document.querySelectorAll('.ax-stat dd, .cz-band-s b, .mx-route b'), function (el) {
    var raw = el.textContent.trim(), m = fromAr(raw).match(/^(\d+)(.*)$/);
    if (!m || +m[1] < 3 || reduce) return;
    var to = +m[1], cut = raw.search(/[^٠-٩٬,]/), rest = cut < 0 ? '' : raw.slice(cut), sep = /[٬,]/.test(raw);
    el.classList.add('bx-num'); el.textContent = toAr(0) + rest;
    once(el, function () {
      var t0 = performance.now();
      (function f(now) { var p = Math.min(1, (now - t0) / 1400), e = 1 - Math.pow(1 - p, 3); el.textContent = toAr(Math.round(to * e), sep) + rest; if (p < 1) requestAnimationFrame(f); })(t0);
    }, .6);
  });

  // بطاقة المشارك تميل مع الماوس
  (function () {
    var px = document.querySelector('[data-px]');
    if (!px || reduce || !matchMedia('(hover: hover)').matches) return;
    px.addEventListener('pointermove', function (e) {
      var r = px.getBoundingClientRect(), x = (e.clientX - r.left) / r.width, y = (e.clientY - r.top) / r.height;
      px.style.setProperty('--tx', ((x - .5) * 6).toFixed(2) + 'deg'); px.style.setProperty('--ty', ((.5 - y) * 5).toFixed(2) + 'deg');
      px.style.setProperty('--gx', (x * 100).toFixed(1) + '%'); px.style.setProperty('--gy', (y * 100).toFixed(1) + '%');
    });
    px.addEventListener('pointerleave', function () { px.style.setProperty('--tx', '0deg'); px.style.setProperty('--ty', '0deg'); });
  })();

  // شريط صور المشروع والتتويج: أسهم، وعدد الصور، ومؤشر تقدّم، و«اسحب لرؤية المزيد»
  [].forEach.call(document.querySelectorAll('[data-bf] .cz-gal'), function (g) {
    var arrow = function (d) { return '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="' + d + '"/></svg>'; };
    var w = document.createElement('div'); w.className = 'bx-galw'; g.parentNode.insertBefore(w, g); w.appendChild(g);
    var pv = document.createElement('button'), nx = document.createElement('button');
    pv.type = nx.type = 'button'; pv.className = 'bx-ar pv'; nx.className = 'bx-ar nx';
    pv.setAttribute('aria-label', L.prev || ''); nx.setAttribute('aria-label', L.next || '');
    pv.innerHTML = arrow('M9 5l7 7-7 7'); nx.innerHTML = arrow('M15 5l-7 7 7 7');
    w.appendChild(pv); w.appendChild(nx);
    var hint = document.createElement('div'); hint.className = 'bx-galhint';
    var count = document.createElement('b'); count.textContent = (L.count || ':n').replace(':n', toAr(g.children.length));
    var trkWrap = document.createElement('span'); trkWrap.className = 'trk'; trkWrap.setAttribute('aria-hidden', 'true'); var trk = document.createElement('i'); trkWrap.appendChild(trk);
    var sw = document.createElement('span'); sw.className = 'swipe';
    hint.appendChild(count); hint.appendChild(trkWrap); hint.appendChild(sw);
    w.parentNode.insertBefore(hint, w);
    function upd() {
      var max = g.scrollWidth - g.clientWidth, x = Math.abs(g.scrollLeft), r = max > 0 ? x / max : 1, vw = g.clientWidth / g.scrollWidth;
      trk.style.setProperty('--w', (vw * 100).toFixed(1) + '%'); trk.style.setProperty('--x', (r * (1 - vw) * 100).toFixed(1) + '%');
      var end = max <= 4 || x > max - 4;
      w.classList.toggle('end', end); hint.classList.toggle('end', end);
      pv.disabled = x < 4; nx.disabled = end;
      sw.style.visibility = max <= 4 ? 'hidden' : '';
      sw.textContent = end ? (L.end || '') : (L.swipe || '');
    }
    pv.addEventListener('click', function () { g.scrollBy({ left: g.clientWidth * .8, behavior: 'smooth' }); });
    nx.addEventListener('click', function () { g.scrollBy({ left: -g.clientWidth * .8, behavior: 'smooth' }); });
    g.addEventListener('scroll', upd, { passive: true }); addEventListener('resize', upd); addEventListener('load', upd); setTimeout(upd, 300);
  });

  // صورة المسابقة الخاصة: عمق خفيف مع التمرير
  (function () {
    var exi = document.querySelector('.mx-ex-media > .cz-img img');
    if (!exi || reduce) return;
    addEventListener('scroll', function () {
      var r = exi.parentNode.getBoundingClientRect(); if (r.bottom < 0 || r.top > innerHeight) return;
      exi.style.setProperty('--py', (((r.top + r.height / 2) / innerHeight - .5) * -24).toFixed(1) + 'px');
    }, { passive: true });
  })();

  // كلمة سموّه: الخط الذهبي جزء من الصورة، والصورة تبقى ظاهرة أثناء قراءة الكلمة كاملة
  (function () {
    var word = document.querySelector('[data-bf] .c-word');
    if (!word) return;
    var wst = word.querySelector('.w-stage'), wln = word.querySelector('.w-line'), full = word.querySelector('.w-full');
    if (wst && wln) wst.appendChild(wln);
    if (full) full.addEventListener('toggle', function () { word.classList.toggle('reading', full.open); });
  })();

  // رحلة التصميم: مشهد من خمسة فصول + الخطوات داخل المستطيل، يتغير كل ٣ ثوانٍ تلقائيًا
  (function () {
    var steps = document.querySelector('[data-bf] .ax-steps');
    if (!steps) return;
    var JX = document.querySelector('[data-jx]'), LI = [].slice.call(steps.children), STEP = 3000;
    var jxGo = function () {};
    if (JX) {
      JX.querySelector('.jx-stage').appendChild(steps);
      [].forEach.call(JX.querySelectorAll('.jx-map path'), function (e) { e.setAttribute('pathLength', '1'); });
      var jNo = JX.querySelector('.jx-no b'), jT = JX.querySelector('.jx-t'), jD = JX.querySelector('.jx-d'), jC = JX.querySelector('.jx-chip'), jt = null;
      jxGo = function (a) {
        JX.style.setProperty('--jx-ms', STEP + 'ms');
        var li = LI[a - 1]; if (!li) return;
        var first = JX.dataset.a === '0';
        JX.classList.add('swap'); clearTimeout(jt);
        jt = setTimeout(function () {
          jNo.textContent = two(a); jT.textContent = li.querySelector('b').textContent; jD.textContent = li.querySelector('small').textContent;
          jC.textContent = li.dataset.chip || ''; jC.hidden = !li.dataset.chip; JX.classList.remove('swap');
        }, first ? 0 : 380);
        JX.dataset.a = a;
        JX.classList.remove('run'); void JX.offsetWidth; JX.classList.add('run');
      };
    }
    steps.classList.add('mg');
    LI.forEach(function (li, i) {
      var si = li.querySelector('.ax-si');
      [].forEach.call(si.querySelectorAll('svg *'), function (e) { if (/^(path|circle|rect|line|polyline)$/.test(e.tagName)) e.setAttribute('pathLength', '1'); });
      si.insertAdjacentHTML('afterbegin', '<svg class="mg-ring" viewBox="0 0 70 70" aria-hidden="true"><circle cx="35" cy="35" r="34" pathLength="1"/></svg>');
      if (i === LI.length - 1) si.insertAdjacentHTML('beforeend', '<span class="mg-build" aria-hidden="true"><span class="p1"></span><span class="p2"></span><span class="p3"></span><span class="p4"></span></span>');
    });
    var track = document.createElement('span'); track.className = 'ax-track'; track.setAttribute('aria-hidden', 'true');
    var spark = document.createElement('span'); spark.className = 'ax-spark'; spark.setAttribute('aria-hidden', 'true');
    steps.appendChild(track); steps.appendChild(spark);
    var cur = -1, tm = null, seen = false, vis = false;
    function centers() {
      // من التخطيط لا من الشاشة، حتى لا تؤثر حركة الظهور في المواضع
      return LI.map(function (li) { var si = li.querySelector('.ax-si'); return { x: li.offsetLeft + si.offsetLeft + si.offsetWidth / 2, y: li.offsetTop + si.offsetTop + si.offsetHeight / 2 }; });
    }
    function place(k, instant) {
      var C = centers(); if (!C.length) return;
      var a = C[0], b = C[Math.max(0, k)], vert = C.length > 1 && Math.abs(C[1].y - C[0].y) > 10;
      if (instant) { track.style.transition = spark.style.transition = 'none'; }
      if (vert) { track.style.cssText += ';left:' + (a.x - 1) + 'px;width:2px;top:' + a.y + 'px;height:' + Math.max(0, b.y - a.y) + 'px;--dir:180deg'; }
      else { track.style.cssText += ';top:' + (a.y - 1) + 'px;height:2px;right:' + (steps.clientWidth - a.x) + 'px;left:auto;width:' + Math.max(0, a.x - b.x) + 'px;--dir:270deg'; }
      spark.style.left = b.x + 'px'; spark.style.top = b.y + 'px'; spark.style.opacity = k < 0 ? 0 : 1;
      if (instant) { void track.offsetWidth; track.style.transition = spark.style.transition = ''; }
    }
    function plan() {
      clearTimeout(tm); if (!vis || reduce) return;
      tm = setTimeout(function () {
        if (cur >= LI.length - 1) { LI.forEach(function (li) { li.classList.remove('on', 'past', 'build'); }); place(0, true); go(0); } else go(cur + 1);
      }, STEP);
    }
    function go(k) {
      cur = k;
      LI.forEach(function (li, i) { li.classList.toggle('on', i === k); li.classList.toggle('past', i < k); li.classList.remove('build'); });
      place(k); jxGo(k + 1);
      if (k === LI.length - 1) {
        setTimeout(function () { if (cur === k) LI[k].classList.add('build'); }, 700);
        setTimeout(function () { if (cur === k) { steps.classList.remove('flash'); void steps.offsetWidth; steps.classList.add('flash'); } }, 1500);
      }
      plan();
    }
    LI.forEach(function (li, i) { li.style.cursor = 'pointer'; li.addEventListener('click', function () { go(i); }); });
    addEventListener('resize', function () { place(cur, true); });
    function start() { steps.classList.add('drawn'); place(0, true); spark.style.opacity = 0; setTimeout(function () { go(0); }, reduce ? 0 : 600); }
    if (reduce || !IO) { vis = true; start(); LI.forEach(function (li) { li.classList.add('past'); }); return; }
    new IntersectionObserver(function (es) { vis = es[0].isIntersecting; if (vis && !seen) { seen = true; start(); } else plan(); }, { threshold: .2 }).observe(JX || steps);
  })();
})();
