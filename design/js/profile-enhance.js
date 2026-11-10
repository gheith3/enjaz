/* صفحة المنجز — تحسينات عامة تعمل لكل منجز من بطاقات إنجازاته:
   ١) الأرقام تُحسب من البطاقات (الوسام + البطاقة التعريفية)
   ٢) تصفية الكل / دولي / محلي (ومن صفوف الوسام)
   ٣) «مسيرتي»: قرص دوّار بالسنوات وبطاقة مؤطّرة من تواريخ البطاقات
   ٤) هيدر مصغّر بصورة المنجز عند التمرير
   ٥) معرض الصور، وإخفاء أي رابط فارغ
   يجب تحميله قبل profile-medal-stats.js و profile-idcard.js */
(function () {
  var $ = function (s, r) { return (r || document).querySelector(s); };
  var $$ = function (s, r) { return [].slice.call((r || document).querySelectorAll(s)); };
  var ar = function (v) { return String(v).replace(/\d/g, function (d) { return '٠١٢٣٤٥٦٧٨٩'[d]; }); };
  var reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
  var cards = $$('.ach[data-scope]');

  /* ---------- ١) الأرقام ---------- */
  var total = cards.length;
  var intl = cards.filter(function (c) { return c.dataset.scope === 'international'; }).length;
  var local = total - intl;
  var medal = $('[data-medal-stats]');
  if (medal && total) {
    medal.dataset.total = total; medal.dataset.intl = intl; medal.dataset.local = local;
    medal.setAttribute('aria-label', 'إحصاءات الإنجازات: ' + ar(total) + ' إجمالًا، ' + ar(intl) + ' دولية و' + ar(local) + ' محلية');
    var c = $('[data-ms-count]', medal); if (c) c.textContent = ar(total);
    $$('[data-ms-num]', medal).forEach(function (n) {
      var v = n.closest('[data-kind="intl"]') ? intl : local;
      n.dataset.msNum = v; n.textContent = ar(v);
    });
    $$('.ms-row', medal).forEach(function (row) {
      var v = row.dataset.kind === 'intl' ? intl : local;
      row.setAttribute('aria-label', ar(v) + (row.dataset.kind === 'intl' ? ' إنجازات دولية' : ' إنجازات محلية') + ' من أصل ' + ar(total) + ' — اضغط لعرضها');
    });
  }
  var json = $('#member-card-data');
  if (json && total) {
    try {
      var d = JSON.parse(json.textContent);
      d.stats = [{ value: total, label: 'إنجازات' }, { value: intl, label: 'دولي' }, { value: local, label: 'محلي' }];
      json.textContent = JSON.stringify(d);
    } catch (e) {}
  }

  /* ---------- ٢) التصفية ---------- */
  var chips = $$('[data-scope-filter]');
  var empty = $('[data-ach-empty]');
  function filter(scope) {
    var shown = 0;
    cards.forEach(function (card) {
      var ok = scope === 'all' || card.dataset.scope === scope;
      card.hidden = !ok; if (ok) shown++;
    });
    if (empty) empty.hidden = shown > 0;
    chips.forEach(function (ch) {
      var on = ch.dataset.scopeFilter === scope;
      ch.classList.toggle('active', on); ch.setAttribute('aria-pressed', String(on));
    });
  }
  chips.forEach(function (ch) { ch.addEventListener('click', function () { filter(ch.dataset.scopeFilter); }); });
  var achSection = $('#achievements');
  $$('.ms-row').forEach(function (row) {
    row.addEventListener('click', function () {
      filter(row.dataset.kind === 'intl' ? 'international' : 'local');
      if (achSection) achSection.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'start' });
    });
  });

  /* ---------- ٣) مسيرتي: كل المحطات ظاهرة على عمود ذهبي متعرّج، وضوء يتنقّل بينها ---------- */
  var journey = $('[data-journey]');
  if (journey && total) {
    var list = cards.slice().sort(function (a, b) { return a.dataset.date < b.dataset.date ? -1 : 1; });
    var months = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
    var N = list.length, k = -1;
    journey.style.setProperty('--n', N);
    journey.innerHTML = '<span class="zj-spine" aria-hidden="true"><i></i></span><span class="zj-bead" aria-hidden="true"></span>'
      + list.map(function (card, i) {
        var d = card.dataset.date.split('-'), intl = card.dataset.scope === 'international';
        return '<button type="button" class="zj-st ' + (i % 2 ? 'l' : 'r') + '" style="grid-row:' + (i + 1) + ' / span 2;--i:' + i + '" data-target="' + card.id + '">'
          + '<span class="zj-dot" aria-hidden="true"></span>'
          + '<span class="zj-y">' + ar(d[0]) + (d[1] ? '<small>' + months[+d[1] - 1] + '</small>' : '')
          + '<em class="' + (intl ? 'i' : 'lo') + '">' + (intl ? 'دولي' : 'محلي') + '</em></span>'
          + '<span class="zj-t">' + $('h3', card).textContent.trim() + '</span></button>';
      }).join('');
    var sts = $$('.zj-st', journey), bead = $('.zj-bead', journey), fill = $('.zj-spine i', journey);
    var set = function (i) {
      k = (i + N) % N;
      var st = sts[k], y = st.offsetTop + st.offsetHeight / 2;
      sts.forEach(function (s, j) { s.classList.toggle('on', j === k); s.classList.toggle('past', j < k); });
      bead.style.top = y + 'px';
      fill.style.height = y + 'px';
    };
    sts.forEach(function (s) {
      s.addEventListener('click', function () {
        var target = document.getElementById(s.dataset.target); if (!target) return;
        if (target.hidden) filter('all');
        stop(); set(sts.indexOf(s));
        target.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'center' });
        target.classList.remove('flash'); void target.offsetWidth; target.classList.add('flash');
      });
    });
    var timer = null;
    var stop = function () { clearInterval(timer); timer = null; };
    var play = function () { stop(); timer = setInterval(function () { set(k + 1); }, 2600); };
    if (reduce) { journey.classList.add('in'); set(N - 1); }
    else {
      // يبدأ عند ظهور القسم، ويتوقف الضوء عند الخروج منه
      var started = false, seen = false;
      var check = function () {
        var r = journey.getBoundingClientRect(), vh = window.innerHeight;
        var vis = r.top < vh * .8 && r.bottom > vh * .2;
        if (vis === seen) return; seen = vis;
        if (!vis) { stop(); return; }
        if (!started) { started = true; journey.classList.add('in'); setTimeout(function () { set(0); if (seen) play(); }, 300 + N * 90); }
        else if (k >= 0) play();
      };
      window.addEventListener('scroll', check, { passive: true });
      check(); setTimeout(check, 400);
    }
    window.addEventListener('resize', function () { if (k >= 0) set(k); });
  }

  // القدوم من صفحة إنجاز (profile.html#ach-...) يُبرز بطاقته
  if (location.hash.indexOf('#ach-') === 0) {
    var target = document.getElementById(location.hash.slice(1));
    if (target) setTimeout(function () {
      target.scrollIntoView({ block: 'center' });
      target.classList.add('flash');
    }, 350);
  }

  /* ---------- ٤) هيدر مصغّر ---------- */
  var header = $('.site-header'), hero = $('.profile-hero'), crumb = $('.crumb');
  var photo = $('.profile-portrait img');
  if (header && hero && crumb && photo && 'IntersectionObserver' in window) {
    var av = document.createElement('span');
    av.className = 'crumb-avatar'; av.setAttribute('aria-hidden', 'true');
    var avImg = document.createElement('img'); avImg.src = photo.getAttribute('data-avatar') || photo.getAttribute('src');   // صورة مصغّرة خاصة إن وُجدت avImg.alt = '';
    av.appendChild(avImg);
    crumb.parentNode.insertBefore(av, crumb);
    new IntersectionObserver(function (es) {
      header.classList.toggle('is-compact', !es[0].isIntersecting);
    }, { rootMargin: '-64px 0px 0px 0px' }).observe(hero);
  }

  /* ---------- ٥) إخفاء الروابط الفارغة ---------- */
  $$('.profile-social, .media3 a, [data-hide-empty]').forEach(function (a) {
    var h = (a.getAttribute('href') || '').trim();
    if (!h || h === '#') a.hidden = true;
  });

  /* ---------- معرض الصور ---------- */
  var gallery = $$('[data-gallery] a');
  if (gallery.length) {
    var box = document.createElement('dialog');
    box.className = 'pg-box';
    box.setAttribute('aria-label', 'معرض الصور');
    box.innerHTML = '<button type="button" class="pg-close" aria-label="إغلاق">×</button>'
      + '<button type="button" class="pg-nav pg-prev" aria-label="الصورة السابقة">›</button>'
      + '<figure><img alt=""><figcaption></figcaption></figure>'
      + '<button type="button" class="pg-nav pg-next" aria-label="الصورة التالية">‹</button>';
    document.body.appendChild(box);
    var img = $('img', box), cap = $('figcaption', box), at = 0;
    var show = function (i) {
      at = (i + gallery.length) % gallery.length;
      img.src = gallery[at].getAttribute('href');
      img.alt = cap.textContent = gallery[at].dataset.caption || '';
    };
    gallery.forEach(function (a, i) {
      a.addEventListener('click', function (e) {
        e.preventDefault(); show(i);
        if (box.showModal) box.showModal(); else box.setAttribute('open', '');
      });
    });
    $('.pg-close', box).addEventListener('click', function () { box.close(); });
    $('.pg-prev', box).addEventListener('click', function () { show(at - 1); });
    $('.pg-next', box).addEventListener('click', function () { show(at + 1); });
    box.addEventListener('click', function (e) { if (e.target === box) box.close(); });
    box.addEventListener('keydown', function (e) {
      if (e.key === 'ArrowLeft') show(at + 1);
      if (e.key === 'ArrowRight') show(at - 1);
    });
    var sx = null;
    box.addEventListener('touchstart', function (e) { sx = e.touches[0].clientX; }, { passive: true });
    box.addEventListener('touchend', function (e) {
      if (sx === null) return;
      var dx = e.changedTouches[0].clientX - sx; sx = null;
      if (Math.abs(dx) > 40) show(at + (dx < 0 ? -1 : 1));
    });
  }
})();
