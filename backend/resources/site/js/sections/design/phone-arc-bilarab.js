/* Ported from design js/phone-arc-bilarab.js (unchanged). */
/* قوس سفلي لبانر جائزة بلعرب على الهاتف (يطابق قوس الرئيسية): سطح كريمي بلون القسم التالي، وخط برونزي مزدوج، وتوهّج وبريق (التنسيق والحركة: css/phone-arc-motion.css). */
(function () {
  var hero = document.querySelector('[data-bf] .c-hero');
  if (!hero || hero.querySelector('.bm-arc')) return;
  var NS = 'http://www.w3.org/2000/svg';
  var d = 'M-2 7Q195 77 392 7';
  hero.insertAdjacentHTML('beforeend',
    '<svg class="bm-arc" viewBox="0 0 390 60" preserveAspectRatio="none" aria-hidden="true" focusable="false">' +
    '<defs><linearGradient id="bmArcGold" x1="0" x2="1"><stop stop-color="#8E6A2C"/><stop offset=".5" stop-color="#E7C676"/><stop offset="1" stop-color="#8E6A2C"/></linearGradient></defs>' +
    '<g class="arc-m1"><path class="bm-arc-fill" d="M0 8Q195 78 390 8V54H0Z"/>' +
    '<path class="bm-arc-halo" pathLength="100" d="' + d + '"/>' +
    '<path class="bm-arc-line" pathLength="100" d="' + d + '"/>' +
    '<path class="bm-arc-echo" pathLength="100" d="M-2 1Q195 71 392 1"/>' +
    '<path class="bm-arc-glint" pathLength="100" d="' + d + '"/></g>' + '<g class="arc-m2"><path class="bm-arc-fill" d="M0 5C90 31 150 41 176 41C187 41 191 45 195 55C199 45 203 41 214 41C240 41 300 31 390 5V60H0Z"/><path class="bm-arc-line2" d="M-2 4C90 30 150 40 176 40C187 40 191 44 195 54C199 44 203 40 214 40C240 40 300 30 392 4" stroke="url(#bmArcGold)"/></g>' + '</svg>');
})();
