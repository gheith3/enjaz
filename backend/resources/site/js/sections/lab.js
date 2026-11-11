/* المختبر (science.html, lab-item.html): from design js/lab-showcase.js, science-page.js and lab-list.js, the
   behaviour only. The markup is rendered by the server.
   - Showcase: one invention in the spotlight, changes every 6 s (stops on hover / focus), numbered slides below.
   - Counters count up, the phone switch toggles inventions / publications, "copy citation".
   - Phone header: under "المختبر" the name of the section in view. */
(function () {
  var reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  var ar = function (n) { return String(n).replace(/\d/g, function (d) { return '٠١٢٣٤٥٦٧٨٩'[d]; }); };

  var show = document.querySelector('[data-lx-show]');
  if (show && show.querySelector('.lx-item')) {
    var pics = show.querySelectorAll('.lx-pics img'), items = show.querySelectorAll('.lx-item'), slides = show.querySelectorAll('.lx-slide');
    var n = items.length, cur = 0, timer = 0, DUR = 6000;
    var go = function (k) {
      cur = (k + n) % n;
      [pics, items, slides].forEach(function (list) { [].forEach.call(list, function (el, i) { el.classList.toggle('on', i === cur); }); });
      [].forEach.call(items, function (el, i) { el.setAttribute('aria-hidden', i === cur ? 'false' : 'true'); el.querySelector('.lx-go').tabIndex = i === cur ? 0 : -1; });
      [].forEach.call(slides, function (el, i) { el.setAttribute('aria-selected', i === cur ? 'true' : 'false'); });
      show.style.setProperty('--dur', DUR + 'ms');
      show.classList.remove('run'); void show.offsetWidth; if (!reduce && !show.matches(':hover')) show.classList.add('run');
    };
    var play = function () { clearInterval(timer); if (reduce) return; timer = setInterval(function () { go(cur + 1); }, DUR); show.classList.add('run'); };
    var stop = function () { clearInterval(timer); show.classList.remove('run'); };
    [].forEach.call(slides, function (b, i) { b.addEventListener('click', function () { go(i); }); });
    show.addEventListener('mouseenter', stop); show.addEventListener('mouseleave', function () { go(cur); play(); });
    show.addEventListener('focusin', stop);
    go(0); play();
  }

  if (document.querySelector('[data-lab-page]')) {
    document.querySelectorAll('[data-count]').forEach(function (el) {
      var to = +el.dataset.count;
      if (reduce) { el.textContent = ar(to); return; }
      var t0 = performance.now(), dur = 1400;
      (function step(t) {
        var p = Math.min(1, (t - t0) / dur), e = 1 - Math.pow(1 - p, 3);
        el.textContent = ar(Math.round(to * e));
        if (p < 1) requestAnimationFrame(step);
      })(t0);
    });

    var sw = document.querySelector('.lab-switch');
    if (sw) {
      var btns = sw.querySelectorAll('[role="tab"]');
      btns.forEach(function (b) {
        b.addEventListener('click', function () {
          btns.forEach(function (x) {
            var on = x === b;
            x.setAttribute('aria-selected', String(on)); x.tabIndex = on ? 0 : -1;
            document.getElementById(x.getAttribute('aria-controls')).hidden = !on;
          });
          sw.dataset.tab = b.dataset.tab;
        });
        b.addEventListener('keydown', function (e) {
          if (e.key === 'ArrowLeft' || e.key === 'ArrowRight') { var other = btns[0] === b ? btns[1] : btns[0]; other.click(); other.focus(); }
        });
      });
    }

    var sc = document.querySelector('.site-header .crumb');
    var secs = [[document.querySelector('.lx-show'), document.getElementById('lx-inv-h')], [document.querySelector('.lx-table'), document.getElementById('lx-pub-h')]];
    if (sc && secs[0][0] && 'IntersectionObserver' in window) {
      var title = sc.textContent.trim();
      sc.innerHTML = '<span class="crumb-title"></span><small class="crumb-sub"></small>'; sc.classList.add('has-sub');
      sc.querySelector('.crumb-title').textContent = title;
      var subEl = sc.querySelector('.crumb-sub'), first = secs[0][1] ? secs[0][1].textContent : '';
      var upd = function () {
        var cur = '';
        secs.forEach(function (p) { if (p[0]) { var r = p[0].getBoundingClientRect(); if (r.top < innerHeight * .5 && r.bottom > 80) cur = p[1] ? p[1].textContent : ''; } });
        subEl.textContent = cur || first;
      };
      var io = new IntersectionObserver(upd, { threshold: [0, .25, .5, .75, 1] });
      secs.forEach(function (p) { if (p[0]) io.observe(p[0]); });
      addEventListener('scroll', upd, { passive: true });
    }
  }

  var cb = document.querySelector('.li-cite');
  if (cb) cb.addEventListener('click', function () {
    (navigator.clipboard ? navigator.clipboard.writeText(cb.dataset.cite) : Promise.reject()).then(function () { cb.textContent = cb.dataset.done; }, function () { cb.textContent = cb.dataset.cite; });
  });
})();
