// Ported from design/js/uni-library.js (data: window.INJAZ_LIBRARY from the server).
/* «ذاكرة الجامعة»: المكتبة المرئية في صفحة الجامعة (university.html?org=...). البيانات من library في js/universities-data.js.
   قسمان مختلفان عن رواق الاتحادات:
   1) ألبوم الصور: صور مثبّتة بشريط لاصق كألبوم ذكريات، وفواصل فصول (التأسيس، الافتتاح، مراسيم وصحافة)، وتفتح كصفحة ألبوم.
   2) صالة العرض: شاشة كبيرة، وتحتها لقطات مصغّرة للاختيار منها؛ اختيار لقطة يعرضها على الشاشة، والتشغيل في مكانه. */
(function () {
  var sec = document.querySelector('[data-u-lib]');
  if (!sec) return;
  // Library data from the database (gallery photos/documents, chapters, videos), rendered by the server.
  var L = window.INJAZ_LIBRARY;
  if (!L || !((L.photos || []).length + (L.videos || []).length)) { sec.hidden = true; return; }
  var esc = function (s) { var d = document.createElement('div'); d.textContent = s == null ? '' : s; return d.innerHTML; };
  var P = L.photos || [], V = L.videos || [];
  sec.querySelector('[data-lib-sum]').textContent = [P.length && P.length + (P.length <= 10 ? ' صور ووثائق' : ' صورة ووثيقة'), V.length && V.length + (V.length <= 10 ? ' مقاطع' : ' مقطعًا')].filter(Boolean).join(' · ');

  // ================= ألبوم الصور =================
  var album = sec.querySelector('[data-lib-album]');
  if (!P.length) album.hidden = true;
  else {
    var chapters = [['', 'كل الألبوم']].concat((L.chapters || []).filter(function (c) { return P.some(function (p) { return p.chapter === c[0]; }); }));
    var tabs = album.querySelector('[data-lib-tabs]'), wall = album.querySelector('[data-lib-wall]'), cur = '', shown = [];
    var TILT = [-2.2, 1.6, -1.2, 2, -1.6, 1.1, -2.4, 1.4];
    var draw = function () {
      tabs.innerHTML = chapters.map(function (c) {
        var n = P.filter(function (p) { return !c[0] || p.chapter === c[0]; }).length;
        return '<button type="button" role="tab" data-c="' + c[0] + '" aria-selected="' + (cur === c[0]) + '">' + esc(c[1]) + '<b>' + n + '</b></button>';
      }).join('');
      shown = P.filter(function (p) { return !cur || p.chapter === cur; });
      wall.innerHTML = shown.map(function (p, i) {
        return '<button type="button" class="lp' + (p.doc ? ' is-doc' : '') + '" data-i="' + i + '" style="--t:' + TILT[i % TILT.length] + 'deg">' +
          '<span class="lp-img"><img src="' + esc(p.src) + '" alt="" loading="lazy"></span>' +
          (p.year ? '<span class="lp-seal">' + esc(p.year) + '</span>' : '') +
          '<span class="lp-cap">' + esc(p.title) + '</span></button>';
      }).join('');
    };
    tabs.addEventListener('click', function (e) { var b = e.target.closest('[data-c]'); if (b) { cur = b.dataset.c; draw(); } });
    wall.addEventListener('click', function (e) { var b = e.target.closest('[data-i]'); if (b) openPage(+b.dataset.i); });
    draw();

    // صفحة الألبوم المكبّرة
    var pg = document.createElement('div');
    pg.className = 'lpg'; pg.hidden = true; pg.setAttribute('role', 'dialog'); pg.setAttribute('aria-modal', 'true'); pg.setAttribute('aria-label', 'ألبوم الصور');
    pg.innerHTML = '<div class="lpg-book"><button type="button" class="lpg-x" data-x aria-label="إغلاق">✕</button>' +
      '<div class="lpg-photo" data-ph></div><div class="lpg-side"><span class="lpg-n" data-n></span><b data-t></b><span class="lpg-note" data-note></span>' +
      '<small class="lpg-cr" data-cr></small><div class="lpg-nav"><button type="button" data-step="-1" aria-label="الصفحة السابقة">→ السابقة</button><button type="button" data-step="1" aria-label="الصفحة التالية">التالية ←</button></div></div></div>';
    document.body.appendChild(pg);
    var at = 0, last = null;
    var show = function (i) {
      at = (i + shown.length) % shown.length; var p = shown[at];
      pg.querySelector('[data-ph]').innerHTML = '<img src="' + esc(p.src) + '" alt="' + esc(p.title) + '">';
      pg.querySelector('[data-n]').textContent = (p.year ? p.year + ' · ' : '') + 'صفحة ' + (at + 1) + ' من ' + shown.length;
      pg.querySelector('[data-t]').textContent = p.title;
      pg.querySelector('[data-note]').textContent = p.note || '';
      var cr = p.credit || L.credit;
      pg.querySelector('[data-cr]').innerHTML = cr ? 'المصدر: <a href="' + esc(cr[1]) + '" target="_blank" rel="noopener">' + esc(cr[0]) + '</a>' : '';
      pg.querySelector('.lpg-book').classList.remove('flip'); void pg.offsetWidth; pg.querySelector('.lpg-book').classList.add('flip');
    };
    var openPage = function (i) { last = document.activeElement; show(i); pg.hidden = false; document.documentElement.classList.add('lib-lock'); pg.querySelector('[data-x]').focus(); };
    var closePage = function () { pg.hidden = true; document.documentElement.classList.remove('lib-lock'); if (last) last.focus(); };
    pg.addEventListener('click', function (e) {
      if (e.target === pg || e.target.closest('[data-x]')) closePage();
      var s = e.target.closest('[data-step]'); if (s) show(at + +s.dataset.step);
    });
    document.addEventListener('keydown', function (e) {
      if (pg.hidden) return;
      if (e.key === 'Escape') closePage(); else if (e.key === 'ArrowLeft') show(at + 1); else if (e.key === 'ArrowRight') show(at - 1);
    });
    var x0 = null;
    pg.addEventListener('touchstart', function (e) { x0 = e.touches[0].clientX; }, { passive: true });
    pg.addEventListener('touchend', function (e) { if (x0 == null) return; var dx = e.changedTouches[0].clientX - x0; if (Math.abs(dx) > 50) show(at + (dx > 0 ? 1 : -1)); x0 = null; });
  }

  // ================= صالة العرض =================
  var hall = sec.querySelector('[data-lib-hall]');
  if (!V.length) { hall.hidden = true; return; }
  var screen = hall.querySelector('[data-scr]'), info = hall.querySelector('[data-scr-info]'), strip = hall.querySelector('[data-strip]');
  var thumb = function (v) { return 'https://i.ytimg.com/vi/' + v.yt + '/hqdefault.jpg'; };
  // لقطات مصغّرة للاختيار: صورة + مدة + سنة + عنوان
  strip.innerHTML = V.map(function (v, i) {
    return '<li><button type="button" class="vt" data-v="' + i + '" aria-label="' + esc(v.title + ' – ' + v.year) + '"><span class="vt-img"><img src="' + thumb(v) + '" alt="" loading="lazy">' +
      '<span class="vt-play" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M8 5.5v13l11-6.5z"/></svg></span><em>' + esc(v.dur || '') + '</em><i class="vt-now">يُعرض الآن</i></span>' +
      '<span class="vt-y">' + esc(v.year) + '</span><span class="vt-t">' + esc(v.title) + '</span></button></li>';
  }).join('');
  var now = 0;
  var cue = function (i, play) {
    now = i; var v = V[i];
    screen.innerHTML = play
      ? '<iframe src="https://www.youtube-nocookie.com/embed/' + esc(v.yt) + '?autoplay=1&rel=0" title="' + esc(v.title) + '" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe>'
      : '<button type="button" class="scr-poster" data-play aria-label="تشغيل: ' + esc(v.title) + '"><img src="' + thumb(v) + '" alt=""><span class="scr-play"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5.5v13l11-6.5z"/></svg></span>' +
        '<span class="scr-year">' + esc(v.year) + '</span></button>';
    info.innerHTML = '<span class="si-k">' + (v.official ? '<b>قناة الجامعة الرسمية</b>' : '') + esc(v.year) + (v.dur ? ' · ' + esc(v.dur) : '') + '</span>' +
      '<b class="si-t">' + esc(v.title) + '</b>' + (v.note ? '<small>' + esc(v.note) + '</small>' : '') +
      '<a href="https://www.youtube.com/watch?v=' + esc(v.yt) + '" target="_blank" rel="noopener">على يوتيوب ↗</a>';
    [].forEach.call(strip.querySelectorAll('.vt'), function (b) { b.setAttribute('aria-pressed', String(+b.dataset.v === i)); });
  };
  screen.addEventListener('click', function (e) { if (e.target.closest('[data-play]')) cue(now, true); });
  strip.addEventListener('click', function (e) {
    var b = e.target.closest('[data-v]'); if (!b) return;
    cue(+b.dataset.v, true);
    var r = hall.getBoundingClientRect(); if (r.top < 0) hall.scrollIntoView({ behavior: 'smooth', block: 'start' });
  });
  cue(0, false);
})();
