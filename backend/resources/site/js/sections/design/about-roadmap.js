/* خارطة العمل — الخيط الذهبي (صفحة من نحن)
   s = العنوان المختصر على البطاقة، t = النص الكامل في نافذة التفاصيل */
(function () {
'use strict';
var root = document.querySelector("[data-roadmap]");
if (!root) return;
const IMG = "";   // full URLs from the server
const M = (window.INJAZ_ABOUT && window.INJAZ_ABOUT.roadmap) || [];   // from the database (site/sections/about/parts)
window.INJAZ_ROADMAP = M; // يُستخدم أيضًا في عرض الكمبيوتر (js/about-journey-desktop.js)

const $ = (s,r=document) => r.querySelector(s), $$ = (s,r=document) => [...r.querySelectorAll(s)];
const reduce = matchMedia("(prefers-reduced-motion: reduce)").matches;
const ar = n => String(n).replace(/\d/g, d => "٠١٢٣٤٥٦٧٨٩"[d]);
const fullDate = m => /م$/.test(m.date) ? m.date : (m.date === "رمضان" ? "رمضان " + m.y + "م" : m.date + " " + m.y + "م");

/* نافذة التفاصيل */
const sheet = $("[data-rm-sheet]"); let si = 0;
function openSheet(i){ si = (i + M.length) % M.length; const m = M[si], md = $("[data-rm-media]", sheet);
  md.className = "rm-media" + (m.logo ? " logo" : "");
  md.innerHTML = `<img src="${IMG + m.img}" alt="${m.t}">`;
  $("[data-rm-n]", sheet).textContent = ar(si + 1);
  $("[data-rm-date]", sheet).textContent = fullDate(m); $("[data-rm-t]", sheet).textContent = m.t;
  if (!sheet.open) sheet.showModal(); }
$("[data-rm-x]", sheet).onclick = () => sheet.close();
sheet.addEventListener("click", e => { if (e.target === sheet) sheet.close(); });
$("[data-rm-next]", sheet).onclick = () => openSheet(si + 1);
$("[data-rm-prev]", sheet).onclick = () => openSheet(si - 1);

/* البناء: الرقم عند انحناءة الخيط، والصورة بجانبه، والتفاصيل تحتها، بالتناوب يمينًا ويسارًا */
const tl = $("[data-rm-tl]", root), ol = $("ol", tl);
let last = null, side = 0;
M.forEach((m,i) => {
  if (m.y !== last) { last = m.y; const y = document.createElement("li"); y.className = "rm-yr"; y.innerHTML = `<b>${m.y}م</b>`; ol.append(y); }
  const li = document.createElement("li"); li.className = "rm-st " + (side++ % 2 ? "l" : "r");
  li.innerHTML = `<span class="rm-node" aria-hidden="true">${ar(i+1)}</span>
    <button class="rm-sq" type="button" aria-label="${ar(i+1)}: ${m.t}"><span class="rm-im${m.logo ? " logo" : ""}"><img src="${IMG + m.img}" alt="" loading="lazy"></span></button>
    <span class="rm-cap"><span class="rm-date">${m.date}</span><span class="rm-t">${m.s}</span></span>`;
  $(".rm-sq", li).onclick = () => openSheet(i);
  ol.append(li);
});

/* النجوم */
(() => { const s = $("[data-rm-stars]", tl); for (let i = 0; i < 60; i++) { const e = document.createElement("i");
  e.style.cssText = `left:${Math.random()*100}%;top:${Math.random()*100}%;animation-delay:${(Math.random()*3).toFixed(2)}s;opacity:${(.2+Math.random()*.6).toFixed(2)}`; s.append(e); } })();

/* الترتيب: كل رقم أسفل الذي قبله بمسافة ثابتة */
const PITCH = 72;      // المسافة الرأسية بين كل رقم والذي يليه
const UNDER = 32 + 4;  // من مركز الرقم إلى منتصف الفراغ بين الصورة وتفاصيلها
function stack(){
  const items = $$("li", ol); items.forEach(li => li.style.marginTop = "");
  items.forEach((li, i) => {
    const prev = items[i-1], prev2 = items[i-2];
    if (li.classList.contains("rm-st") && prev && prev.classList.contains("rm-st")) {
      const pb = prev.offsetTop + prev.offsetHeight;
      let top = prev.offsetTop + PITCH;
      if (prev2 && prev2.classList.contains("rm-st")) top = Math.max(top, prev2.offsetTop + prev2.offsetHeight + 8);
      li.style.marginTop = (top - pb) + "px";
    }
    if (li.classList.contains("rm-st")) { const sq = $(".rm-sq", li); $(".rm-node", li).style.top = (sq.offsetTop + sq.offsetHeight/2) + "px"; }
  });
}
/* الخيط: ينزل من الرقم، ثم يعبر أفقيًا في الفراغ تحت الصورة، ثم ينحني نازلًا للرقم التالي — فلا يمرّ فوق أي صورة */
function seg(a, b){
  const [x0,y0,pill0] = a, [x1,y1] = b, sx = Math.sign(x1 - x0);
  if (Math.abs(x1 - x0) < 1) return ` L${x1},${y1}`;
  const yc = pill0 ? y0 : Math.min(y0 + UNDER, y1 - 4);
  const r1 = Math.min(38, yc - y0, Math.abs(x1 - x0) / 2), r2 = Math.min(38, y1 - yc, Math.abs(x1 - x0) / 2);
  let d = "";
  if (r1 > 0) d += ` L${x0},${yc - r1} C${x0},${yc} ${x0},${yc} ${x0 + sx*r1},${yc}`;
  d += ` L${x1 - sx*r2},${yc} C${x1},${yc} ${x1},${yc} ${x1},${yc + r2} L${x1},${y1}`;
  return d;
}
const fg = $(".rm-fg", tl), bgp = $(".rm-bg", tl), spark = $("[data-rm-spark]", tl);
let LEN = 0;
function layout(){
  stack();
  const w = tl.getBoundingClientRect();
  const P = $$(".rm-yr b, .rm-node", tl).map(n => { const r = n.getBoundingClientRect();
    return [r.left - w.left + r.width/2, r.top - w.top + r.height/2, !n.classList.contains("rm-node")]; });
  let d = `M${P[0][0]},${P[0][1]}`; for (let i = 1; i < P.length; i++) d += seg(P[i-1], P[i]);
  fg.setAttribute("d", d); bgp.setAttribute("d", d);
  LEN = fg.getTotalLength(); fg.style.strokeDasharray = LEN; update();
}
function update(){
  const r = tl.getBoundingClientRect(), vh = innerHeight;
  if (r.bottom < -200 || r.top > vh + 200) return;
  const p = reduce ? 1 : Math.min(1, Math.max(0, (vh*.62 - r.top) / r.height)), L = LEN * p;
  fg.style.strokeDashoffset = LEN - L;
  const pt = fg.getPointAtLength(Math.max(.01, L));
  spark.style.left = pt.x + "px"; spark.style.top = pt.y + "px"; spark.style.opacity = p > 0 && p < 1 ? 1 : 0;
  $$(".rm-st", tl).forEach(li => { const nr = $(".rm-node", li).getBoundingClientRect(); li.classList.toggle("reached", reduce || nr.top + nr.height/2 < vh*.62); });
}
addEventListener("scroll", update, {passive:true});
let rw = innerWidth; addEventListener("resize", () => { if (innerWidth !== rw) { rw = innerWidth; layout(); } });
addEventListener("load", layout);
if (document.fonts) document.fonts.ready.then(layout);
layout();

/* ظهور المحطات */
const io = new IntersectionObserver(es => es.forEach(e => { if (e.isIntersecting) { e.target.classList.add("in"); io.unobserve(e.target); } }), {threshold:.2});
$$(".rm-st", tl).forEach(li => io.observe(li));
})();
