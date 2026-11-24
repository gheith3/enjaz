// تصاميم ٢٠٨٠ (معتمد): صفحة المنجز لكل المنجزين (النموذج: د. جوخة الحارثية). التنسيق في css/profile-2080.css.
// يعمل فقط في صفحة المنجز، بعد أن تبني السكربتات الأخرى أقسامها (الإنجازات، المسيرة، المكتبة المرئية).
(function () {
  if (!document.querySelector('.profile-hero')) return;
  var reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  var digits = function (n) { return String(n).replace(/\d/g, function (d) { return '٠١٢٣٤٥٦٧٨٩'[d]; }); };

  /* ---------- ٢) الإنجازات: «إنجازاتي الدولية» ثم خط ثم «إنجازاتي المحلية» ---------- */
  function groupAchievements() {
    var aj = document.querySelector('[data-aj]');
    var grid = aj && aj.querySelector('.ab-grid');
    if (!grid || aj.classList.contains('x-grouped')) return !!grid;
    var data = {};
    try { (JSON.parse(document.getElementById('aj-data').textContent).items || []).forEach(function (it) { data[it.slug] = it; }); } catch (e) { return true; }

    var tiles = [].slice.call(grid.querySelectorAll('.ab-t'));
    var groups = [
      { key: 'international', title: 'إنجازاتي الدولية', cls: 'x-grp-intl', star: '✦ الإنجاز الأبرز', tiles: [] },
      { key: 'local', title: 'إنجازاتي المحلية', cls: 'x-grp-local', star: '✦ أبرز إنجاز محلي', tiles: [] }
    ];
    tiles.forEach(function (t) {
      var it = data[t.dataset.tile] || {};
      (it.scope === 'local' ? groups[1] : groups[0]).tiles.push(t);
    });

    var frag = document.createDocumentFragment();
    groups.forEach(function (g) {
      if (!g.tiles.length) return;
      // الصورة الكبيرة: الإنجاز المميز إن وُجد، وإلا الأحدث
      g.tiles.sort(function (a, b) {
        var A = data[a.dataset.tile] || {}, B = data[b.dataset.tile] || {};
        return (B.featured || 0) - (A.featured || 0) || (B.iso > A.iso ? 1 : -1);
      });
      var wrap = document.createElement('div');
      wrap.className = 'x-grp ' + g.cls;
      var its = g.tiles.map(function (t) { return data[t.dataset.tile] || {}; });
      var years = its.map(function (it) { return parseInt(it.iso, 10); }).filter(Boolean);
      var span = years.length ? digits(Math.min.apply(null, years)) + (Math.min.apply(null, years) !== Math.max.apply(null, years) ? ' – ' + digits(Math.max.apply(null, years)) : '') : '';
      var places = its.map(function (it) { return it.country; }).filter(function (c, k, a) { return c && a.indexOf(c) === k; });
      var intl = g.key === 'international';
      // عنوان بسيط: «إنجازاتي» ثم الكلمة المميزة بخط ذهبي يُرسم تحتها كضربة قلم، والعدد بجانبها، وتحته الأماكن والسنوات
      wrap.innerHTML = '<div class="x-band ' + (intl ? 'x-band-intl' : 'x-band-local') + '">'
        + '<h3><span class="x-w1">إنجازاتي</span> <span class="x-w2">' + (intl ? 'الدولية' : 'المحلية')
        + '<svg class="x-stroke" viewBox="0 0 200 20" preserveAspectRatio="none" aria-hidden="true"><path d="M3 13 C 45 4, 95 4, 140 9 S 185 15, 197 7"/></svg></span>'
        + '<sup class="x-band-num">' + digits(g.tiles.length) + '</sup></h3>'
        + '<p>' + (intl ? (places.length ? '<span class="x-band-places">' + places.map(function (p) { return '<i>' + p + '</i>'; }).join('') + '</span>' : '')
          : '<span class="x-band-places"><i>داخل سلطنة عُمان</i></span>')
        + (span ? '<span class="x-band-years">' + span + '</span>' : '') + '</p></div>';
      var gg = document.createElement('div');
      gg.className = 'ab-grid ab-grid-balanced';
      var small = g.tiles.length - 1;
      var cols = Math.max(1, Math.min(4, Math.ceil(small / 2)));   // الصغيرة في سطرين بجانب الكبيرة
      gg.style.setProperty('--x-cols', cols);
      g.tiles.forEach(function (t, i) {
        t.classList.remove('xl', 'sm', 'x-wide');
        t.classList.add(i === 0 ? 'xl' : 'sm');
        t.style.removeProperty('--x-span');
        var star = t.querySelector('.ab-star');
        if (star && i === 0) star.textContent = g.star;
        gg.appendChild(t);
      });
      // إن بقي فراغ في السطر الثاني تمتد آخر بطاقة لتملأه
      var rem = small % cols;
      if (small > 0 && rem) {
        var last = g.tiles[g.tiles.length - 1];
        last.classList.add('x-wide');
        last.style.setProperty('--x-span', cols - rem + 1);
      }
      if (small > 0 && small <= cols) {   // سطر واحد فقط: البطاقات الصغيرة تأخذ الارتفاع كاملًا
        g.tiles.slice(1).forEach(function (t) { t.style.gridRow = 'span 2'; });
      }
      wrap.appendChild(gg);
      frag.appendChild(wrap);
    });
    grid.replaceWith(frag);
    aj.classList.add('x-grouped');
    if (!reduce) {
      var bio = new IntersectionObserver(function (en) {
        en.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('x-in'); bio.unobserve(e.target); } });
      }, { threshold: .5 });
      [].forEach.call(aj.querySelectorAll('.x-band'), function (b) { bio.observe(b); });
    }

    // أزرار «الكل / دولي / محلي» تعرض القسم المطلوب بدل إعادة بناء الشبكة
    aj.addEventListener('click', function (ev) {
      var btn = ev.target.closest('.ab-seg [data-scope]');
      if (!btn) return;
      ev.stopPropagation(); ev.preventDefault();
      var scope = btn.dataset.scope;
      [].forEach.call(aj.querySelectorAll('.ab-seg [data-scope]'), function (b) {
        var on = b === btn; b.classList.toggle('on', on); b.setAttribute('aria-selected', on);
      });
      var intl = aj.querySelector('.x-grp-intl'), local = aj.querySelector('.x-grp-local');
      if (intl) intl.hidden = scope === 'local';
      if (local) local.hidden = scope === 'international';
      if (local) local.classList.toggle('x-solo', scope === 'local');
    }, true);
    return true;
  }

  /* ---------- ٤) «مسيرتي»: دخول القسم وصورة المنجز ---------- */
  function journey() {
    var sec = document.getElementById('journey');
    if (!sec || reduce) return;
    [].forEach.call(sec.querySelectorAll('.jp-grid > *'), function (c, i) { c.style.setProperty('--x-i', i); });
    sec.classList.add('x-armed');
    new IntersectionObserver(function (en, ob) {
      if (!en[0].isIntersecting) return;
      ob.disconnect();
      requestAnimationFrame(function () {
        sec.classList.add('x-in');
        setTimeout(function () { sec.classList.add('x-done'); }, 2600);
      });
    }, { threshold: .2 }).observe(sec);
  }

  /* ---------- ٧) عناوين الأقسام: تظهر حين يصل إليها الزائر ---------- */
  function titles() {
    var all = document.querySelectorAll('.sec-title');
    [].forEach.call(all, function (t) { t.classList.add('x-sec'); });
    if (reduce) return;
    var io = new IntersectionObserver(function (en) {
      en.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('x-in'); io.unobserve(e.target); } });
    }, { threshold: .6 });
    [].forEach.call(all, function (t) { io.observe(t); });
  }

  /* ---------- ٦) أيقونة الفيديو في سطر شارة «مكتبة الفيديو» ---------- */
  function videoRing() {
    var cv = document.querySelector('.ml-cv.vd');
    var kick = cv && cv.querySelector('.kick'), ring = cv && cv.querySelector('.playring');
    if (!kick || !ring) return;
    function place() {
      var c = cv.getBoundingClientRect(), k = kick.getBoundingClientRect();
      cv.style.setProperty('--x-ring-top', Math.round(k.top - c.top + k.height / 2 - ring.offsetHeight / 2) + 'px');
    }
    place();
    addEventListener('resize', place);
    if (document.fonts) document.fonts.ready.then(place);
  }

  /* ---------- ٨) «منجزون في نفس المجال»: ذرات ذهبية تتطاير من الأسفل ---------- */
  function dust() {
    var sec = document.querySelector('[data-related]');
    sec = sec && sec.closest('section');
    if (!sec || reduce) return;
    var box = document.createElement('div');
    box.className = 'x-dust'; box.setAttribute('aria-hidden', 'true');
    var r = function (a, b) { return a + Math.random() * (b - a); }, html = '';
    for (var i = 0; i < 60; i++) {
      html += '<i style="--x:' + r(1, 99).toFixed(1) + '%;--s:' + r(2, 4.5).toFixed(1) + 'px;--d:' + r(1.8, 3.2).toFixed(2)
        + 's;--dl:-' + r(0, 3.2).toFixed(2) + 's;--o:' + r(.45, .9).toFixed(2) + ';--dx:' + Math.round(r(-10, 10)) + 'px;--c:' + (i % 4 ? '#d7b266' : '#0c9aa5') + '"></i>';
    }
    box.innerHTML = html;
    sec.appendChild(box);
  }

  /* ---------- ١٨) بطاقات «منجزون في نفس المجال» بنفس بطاقة صفحة المنجزين ---------- */
  function personCards() {
    [].forEach.call(document.querySelectorAll('[data-related] .rel-card'), function (card) {
      if (card.classList.contains('x-pc')) return;
      var img = card.querySelector('img'), name = card.querySelector('b'), role = card.querySelector('small');
      var body = document.createElement('span');
      body.className = 'x-pc-body';
      if (name) body.appendChild(name);
      if (role) body.appendChild(role);
      card.textContent = '';
      if (img) card.appendChild(img);
      var shade = document.createElement('span'); shade.className = 'x-pc-shade';
      card.appendChild(shade);
      card.appendChild(body);
      card.classList.add('x-pc');
    });
  }

  /* ---------- ١٧) «منجزون في نفس المجال»: البطاقة في الوسط أكبر وأوضح ---------- */
  function stage() {
    var rel = document.querySelector('[data-related]');
    if (!rel || reduce) return;
    var cards = rel.querySelectorAll('.rel-card'), visible = false;
    new IntersectionObserver(function (en) { visible = en[0].isIntersecting; }).observe(rel);
    (function tick() {
      if (visible) {
        var b = rel.getBoundingClientRect(), mid = b.left + b.width / 2, half = b.width / 2, best = null, bestD = 1e9;
        for (var i = 0; i < cards.length; i++) {
          var c = cards[i], r = c.getBoundingClientRect(), d = Math.abs(r.left + r.width / 2 - mid) / half;
          if (d > 1.3) continue;
          var k = Math.min(1, d);
          c.style.setProperty('--x-z', (1.07 - .17 * k * k).toFixed(3));
          c.style.setProperty('--x-op', (1 - .45 * k * k).toFixed(3));
          if (d < bestD) { bestD = d; best = c; }
        }
        for (var j = 0; j < cards.length; j++) cards[j].classList.toggle('x-center', cards[j] === best);
      }
      requestAnimationFrame(tick);
    })();
  }

  // site.js يبني الأقسام بعد التحميل: ننتظرها ثم نطبق التعديلات
  var tries = 0;
  (function wait() {
    var ready = document.querySelector('[data-aj] .ab-grid') && document.querySelector('#journey .jp');
    if (!ready && tries++ < 60) { setTimeout(wait, 100); return; }
    groupAchievements();
    journey();
    titles();
    videoRing();
    personCards();
    dust();
    stage();
  })();
})();
