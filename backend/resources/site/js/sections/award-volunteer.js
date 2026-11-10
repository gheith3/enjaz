/* جائزة العمل التطوعي (award-volunteer.html): from design js/award-volunteer.js and award-volunteer-winners.js,
   the interactive part only. Podiums and edition tabs are rendered by the server; this switches the tabs and opens
   a winner's card from window.INJAZ_VOL_WINNERS. */
(function () {
  var root = document.querySelector('[data-vol]'), V = window.INJAZ_VOL_WINNERS;
  if (!root || !V) return;
  var W = V.winners, L = V.labels;
  var esc = function (s) { var d = document.createElement('div'); d.textContent = s == null ? '' : s; return d.innerHTML; };
  var icon = function (k) { var i = root.querySelector('.vc-' + k + ' svg'); return i ? i.outerHTML : ''; };

  // ---------- Edition tabs (desktop) ----------
  var box = root.querySelector('[data-lw]');
  if (box) {
    var tabs = box.querySelectorAll('[role="tab"]'), panels = box.querySelectorAll('[role="tabpanel"]');
    var pick = function (k, focus) {
      [].forEach.call(tabs, function (t, i) { t.setAttribute('aria-selected', i === k ? 'true' : 'false'); t.tabIndex = i === k ? 0 : -1; });
      [].forEach.call(panels, function (p, i) { p.hidden = i !== k; });
      if (focus) tabs[k].focus();
    };
    [].forEach.call(tabs, function (t, i) {
      t.addEventListener('click', function () { pick(i); });
      t.addEventListener('keydown', function (ev) { var d = ev.key === 'ArrowLeft' ? 1 : ev.key === 'ArrowRight' ? -1 : 0; if (d) { ev.preventDefault(); pick((i + d + tabs.length) % tabs.length, true); } });
    });
  }

  // ---------- Winner card ----------
  var sheet = root.querySelector('[data-v-sheet]'), card = sheet.querySelector('.vs-card'), last = null;
  var avatar = function (w) {
    var p = w.pic;
    if (!p) return '<i class="vp-av big' + (w.cat === 'org' ? ' is-org' : '') + '">' + icon(w.cat) + '</i>';
    return '<i class="vp-av has-img big' + (p.photo ? '' : ' is-logo') + '"><img src="' + esc(p.src) + '" alt="" loading="lazy"' + (p.pos && p.photo ? ' style="object-position:' + esc(p.pos) + '"' : '') + '></i>';
  };
  function open(w, btn) {
    last = btn;
    card.className = 'vs-card r' + w.rank;
    card.innerHTML = '<button type="button" class="vs-x" data-v-close aria-label="' + esc(L.close) + '"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 6l12 12M18 6 6 18"/></svg></button>' +
      '<span class="vs-rings" aria-hidden="true"><i></i><i></i><i></i></span>' +
      '<div class="vs-media">' + (w.photo ? '<figure class="vs-photo"><img src="' + esc(w.photo) + '" alt="' + esc(w.name) + '"><figcaption>' + esc(L.photo.replace('%s', w.credit || '')) + '</figcaption></figure>' : avatar(w)) +
      (w.gallery.length ? '<div class="vs-gal">' + w.gallery.map(function (g) { return '<figure><img src="' + esc(g[0]) + '" alt="' + esc(g[1]) + '" loading="lazy"><figcaption>' + esc(g[1]) + '</figcaption></figure>'; }).join('') + '</div>' : '') + '</div>' +
      '<span class="vs-rank">' + esc(L.ranks[w.rank] || '') + ' · ' + esc(L.category.replace('%s', w.catName || '')) + '</span>' +
      '<h3 id="vs-h">' + esc(w.name) + '</h3><p class="vs-proj">' + esc(L.project.replace('%s', w.project || '')) + (w.place ? ' · ' + esc(w.place) : '') + '</p>' +
      (w.why ? '<div class="vs-why"><span>' + esc(L.why) + '</span><p>' + esc(w.why) + '</p></div>' : '<p class="vs-none">' + esc(L.no_why) + '</p>') +
      (w.facts.length ? '<dl class="vs-facts">' + w.facts.map(function (f) { return '<div><dt>' + esc(f[0]) + '</dt><dd>' + esc(f[1]) + '</dd></div>'; }).join('') + '</dl>' : '') +
      (w.src ? '<p class="vs-src">' + esc(L.source) + ' <a href="' + esc(w.src[1]) + '" target="_blank" rel="noopener">' + esc(w.src[0]) + '</a></p>' : '') +
      '<p class="vs-ed">' + esc(w.edition) + '</p>';
    sheet.hidden = false;
    requestAnimationFrame(function () { sheet.classList.add('on'); });
    document.documentElement.style.overflow = 'hidden';
    card.querySelector('[data-v-close]').focus();
  }
  function close() {
    sheet.classList.remove('on'); document.documentElement.style.overflow = '';
    setTimeout(function () { sheet.hidden = true; }, 320);
    if (last) last.focus();
  }
  root.addEventListener('click', function (e) { var b = e.target.closest('[data-vw]'); if (b) open(W[+b.dataset.vw], b); });
  sheet.addEventListener('click', function (e) { if (e.target === sheet || e.target.closest('[data-v-close]')) close(); });
  addEventListener('keydown', function (e) { if (e.key === 'Escape' && !sheet.hidden) close(); });
})();
