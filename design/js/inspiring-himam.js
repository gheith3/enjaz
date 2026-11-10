/* «هِمَمٌ بلا حدود»: زر تكبير الخط في قسم أخبار الأشخاص ذوي الإعاقة، ويُحفظ اختيار الزائر */
(function () {
  var sec = document.querySelector('[data-hm]'), btn = sec && sec.querySelector('[data-hm-size]');
  if (!btn) return;
  var set = function (on) { sec.classList.toggle('hm-big', on); btn.setAttribute('aria-pressed', on ? 'true' : 'false'); };
  try { set(localStorage.getItem('hm-big') === '1'); } catch (e) {}
  btn.addEventListener('click', function () {
    var on = !sec.classList.contains('hm-big'); set(on);
    try { localStorage.setItem('hm-big', on ? '1' : '0'); } catch (e) {}
  });
})();
