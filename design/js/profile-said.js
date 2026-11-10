/* «قالوا عن…»: قولٌ واحد في كل مرة يتبدّل تلقائيًا، ونقاط للتنقّل */
(function () {
  var box = document.querySelector('.said'); if (!box) return;
  var cards = [].slice.call(box.querySelectorAll('.said-card')), N = cards.length; if (!N) return;
  var reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
  var dots = document.createElement('div'); dots.className = 'said-dots';
  cards.forEach(function (c, i) {
    var b = document.createElement('button'); b.type = 'button'; b.setAttribute('aria-label', 'القول ' + (i + 1));
    b.addEventListener('click', function () { show(i); play(); }); dots.appendChild(b);
  });
  box.appendChild(dots);
  var bs = [].slice.call(dots.children), k = 0, timer = null;
  var show = function (i) { k = (i + N) % N; cards.forEach(function (c, j) { c.classList.toggle('on', j === k); c.setAttribute('aria-hidden', j !== k); }); bs.forEach(function (b, j) { b.classList.toggle('on', j === k); }); };
  var visible = function () { var r = box.getBoundingClientRect(); return r.top < innerHeight && r.bottom > 0; };
  var play = function () { clearInterval(timer); if (!reduce) timer = setInterval(function () { if (visible() && !document.hidden) show(k + 1); }, 4500); };
  show(0); play();
})();
