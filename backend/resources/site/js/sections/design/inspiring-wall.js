/* From design js/inspiring-page.js (the wall filter part). */
/* فرز جدار الإلهام حسب المجال */
(function () {
  var bar = document.querySelector('[data-wall-filter]'), wall = document.querySelector('.wall');
  if (!bar || !wall) return;
  var chips = Array.prototype.slice.call(bar.querySelectorAll('.chip'));
  var tiles = Array.prototype.slice.call(wall.querySelectorAll('.tile'));
  chips.forEach(function (chip) {
    chip.addEventListener('click', function () {
      var f = chip.dataset.field;
      chips.forEach(function (c) { var on = c === chip; c.classList.toggle('active', on); c.setAttribute('aria-pressed', String(on)); });
      tiles.forEach(function (t) { var s = t.querySelector('.tile-b span'); t.hidden = f !== '*' && (!s || s.textContent.trim() !== f); });
      wall.classList.toggle('is-filtered', f !== '*');
    });
  });
})();
