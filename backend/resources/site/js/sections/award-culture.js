/* جائزة الثقافة (award.html): from design js/award.js, the interactive part only.
   The medals timeline is rendered by the server; this filters it by branch, fills the winner preview (desktop)
   and the bottom sheet (phone) from window.INJAZ_AWARD_WINNERS. */
(function () {
  var root = document.querySelector('[data-award]'), A = window.INJAZ_AWARD_WINNERS;
  if (!root || !A) return;
  var W = A.winners, L = A.labels;
  var $ = function (s) { return root.querySelector(s); };
  var ar = function (n) { return String(n).replace(/\d/g, function (d) { return '٠١٢٣٤٥٦٧٨٩'[d]; }); };
  var esc = function (s) { var d = document.createElement('div'); d.textContent = s == null ? '' : s; return d.innerHTML; };
  var icon = function (k) { var b = root.querySelector('.aw-filters [data-aw-f="' + k + '"] svg'); return b ? b.outerHTML : ''; };
  var desk = function () { return matchMedia('(min-width: 1100px)').matches; };
  var st = { branch: '' };

  function details(w) {
    return (w.why ? '<div class="aw-why"><span>' + esc(L.why) + '</span><p>' + esc(w.why) + '</p></div>' : '') +
      '<dl>' + (w.work ? '<div><dt>' + esc(L.work) + '</dt><dd>' + esc(w.work) + '</dd></div>' : '') +
      '<div><dt>' + esc(L.edition) + '</dt><dd>' + esc(w.edition) + '</dd></div>' +
      (w.medal ? '<div><dt>' + esc(L.honor) + '</dt><dd>' + esc(w.medal) + '</dd></div>' : '') +
      (w.arab && w.basis ? '<div><dt>' + esc(L.notable) + '</dt><dd>' + esc(L.notable_text.replace(':basis', w.basis)) + '</dd></div>' : '') + '</dl>' +
      (w.profile ? '<a class="aw-card-go" href="' + esc(w.profile) + '" aria-label="' + esc(L.profile + ': ' + w.name) + '">' + esc(L.profile) + ' ←</a>' : '');
  }
  var disc = function (w) { return '<span class="aw-disc big">' + (w.photo ? '<img src="' + esc(w.photo) + '" alt=""' + (w.group ? ' class="is-group"' : '') + '>' : '') + '</span>'; };
  var kicker = function (w) { return '<p class="aw-card-k">' + icon(w.branch) + esc(L.branch.replace('%s', w.branchName || '')) + ' · ' + esc(w.field) + '</p>'; };

  // Fit the preview to the screen height without inner scrolling (design fitWinnerPreview).
  function fit() {
    var pane = $('[data-aw-preview]'), content = pane && pane.querySelector('.aw-preview-content');
    if (!content || !desk()) return;
    var available = Math.max(1, window.innerHeight - 100);
    var setScale = function (s) { content.style.zoom = s; content.style.width = (100 / s) + '%'; };
    setScale(1);
    if (pane.getBoundingClientRect().height <= available) return;
    var low = 0.1, high = 1;
    for (var i = 0; i < 12; i++) { var mid = (low + high) / 2; setScale(mid); if (pane.getBoundingClientRect().height <= available) low = mid; else high = mid; }
    setScale(low);
  }
  function preview(i) {
    var pane = $('[data-aw-preview]'); if (!pane) return;
    var w = W[i];
    if (!w) { pane.innerHTML = '<p>' + esc(L.no_winners) + '</p>'; return; }
    pane.className = 'aw-winner-preview b-' + w.branch;
    pane.innerHTML = '<div class="aw-preview-content"><div class="aw-preview-heading"><span class="aw-preview-label">' + esc(L.preview_label) + '</span>' + disc(w) +
      '<div class="aw-preview-identity"><h3>' + esc(w.name) + '</h3>' + kicker(w) + '</div></div>' + details(w) + '</div>';
    fit();
    [].forEach.call(root.querySelectorAll('[data-w]'), function (b) { b.setAttribute('aria-pressed', String(+b.dataset.w === i)); });
  }
  function filter() {
    var first = null, n = 0;
    [].forEach.call(root.querySelectorAll('.aw-tl .aw-ed'), function (ed) {
      var shown = 0;
      [].forEach.call(ed.querySelectorAll('.aw-m'), function (m) {
        var ok = !st.branch || m.dataset.branch === st.branch;
        m.hidden = !ok;
        if (ok) { shown++; if (first === null) first = +m.querySelector('[data-w]').dataset.w; }
      });
      ed.hidden = !shown; n += shown;
    });
    $('[data-aw-count]').textContent = ar(n);
    [].forEach.call(root.querySelectorAll('[data-aw-f]'), function (b) { b.setAttribute('aria-pressed', String(b.dataset.awF === st.branch)); });
    preview(first);
  }
  $('.aw-filters').addEventListener('click', function (e) { var b = e.target.closest('[data-aw-f]'); if (b) { st.branch = b.dataset.awF; filter(); } });
  root.addEventListener('click', function (e) {
    var b = e.target.closest('[data-jump-branch]'); if (!b) return;
    st.branch = b.dataset.jumpBranch; filter();
    $('#aw-winners').scrollIntoView({ behavior: 'smooth', block: 'start' });
  });
  filter();
  addEventListener('resize', fit);
  if (document.fonts && document.fonts.ready) document.fonts.ready.then(fit);

  // Winner card sliding from the bottom (phone and tablet).
  var sheet = $('[data-aw-sheet]'), card = sheet.querySelector('.aw-card'), lastBtn = null;
  function open(i, btn) {
    if (desk()) { preview(i); return; }
    var w = W[i]; lastBtn = btn;
    card.className = 'aw-card b-' + w.branch;
    card.innerHTML = '<button type="button" class="aw-x" data-aw-close aria-label="' + esc(L.close) + '"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 6l12 12M18 6 6 18"/></svg></button>' +
      '<span class="aw-card-rib" aria-hidden="true"></span>' + disc(w) + '<h3 id="aw-card-h">' + esc(w.name) + '</h3>' + kicker(w) + details(w);
    sheet.hidden = false;
    requestAnimationFrame(function () { sheet.classList.add('on'); });
    document.documentElement.style.overflow = 'hidden';
    card.querySelector('[data-aw-close]').focus();
  }
  function close() {
    sheet.classList.remove('on'); document.documentElement.style.overflow = '';
    setTimeout(function () { sheet.hidden = true; }, 320);
    if (lastBtn) lastBtn.focus();
  }
  root.addEventListener('click', function (e) { var b = e.target.closest('[data-w]'); if (b) open(+b.dataset.w, b); });
  sheet.addEventListener('click', function (e) { if (e.target === sheet || e.target.closest('[data-aw-close]')) close(); });
  addEventListener('keydown', function (e) { if (e.key === 'Escape' && !sheet.hidden) close(); });
})();
