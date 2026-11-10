/* المقالات — هيدر «جدار الصور» (الكمبيوتر): صور المقالات فسيفساء تملأ الهيدر بلون الثيمة، وفي الوسط لوح زجاجي بالعنوان والبحث.
   صورة تُضيء كل ثانيتين ويظهر عنوانها، والوقوف بالفأرة يضيء أي صورة، والضغط يفتح المقال. البيانات من بطاقات المقالات نفسها. التنسيق: css/articles-hero-wall.css */
(function () {
  /* نسخة المراجعة: يعمل على الهاتف أيضًا */
  var art = document.querySelector('.mag-hero-art'); if (!art) return;
  var A = [].slice.call(document.querySelectorAll('article[data-cat]')).map(function (a) {
    var img = a.querySelector('img.gold-ph'), h = a.querySelector('h2, h3'), l = a.querySelector('.cover-link');
    return img && h ? { img: 'assets/img/aw-thumbs/' + img.getAttribute('src').split('/').pop().split('?')[0].replace(/\.(jpe?g|png)$/i, '.webp'), t: h.textContent.trim(), href: l ? l.getAttribute('href') : 'article.html' } : null;
  }).filter(Boolean);
  if (A.length < 4) return;
  var cells = [], shapes = ['w2', '', 'h2', '', '', 'w2', '', '', '', '', '', ''];
  for (var i = 0; i < 12; i++) cells.push(A[i % A.length]);
  art.innerHTML = '<div class="aw-grid">' + cells.map(function (a, i) {
    return '<a class="aw-tile ' + shapes[i] + '" href="' + a.href + '" tabindex="-1"><img src="' + a.img + '" alt="" loading="' + (i < 6 ? 'eager' : 'lazy') + '"><b>' + a.t + '</b></a>';
  }).join('') + '</div>';
  art.classList.add('aw-on');
  var tiles = art.querySelectorAll('.aw-tile'), order = [1, 5, 3, 7, 4, 8, 6, 2], k = 0;
  if (!matchMedia('(prefers-reduced-motion: reduce)').matches) setInterval(function () {
    if (art.matches(':hover')) return;
    [].forEach.call(tiles, function (t) { t.classList.remove('lit'); }); tiles[order[k++ % order.length]].classList.add('lit');
  }, 2000);
})();
