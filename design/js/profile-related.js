/* Repeat equal groups for continuous motion without blank stretches. */
(function () {
  document.querySelectorAll('[data-related]').forEach(function (rail) {
    var track = rail.querySelector('.rel-track');
    var group = track.querySelector('.rel-group');
    var originals = Array.from(group.children);
    var reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
    function copy(card) {
      var node = card.cloneNode(true);
      node.setAttribute('aria-hidden', 'true');
      node.tabIndex = -1;
      return node;
    }
    function size() {
      track.querySelectorAll('.rel-group-copy').forEach(function (node) { node.remove(); });
      Array.from(group.children).slice(originals.length).forEach(function (node) { node.remove(); });
      if (reduced.matches) return;
      while (group.getBoundingClientRect().width < rail.clientWidth) {
        originals.forEach(function (node) { group.appendChild(copy(node)); });
      }
      var clone = group.cloneNode(true);
      clone.classList.add('rel-group-copy');
      clone.setAttribute('aria-hidden', 'true');
      clone.querySelectorAll('a').forEach(function (a) { a.tabIndex = -1; });
      track.appendChild(clone);
    }
    size();
    new ResizeObserver(size).observe(rail);
    reduced.addEventListener('change', size);
  });
})();
