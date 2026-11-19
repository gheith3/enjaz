// Leader's word «نافذة المقام» (components/site/organization/word-window, css leader-window): entrance, the emblem
// swinging on the arch frame like a pendulum (it becomes the reading marker when the word is open), reading focus,
// signature, a light tilt with the pointer and golden dust.
(function () {
  var reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;

  function init(sec) {
    // A photo taller than the arch would lose the head under its top: it is narrowed until it fits (80% of the glass, the head clear of the arch).
    var glass = sec.querySelector('.ww-glass'), photo = sec.querySelector('.ww-photo'), pic = photo && photo.querySelector('img');
    var fitPhoto = function () {
      if (!glass || !pic || !pic.naturalWidth) return;
      photo.style.width = '';
      var room = glass.clientHeight * 0.8, h = photo.offsetWidth * pic.naturalHeight / pic.naturalWidth;
      if (h > room) photo.style.width = (room * pic.naturalWidth / pic.naturalHeight) + 'px';
    };
    if (pic) { if (pic.complete) fitPhoto(); else pic.addEventListener('load', fitPhoto); addEventListener('resize', fitPhoto); }
    // Quote words appear one by one (words inside <mark> stay inside it).
    var quote = sec.querySelector('[data-ww-words]');
    var n = 0;
    var wrap = function (node) {
      Array.prototype.slice.call(node.childNodes).forEach(function (c) {
        if (c.nodeType === 3) {
          var frag = document.createDocumentFragment();
          c.textContent.split(/(\s+)/).forEach(function (t) {
            if (!t) return;
            if (/^\s+$/.test(t)) { frag.appendChild(document.createTextNode(t)); return; }
            var s = document.createElement('span'); s.className = 'ww-q'; s.style.setProperty('--i', n++); s.textContent = t; frag.appendChild(s);
          });
          c.parentNode.replaceChild(frag, c);
        } else if (c.nodeType === 1) { wrap(c); }
      });
    };
    if (quote) wrap(quote);

    // Emblem on the frame: pendulum from the apex, reading marker when open.
    var tilt = sec.querySelector('.ww-tilt'), outline = sec.querySelector('.ww-line-out');
    var keyEl = sec.querySelector('.ww-key'), trails = Array.prototype.slice.call(sec.querySelectorAll('.ww-trail'));
    var total = outline.getTotalLength();
    var PERIOD = 15000, AMP = 0.46, RAMP = 5000;
    var swingAt = Infinity, readP = 0, shown = 0.5, visible = true, prev = performance.now();
    var swingFrom = function (t) { swingAt = t; };
    var placeKey = function (s) {
      if (!keyEl) return;
      var pt = outline.getPointAtLength(Math.min(1, Math.max(0, s)) * total), k = keyEl.offsetWidth / 2;
      keyEl.style.setProperty('--kx', (pt.x / 440 * tilt.offsetWidth - k).toFixed(1) + 'px');
      keyEl.style.setProperty('--ky', (pt.y / 600 * tilt.offsetHeight - k).toFixed(1) + 'px');
    };
    new IntersectionObserver(function (es) { visible = es[0].isIntersecting; }).observe(sec);
    var loop = function (now) {
      requestAnimationFrame(loop);
      if (!visible) { prev = now; return; }
      var dt = Math.min(64, Math.max(1, now - prev)); prev = now;
      var reading = sec.classList.contains('open'), target = 0.5;
      if (reading) target = 0.03 + 0.94 * readP;
      else if (!reduce && now > swingAt) {
        var t = now - swingAt, r = Math.min(1, t / RAMP), e = r * r * (3 - 2 * r);
        target = 0.5 - AMP * e * Math.sin(2 * Math.PI * t / PERIOD);
      }
      var before = shown;
      shown += (target - shown) * (1 - Math.pow(0.88, dt / 16.7));
      placeKey(shown);
      var v = (shown - before) / dt, glow = reading || reduce ? 0 : Math.min(1, Math.abs(v) / 1.9e-4);
      trails.forEach(function (tr, i) {
        tr.style.opacity = (glow * [1, 0.5, 0.2][i]).toFixed(3);
        tr.style.strokeDashoffset = (-(v < 0 ? shown + i * 0.055 : shown - (i + 1) * 0.055)).toFixed(4);
      });
    };
    placeKey(0.5);
    requestAnimationFrame(loop);
    addEventListener('resize', function () { placeKey(shown); });

    // Entrance
    var start = function () { sec.classList.add('is-in'); dust(); swingFrom(performance.now() + 2800); };
    if (reduce || !('IntersectionObserver' in window)) sec.classList.add('is-in');
    else {
      var io = new IntersectionObserver(function (es) { if (es.some(function (x) { return x.isIntersecting; })) { io.disconnect(); start(); } }, { threshold: 0.28 });
      io.observe(sec);
    }

    // Open / close the word
    var cta = sec.querySelector('.ww-cta'), close = sec.querySelector('.ww-close'), full = sec.querySelector('.ww-full');
    var prog = sec.querySelector('.ww-progress'), paras = Array.prototype.slice.call(sec.querySelectorAll('.ww-p')), sign = sec.querySelector('.ww-sign');
    var setOpen = function (open) {
      sec.classList.toggle('open', open);
      cta.setAttribute('aria-expanded', String(open));
      if (open) { full.removeAttribute('inert'); } else { full.setAttribute('inert', ''); }
      full.setAttribute('aria-hidden', String(!open));
      if (open) { setTimeout(function () { if (paras[0]) paras[0].scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'center' }); }, 450); }
      else { sec.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'start' }); swingFrom(performance.now() + 1200); }
      onScroll();
    };
    cta.addEventListener('click', function () { setOpen(true); });
    close.addEventListener('click', function () { setOpen(false); });

    // Reading progress on the frame + the paragraph being read
    function onScroll() {
      if (!sec.classList.contains('open')) {
        readP = 0; prog.style.strokeDashoffset = 1;
        paras.forEach(function (p) { p.classList.remove('is-active', 'is-read'); });
        return;
      }
      var mid = innerHeight * 0.5, r = full.getBoundingClientRect();
      var p = Math.min(1, Math.max(0, (mid - r.top) / Math.max(1, r.height - innerHeight * 0.25)));
      readP = p; prog.style.strokeDashoffset = (1 - p).toFixed(4);
      var best = null, dist = Infinity;
      paras.forEach(function (el) {
        var b = el.getBoundingClientRect(), d = Math.abs((b.top + b.bottom) / 2 - mid);
        if (b.bottom > 0 && b.top < innerHeight && d < dist) { dist = d; best = el; }
      });
      var passed = true;
      paras.forEach(function (el) {
        if (el === best) passed = false;
        el.classList.toggle('is-active', el === best);
        el.classList.toggle('is-read', passed && el !== best);
      });
      if (sign && sign.getBoundingClientRect().top < innerHeight * 0.85) sign.classList.add('is-in');
    }
    addEventListener('scroll', onScroll, { passive: true });
    addEventListener('resize', onScroll);

    // Light tilt with the pointer (desktop)
    if (!reduce && matchMedia('(hover: hover) and (min-width: 1100px)').matches) {
      var img = sec.querySelector('.ww-photo img'), tx = 0, ty = 0, cx = 0, cy = 0, raf = 0;
      var clamp = function (v) { return Math.max(-1, Math.min(1, v)); };
      var step = function () {
        cx += (tx - cx) * 0.08; cy += (ty - cy) * 0.08;
        tilt.style.setProperty('--ry', (cx * 6).toFixed(2) + 'deg'); tilt.style.setProperty('--rx', (-cy * 4).toFixed(2) + 'deg');
        if (img) { img.style.setProperty('--px', (cx * -9).toFixed(1) + 'px'); img.style.setProperty('--py', (cy * -5).toFixed(1) + 'px'); }
        raf = (Math.abs(tx - cx) > 0.002 || Math.abs(ty - cy) > 0.002) ? requestAnimationFrame(step) : 0;
      };
      sec.addEventListener('pointermove', function (e) {
        var r = tilt.getBoundingClientRect();
        tx = clamp((e.clientX - (r.left + r.width / 2)) / (r.width * 1.4)); ty = clamp((e.clientY - (r.top + r.height / 2)) / (r.height * 1.2));
        if (!raf) raf = requestAnimationFrame(step);
      });
      sec.addEventListener('pointerleave', function () { tx = ty = 0; if (!raf) raf = requestAnimationFrame(step); });
    }

    // Golden dust
    var dustOn = false;
    function dust() {
      if (reduce || dustOn) return; dustOn = true;
      var c = sec.querySelector('.ww-dust'), ctx = c.getContext('2d'), w = 0, h = 0;
      var size = function () { var d = Math.min(2, devicePixelRatio || 1); w = c.clientWidth; h = c.clientHeight; c.width = w * d; c.height = h * d; ctx.setTransform(d, 0, 0, d, 0, 0); };
      size(); addEventListener('resize', size);
      var make = function (any) { return { x: Math.random() * w, y: any ? Math.random() * h : h + 8, r: 0.5 + Math.random() * 1.7, s: 0.12 + Math.random() * 0.42, a: Math.random() * 6.28, t: 0.004 + Math.random() * 0.012 }; };
      var ps = []; for (var i = 0; i < (innerWidth < 1100 ? 22 : 48); i++) ps.push(make(true));
      (function frame() {
        if (visible) {
          ctx.clearRect(0, 0, w, h);
          ps.forEach(function (p) {
            p.y -= p.s; p.a += p.t; p.x += Math.sin(p.a) * 0.22;
            if (p.y < -8) { var q = make(false); for (var k in q) p[k] = q[k]; }
            ctx.beginPath(); ctx.fillStyle = 'rgba(244,223,174,' + (0.18 + 0.5 * (0.5 + 0.5 * Math.sin(p.a * 3))).toFixed(3) + ')';
            ctx.arc(p.x, p.y, p.r, 0, 6.283); ctx.fill();
          });
        }
        requestAnimationFrame(frame);
      })();
    }
  }

  document.querySelectorAll('[data-word-window]').forEach(init);
})();
