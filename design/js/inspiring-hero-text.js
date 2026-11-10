/* هيدر «ملهمون» — نماذج تطوير قسم الكلام: ?tx=1 لوح زجاجي، ?tx=2 تحريري، ?tx=3 كلمة متبدّلة. */
(function () {
  var m = /[?&]tx=(\d)/.exec(location.search); if (!m) return;
  var tx = +m[1], hero = document.querySelector('.ins-strip'); if (!hero) return;
  var head = hero.querySelector('.is-head'), cta = hero.querySelector('.is-discover');
  hero.classList.add('tx' + tx);
  var faces = [['mohammed', '30% 20%'], ['safiya', '55% 30%'], ['laith', '50% 25%'], ['zainab', '40% 20%']];
  function facesRow(txt) {
    var d = document.createElement('div'); d.className = 'tx-faces';
    d.innerHTML = '<span class="f">' + faces.map(function (f) { return '<img src="assets/img/inspiring-strip/' + f[0] + '.jpg?v=1" alt="" style="object-position:' + f[1] + '">'; }).join('') + '</span><span>' + txt + '</span>';
    return d;
  }
  if (tx === 1) head.insertBefore(facesRow('قصص <b>ملهمة</b> في الرياضة والفن والقرآن الكريم والعلم'), cta);
  if (tx === 2) {
    var h1 = hero.querySelector('.is-title');
    h1.childNodes.forEach(function (n) { if (n.nodeType === 3 && n.textContent.trim()) { var s = document.createElement('span'); s.className = 'is-from'; s.textContent = n.textContent.trim(); h1.replaceChild(s, n); } });
  }
  if (tx === 3) {
    var F = ['الرياضة', 'الفن', 'القرآن الكريم', 'العلم'];
    var r = document.createElement('div'); r.className = 'tx-rot'; r.setAttribute('aria-hidden', 'true');
    r.innerHTML = '<span>ملهمون في</span><span class="win">' + F.map(function (f) { return '<i>' + f + '</i>'; }).join('') + '</span>';
    head.insertBefore(r, hero.querySelector('.is-intro'));
    head.insertBefore(facesRow('<b>وجوه</b> نعتز بها من كل مجال'), cta);
    var items = r.querySelectorAll('.win i'), k = 0;
    function show(n) {
      items.forEach(function (it, j) { it.classList.toggle('on', j === n); it.classList.toggle('out', j === (n + items.length - 1) % items.length && j !== n); });
      r.querySelector('.win').style.setProperty('--uw', items[n].offsetWidth + 'px');
    }
    show(0);
    if (!matchMedia('(prefers-reduced-motion: reduce)').matches) setInterval(function () { k = (k + 1) % items.length; show(k); }, 2300);
  }
})();
