/* صفحة المنجز — «المكتبة المرئية» (تصاميم ٢٠٣٠): رموز متحركة على الغلافين، ونافذة فيها شبكة الصور أو المقاطع ثم عارض بالتنقل و«رجوع». */
(function () {
  var sec = document.querySelector('[data-ml]');
  if (!sec) return;
  var data; try { data = JSON.parse(sec.getAttribute('data-media')); } catch (e) { return; }
  var Q = function (s, r) { return (r || sec).querySelector(s); };
  var QA = function (s, r) { return Array.prototype.slice.call((r || sec).querySelectorAll(s)); };
  var AR = '٠١٢٣٤٥٦٧٨٩', ar = function (n) { return String(n).replace(/\d/g, function (d) { return AR[d]; }); };
  var esc = function (t) { return String(t == null ? '' : t).replace(/[&<>"]/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]; }); };
  var RM = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var CAM = '<svg viewBox="0 0 24 24"><path d="M3 8.5A2.5 2.5 0 0 1 5.5 6H8l1.5-2h5L16 6h2.5A2.5 2.5 0 0 1 21 8.5v9a2.5 2.5 0 0 1-2.5 2.5h-13A2.5 2.5 0 0 1 3 17.5z"/><circle cx="12" cy="13" r="4"/></svg>';
  var PLAY = '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9.5"/><path d="M10 8.5v7l6-3.5z" fill="currentColor"/></svg>';
  var L = { photos: sec.dataset.lPhotos || 'الصور', videos: sec.dataset.lVideos || 'الفيديو', back: 'رجوع', none: 'لا توجد عناصر بعد.' };

  // الرموز الطافية (تُولَّد هنا كي لا تثقل القالب)
  if (!RM) {
    var rnd = function (a, b) { return a + Math.random() * (b - a); };
    QA('.ml-fx').forEach(function (fx) {
      var n = fx.classList.contains('cams') ? 14 : 11, h = '';
      for (var i = 0; i < n; i++) h += '<i style="--x:' + rnd(3, 97).toFixed(1) + '%;--s:' + rnd(12, 24).toFixed(0) + 'px;--d:' + rnd(9, 16).toFixed(1) + 's;--dl:-' + rnd(0, 16).toFixed(1) + 's;--o:' + rnd(.4, .75).toFixed(2) + ';--sw:' + rnd(-18, 18).toFixed(0) + 'px"></i>';
      fx.innerHTML = h;
    });
  }

  var md = Q('[data-ml-md]'), body = Q('[data-ml-body]'), tabs = Q('[data-ml-tabs]'), mode = 'photo', viewing = -1, keep = 0, lastBtn = null;
  var list = function () { return mode === 'photo' ? data.photos : data.videos; };
  function drawTabs() {
    tabs.innerHTML = '<button type="button" data-m="photo" aria-pressed="' + (mode === 'photo') + '">' + CAM + L.photos + ' <span class="num">' + ar(data.photos.length) + '</span></button>' +
      '<button type="button" data-m="video" aria-pressed="' + (mode === 'video') + '">' + PLAY + L.videos + ' <span class="num">' + ar(data.videos.length) + '</span></button>';
    QA('[data-m]', tabs).forEach(function (b) { b.onclick = function () { mode = b.dataset.m; viewing = -1; keep = 0; draw(); }; });
  }
  function grid() {
    var items = list();
    if (!items.length) { body.innerHTML = '<p class="ml-empty">' + L.none + '</p>'; return; }
    body.innerHTML = '<div class="ml-grid' + (mode === 'video' ? ' v' : '') + '">' + items.map(function (m, i) {
      return '<button type="button" class="ml-gi" data-i="' + i + '"><span class="th"><img src="' + esc(m.img) + '" alt="" loading="lazy"' + (m.focal ? ' style="object-position:' + esc(m.focal) + '"' : '') +
        (m.fb ? ' data-fb="' + esc(m.fb) + '"' : '') + '>' +
        (mode === 'video' ? '<i class="pl"></i>' : '<span class="n num">' + ar(i + 1) + '</span>') + '</span>' +
        '<span class="cp">' + (mode === 'video' && m.y ? '<small class="num">' + esc(m.y) + '</small>' : '') + esc(m.t) + '</span></button>';
    }).join('') + '</div>';
    QA('[data-i]', body).forEach(function (b) { b.onclick = function () { keep = body.scrollTop; viewing = +b.dataset.i; draw(); }; });
    body.scrollTop = keep;
  }
  function viewer() {
    var items = list(), m = items[viewing];
    body.innerHTML = '<div class="ml-vw"><div class="ml-vw-top"><button type="button" class="ml-back" data-back>→ ' + L.back + '</button><span class="c num">' + ar(viewing + 1) + ' / ' + ar(items.length) + '</span></div>' +
      '<div class="ml-stage">' + (mode === 'photo' ? '<img src="' + esc(m.full || m.img) + '" alt="' + esc(m.t) + '">'
        : '<div class="vid"><img src="' + esc(m.img) + '" alt=""' + (m.fb ? ' data-fb="' + esc(m.fb) + '"' : '') + '><button type="button" class="pb" aria-label="▶"></button></div>') +
      (items.length > 1 ? '<button type="button" class="ml-ar pv" data-pv aria-label="›">›</button><button type="button" class="ml-ar nx" data-nx aria-label="‹">‹</button>' : '') + '</div>' +
      '<div class="ml-cap">' + (mode === 'video' && m.y ? '<small class="num">' + esc(m.y) + '</small>' : '') + esc(m.t) + '</div></div>';
    Q('[data-back]', body).onclick = back;
    var pb = Q('.pb', body);
    if (pb) pb.onclick = function () {
      var v = Q('.vid', body);
      if (m.id) v.insertAdjacentHTML('beforeend', '<iframe src="https://www.youtube-nocookie.com/embed/' + encodeURIComponent(m.id) + '?autoplay=1&rel=0" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen title="' + esc(m.t) + '"></iframe>');
      else if (m.url) window.open(m.url, '_blank', 'noopener');
      pb.remove();
    };
    var pv = Q('[data-pv]', body), nx = Q('[data-nx]', body);
    if (pv) pv.onclick = function () { step(-1); };
    if (nx) nx.onclick = function () { step(1); };
  }
  // صور يوتيوب الكبيرة غير موجودة لبعض المقاطع (يعيد يوتيوب صورة رمادية ١٢٠×٩٠): تُستبدل بالمتوسطة
  function fixThumbs() {
    QA('img[data-fb]', body).forEach(function (im) {
      var swap = function () { if (im.dataset.fb && (!im.naturalWidth || im.naturalWidth <= 120)) { im.src = im.dataset.fb; im.removeAttribute('data-fb'); } };
      if (im.complete) swap(); else { im.addEventListener('load', swap, { once: true }); im.addEventListener('error', swap, { once: true }); }
    });
  }
  function draw() { drawTabs(); if (viewing < 0) grid(); else viewer(); fixThumbs(); }
  function step(d) { var items = list(); viewing = (viewing + d + items.length) % items.length; viewer(); fixThumbs(); }
  function back() { viewing = -1; draw(); }
  function open(m, btn) { mode = m; viewing = -1; keep = 0; lastBtn = btn; md.hidden = false; document.documentElement.classList.add('ml-lock'); draw(); var x = Q('[data-ml-x]'); if (x) x.focus(); }
  function close() { md.hidden = true; body.innerHTML = ''; document.documentElement.classList.remove('ml-lock'); if (lastBtn) lastBtn.focus(); }
  QA('[data-ml-open]').forEach(function (b) { b.addEventListener('click', function () { open(b.dataset.mlOpen, b); }); });
  Q('[data-ml-x]').addEventListener('click', close);
  md.addEventListener('click', function (e) { if (e.target === md) close(); });
  document.addEventListener('keydown', function (e) {
    if (md.hidden) return;
    if (e.key === 'Escape') { if (viewing >= 0) back(); else close(); }
    if (viewing >= 0 && e.key === 'ArrowLeft') step(1);
    if (viewing >= 0 && e.key === 'ArrowRight') step(-1);
  });
  var sx = null;
  md.addEventListener('touchstart', function (e) { sx = e.touches[0].clientX; }, { passive: true });
  md.addEventListener('touchend', function (e) { if (sx == null || viewing < 0) return; var dx = e.changedTouches[0].clientX - sx; if (Math.abs(dx) > 50) step(dx > 0 ? 1 : -1); sx = null; });
})();
