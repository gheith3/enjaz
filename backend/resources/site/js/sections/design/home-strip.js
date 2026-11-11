/* شريط المنجزين في الرئيسية: from design js/main.js (unchanged behaviour). */
(function () {
  'use strict';
  var $ = function (s, r) { return (r || document).querySelector(s); };
  var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };

  /* ---------- شريط المنجزين (الرئيسية) ---------- */
  var strip = $('[data-strip]');
  if (strip) {
    var row = $('.strip', strip);
    var items = $$('button', row);
    var motion = window.matchMedia('(prefers-reduced-motion: reduce)');
    var paused = motion.matches;
    var timer;
    function show() {
      items.forEach(function (button, i) {
        button.hidden = i >= 5;
        button.classList.toggle('center', i === 2);
        button.classList.toggle('edge', i === 0 || i === 4);
        button.setAttribute('aria-current', i === 2 ? 'true' : 'false');
        row.appendChild(button);
      });
    }
    var moving = false;
    var pendingMove = null;
    function reorder(request) {
      // Finish the current movement before honoring the latest interaction.
      if (moving) { pendingMove = request; return; }
      var shift = typeof request === 'number' ? request : items.indexOf(request) - 2;
      if (!shift) return;
      var previous = items.slice();
      var oldSlots = previous.slice(0, 5).map(function (button) { return button.getBoundingClientRect(); });
      var copies = previous.slice(0, 5).map(function (button) { return button.cloneNode(true); });
      var count = items.length;
      items = previous.slice((shift + count) % count).concat(previous.slice(0, (shift + count) % count));
      show();
      if (motion.matches) return;
      moving = true;
      var newSlots = items.slice(0, 5).map(function (button) { return button.getBoundingClientRect(); });
      var rowRect = row.getBoundingClientRect();
      var animations = [];
      var ghosts = [];
      // Extend the actual grid beyond either edge, preserving portrait spacing.
      function slot(rects, index) {
        if (index >= 0 && index < 5) return rects[index];
        var edge = index < 0 ? 0 : 4;
        var neighbor = index < 0 ? 1 : 3;
        var step = (rects[edge].left - rects[neighbor].left) / (edge - neighbor);
        return { left: rects[edge].left + (index - edge) * step,
          top: rects[edge].top, width: rects[edge].width, height: rects[edge].height };
      }
      function animate(button, from, to) {
        if (!to.width || !to.height) return;
        animations.push(button.animate([
          { transform: 'translate(' + (from.left - to.left) + 'px,' + (from.top - to.top) + 'px) scale(' + (from.width / to.width) + ',' + (from.height / to.height) + ')' },
          { transform: 'translate(0,0) scale(1,1)' }
        ], { duration: 650, easing: 'cubic-bezier(.22,.61,.36,1)' }));
      }
      previous.slice(0, 5).forEach(function (button, i) {
        if (!button.hidden) return;
        var from = oldSlots[i];
        var to = slot(newSlots, i - shift);
        var ghost = copies[i];
        ghost.hidden = false;
        ghost.classList.add('strip-ghost');
        ghost.setAttribute('aria-hidden', 'true');
        ghost.tabIndex = -1;
        ghost.inert = true;
        Object.assign(ghost.style, { left: (to.left - rowRect.left) + 'px', top: (to.top - rowRect.top) + 'px', width: to.width + 'px', height: to.height + 'px' });
        row.appendChild(ghost);
        ghosts.push(ghost);
        animate(ghost, from, to);
      });
      items.slice(0, 5).forEach(function (button, i) {
        animate(button, slot(oldSlots, i + shift), newSlots[i]);
      });
      Promise.all(animations.map(function (animation) { return animation.finished.catch(function () {}); })).then(function () {
        ghosts.forEach(function (ghost) { ghost.remove(); });
        moving = false;
        var next = pendingMove;
        pendingMove = null;
        if (next !== null) reorder(next);
      });
    }
    function moveNext() { reorder(1); }
    function movePrev() { reorder(-1); }
    function schedule() {
      clearInterval(timer);
      if (!paused) timer = setInterval(function () {
        if (!document.hidden && !(strip.contains(document.activeElement) && document.activeElement.matches(':focus-visible'))) moveNext();
      }, 3000);
    }
    $('[data-strip-next]', strip).addEventListener('click', function () { moveNext(); schedule(); });
    $('[data-strip-prev]', strip).addEventListener('click', function () { movePrev(); schedule(); });
    items.forEach(function (button) {
      // Tapping a portrait opens the achiever's page.
      button.addEventListener('click', function () {
        location.href = button.getAttribute('data-href') || '#';
      });
    });
    show();
    schedule();
  }
})();
