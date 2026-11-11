/* مجلة إنجازات: from design js/articles-hero-wall.js and the inline scripts of article.html and writers.html.
   - Articles hero (desktop and phone): a mosaic of the articles' covers; one lights up every 2 s. Data: data-mag-wall.
   - Article: reading progress, share / copy link, and one read counted per visit.
   - Writers: tabs filter the cards; the card of #hash is marked. */
(function () {
  var art = document.querySelector('[data-mag-wall]');
  if (art) {
    var A = []; try { A = JSON.parse(art.dataset.magWall); } catch (e) {}
    if (A.length >= 4) {
      var esc = function (s) { var d = document.createElement('div'); d.textContent = s == null ? '' : s; return d.innerHTML; };
      var cells = [], shapes = ['w2', '', 'h2', '', '', 'w2', '', '', '', '', '', ''];
      for (var i = 0; i < 12; i++) cells.push(A[i % A.length]);
      art.innerHTML = '<div class="aw-grid">' + cells.map(function (a, i) {
        return '<a class="aw-tile ' + shapes[i] + '" href="' + esc(a.href) + '" tabindex="-1"><img src="' + esc(a.img) + '" alt="" loading="' + (i < 6 ? 'eager' : 'lazy') + '"><b>' + esc(a.t) + '</b></a>';
      }).join('') + '</div>';
      art.classList.add('aw-on');
      var tiles = art.querySelectorAll('.aw-tile'), order = [1, 5, 3, 7, 4, 8, 6, 2], k = 0;
      if (!matchMedia('(prefers-reduced-motion: reduce)').matches) setInterval(function () {
        if (art.matches(':hover')) return;
        [].forEach.call(tiles, function (t) { t.classList.remove('lit'); }); tiles[order[k++ % order.length]].classList.add('lit');
      }, 2000);
    }
  }

  var page = document.querySelector('[data-article-view]');
  if (page) {
    var bar = document.querySelector('[data-progress]'), body = document.querySelector('.ar-body');
    var upd = function () { var r = body.getBoundingClientRect(), h = r.height - innerHeight * .6; bar.style.transform = 'scaleX(' + Math.max(0, Math.min(1, -r.top / Math.max(1, h))) + ')'; };
    if (bar && body) { addEventListener('scroll', upd, { passive: true }); upd(); }
    var msg = document.querySelector('.ar-copied'), copyBtn = document.querySelector('[data-copy]');
    var copy = function () {
      (navigator.clipboard ? navigator.clipboard.writeText(location.href) : Promise.reject()).then(function () { msg.textContent = copyBtn.dataset.done; }, function () { msg.textContent = location.href; });
      setTimeout(function () { msg.textContent = ''; }, 2500);
    };
    if (copyBtn) copyBtn.addEventListener('click', copy);
    var shareBtn = document.querySelector('[data-share]');
    if (shareBtn) shareBtn.addEventListener('click', function () {
      if (navigator.share) navigator.share({ title: document.title, url: location.href }).catch(function () {}); else copy();
    });
    try {
      var key = 'injaz-read:' + page.dataset.articleView;
      if (!sessionStorage.getItem(key)) { sessionStorage.setItem(key, '1'); fetch(page.dataset.articleView, { method: 'POST', keepalive: true }).catch(function () {}); }
    } catch (e) {}
  }

  var wr = document.querySelector('[data-writers]');
  if (wr) {
    var cards = [].slice.call(wr.querySelectorAll('.wr-card')), tabs = [].slice.call(wr.querySelectorAll('.mag-tabs .chip'));
    tabs.forEach(function (t) { t.addEventListener('click', function () {
      tabs.forEach(function (x) { x.classList.toggle('active', x === t); x.setAttribute('aria-pressed', String(x === t)); });
      cards.forEach(function (c) { c.hidden = t.dataset.tab !== '*' && c.dataset.cat !== t.dataset.tab; });
    }); });
    var target = location.hash && document.getElementById(decodeURIComponent(location.hash.slice(1)));
    if (target && target.classList.contains('wr-card')) target.classList.add('is-target');
  }
})();
