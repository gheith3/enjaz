/* من نحن (about.html): the design's inline page script, unchanged except that its data (PAGE) comes from
   window.INJAZ_ABOUT (printed by site/sections/about/parts from the database) and the menu is handled by core/menu.js. */
(function () {
if (!window.INJAZ_ABOUT || !document.body.classList.contains('about-page')) return;

/* =========================================================
   بيانات الصفحة — عدّل هنا فقط
   ========================================================= */
const IMG = "", SVG = "";   // image URLs come complete from the server
const PAGE = window.INJAZ_ABOUT;

/* =========================================================
   محرك القوالب والتفاعل
   ========================================================= */
const $ = (s, r=document) => r.querySelector(s);
const $$ = (s, r=document) => [...r.querySelectorAll(s)];
const tpl = id => document.getElementById(id).content.firstElementChild.cloneNode(true);
const reduce = matchMedia("(prefers-reduced-motion: reduce)").matches;
const ar = n => String(n).replace(/\d/g, d => "٠١٢٣٤٥٦٧٨٩"[d]).replace(/(\S)(?=(\S{3})+$)/g,"$1٬");

/* دخول الصورة الرئيسية بالنقاط النصفية: صار بالتنسيق وحده (انظر .hero-art img في الأعلى) */

/* شريط الأقسام */
const chips = $("[data-chips]");
PAGE.nav.forEach(([h,t]) => chips.insertAdjacentHTML("beforeend", `<li><a href="${h}">${t}</a></li>`));

/* القائمة */
/* القائمة: resources/site/js/core/menu.js */

const countUp = el => { const n = +el.dataset.n, t0 = performance.now(), dur = 1400;
  const step = t => { const p = Math.min(1,(t-t0)/dur), e = 1-Math.pow(1-p,3); el.textContent = el.dataset.pre + ar(Math.round(n*e)); if (p<1) requestAnimationFrame(step); };
  requestAnimationFrame(step); };

/* ميلان صورة الافتتاحية مع اللمس / المؤشر */
const art = $("[data-tilt]"), artImg = $("img", art);
if (!reduce) art.addEventListener("pointermove", e => { const r = art.getBoundingClientRect(); const x = (e.clientX-r.left)/r.width-.5, y = (e.clientY-r.top)/r.height-.5; artImg.style.setProperty("--ry", `${x*14}deg`); artImg.style.setProperty("--rx", `${-y*10}deg`); });
art.addEventListener("pointerleave", () => { artImg.style.setProperty("--ry","0deg"); artImg.style.setProperty("--rx","0deg"); });

/* ٢) القوس ثلاثي الأبعاد */
const ring = $("[data-carousel]"), stage = $("[data-stage]"), info = $("[data-field-info]");
const N = PAGE.fields.length, STEP = 360/N;
/* نصف القطر محسوب من عرض البطاقة لتبقى البطاقات متقاربة */
const CARD_W = 104, CARD_GAP = 6;
const R = () => Math.round(N*(CARD_W+CARD_GAP)/(2*Math.PI));
const cards = PAGE.fields.map((f,i) => { const el = tpl("tpl-field");
  $(".ph",el).src = IMG+f.img; $(".ph",el).alt = ""; $(".ico img",el).src = SVG+f.ico; $(".lbl",el).textContent = f.t;
  el.setAttribute("aria-label", f.t); el.onclick = () => { if (!moved) goTo(i); }; ring.append(el); return el; });
let angle = 0, target = 0, active = -1, auto = !reduce, moved = false;
const layout = () => { const r = R(); cards.forEach((c,i) => c.style.transform = `rotateY(${i*STEP}deg) translateZ(${r}px)`); };
const norm = a => ((a % 360) + 360) % 360;
const goTo = i => { const cur = Math.round(-target/STEP); let d = ((i - cur) % N + N) % N; if (d > N/2) d -= N; target = -(cur + d)*STEP; };
const setInfo = i => { if (i === active) return; active = i; const f = PAGE.fields[i];
  cards.forEach((c,k) => { c.classList.toggle("active", k===i); c.classList.toggle("dim", k!==i); c.tabIndex = k===i ? 0 : -1; });
};
/* بطاقة «استكشف» مستقلة عن الدوران: تتبدّل كل ٥ ثوانٍ */
const dotsBox = $("[data-explore-dots]"); let exIdx = -1;
PAGE.fields.forEach(() => dotsBox.append(document.createElement("i")));
const showExplore = i => { const f = PAGE.fields[i]; exIdx = i;
  info.innerHTML = `<div class="txt fade-in"><b>${f.t}</b><small>${f.d}</small></div><a href="/achievements">استكشف</a>`;
  [...dotsBox.children].forEach((d,k) => d.classList.toggle("on", k===i)); };
showExplore(0);
if (!matchMedia("(prefers-reduced-motion: reduce)").matches) setInterval(() => showExplore((exIdx + 1) % N), 5000);
const tick = () => { if (auto && !dragging) target -= 0.06; angle += (target-angle)*0.09;
  ring.style.transform = `translateZ(${-R()}px) rotateY(${angle}deg)`;
  setInfo(((Math.round(-angle/STEP) % N) + N) % N); requestAnimationFrame(tick); };
let dragging = false, sx = 0, sa = 0;
stage.addEventListener("pointerdown", e => { dragging = true; moved = false; sx = e.clientX; sa = target; stage.setPointerCapture(e.pointerId); });
stage.addEventListener("pointermove", e => { if (!dragging) return; const dx = e.clientX - sx; if (Math.abs(dx) > 6) moved = true; target = sa + dx*0.35; });
const endDrag = () => { if (!dragging) return; dragging = false; target = Math.round(target/STEP)*STEP; };
stage.addEventListener("pointerup", endDrag); stage.addEventListener("pointercancel", endDrag);
$("[data-ring-next]").onclick = () => { target = Math.round(target/STEP)*STEP - STEP; };
$("[data-ring-prev]").onclick = () => { target = Math.round(target/STEP)*STEP + STEP; };
const pauseBtn = $("[data-ring-pause]");
const paintPause = () => { pauseBtn.setAttribute("aria-pressed", !auto); pauseBtn.setAttribute("aria-label", auto ? "إيقاف الدوران التلقائي" : "تشغيل الدوران التلقائي");
  pauseBtn.innerHTML = auto ? '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M7 5h3v14H7zM14 5h3v14h-3z"/></svg>' : '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M17 5v14L6 12z"/></svg>'; };
pauseBtn.onclick = () => { auto = !auto; target = Math.round(target/STEP)*STEP; paintPause(); };
stage.addEventListener("keydown", e => { if (e.key==="ArrowLeft") $("[data-ring-next]").click(); if (e.key==="ArrowRight") $("[data-ring-prev]").click(); });
paintPause(); layout(); addEventListener("resize", layout); requestAnimationFrame(tick);


/* ٤) خارطة العمل: تصميم الخيط الذهبي في ./js/about-roadmap.js */
let pLen = 0; const pWrap = null, pFg = null, stopsBox = null;
function drawPath(){}

/* ٥) كلمة المؤسس */
$("[data-founder-text]").innerHTML = PAGE.founder.map(p=>`<p>${p}</p>`).join("");
const fBox = $("[data-founder]"), fText = $("[data-founder-text]"), fBtn = $("[data-founder-toggle]");
fBtn.onclick = () => { const o = !fBox.classList.contains("open"); fBox.classList.toggle("open",o); fBtn.setAttribute("aria-expanded",o);
  fText.style.maxHeight = o ? fText.scrollHeight+"px" : ""; $("span",fBtn).textContent = o ? "إخفاء الكلمة" : "اقرأ الكلمة كاملة";
  if(!o) fBox.scrollIntoView({block:"start",behavior:reduce?"auto":"smooth"}); setTimeout(drawPath,650); };

/* ٦) قالوا عن إنجازات */
(() => {
  const DUR = 8000, Q = PAGE.quotes, n = Q.length, box = $("[data-tm]"), orbit = $("[data-tm-orbit]"), body = $("[data-tm-body]");
  const stars = $("[data-tm-stars]");
  for (let i = 0; i < 26; i++) { const e = document.createElement("i"); e.style.cssText = `left:${Math.random()*100}%;top:${Math.random()*70}%;animation-delay:${(Math.random()*3).toFixed(2)}s;opacity:${(.3+Math.random()*.6).toFixed(2)}`; stars.append(e); }
  const avs = Q.map((q,i) => { const b = document.createElement("button"); b.type = "button"; b.className = "tm-av"; b.style.setProperty("--dur", DUR+"ms");
    b.setAttribute("aria-label", `شهادة ${q.n}`);
    b.innerHTML = `<svg viewBox="0 0 100 100" aria-hidden="true"><circle class="track" cx="50" cy="50" r="48" pathLength="100"/><circle class="prog" cx="50" cy="50" r="48" pathLength="100"/></svg><span class="ph"><img src="${IMG+q.img}" alt=""></span>`;
    b.onclick = () => go(i, true); if (q.t === "person") b.classList.add("is-person"); orbit.append(b); return b; });
  // مواضع الشخصيات على القوس: الوسط، التالية يسارًا، السابقة يمينًا
  const slots = { center:{x:"0px",y:"34px",s:1,o:1,sat:1}, next:{x:"-112px",y:"92px",s:.48,o:.75,sat:.4}, prev:{x:"112px",y:"92px",s:.48,o:.75,sat:.4} };
  // البقية على السطر المقوّس أسفل الثلاثة (نفس منحنى tm-qarc): الأقرب للدخول في يسار السطر بجانب «التالية»، والخارج من المدار يذهب إلى يمينه
  const queueSlot = j => { const m = Math.max(1, n - 3), t = m === 1 ? .5 : j / (m - 1), u = 1 - t;
    const cx = u*u*-84 + 2*u*t*0 + t*t*84, cy = u*u*214 + 2*u*t*244 + t*t*214;
    return { x: cx.toFixed(1) + "px", y: (cy - 66).toFixed(1) + "px", s: .22, o: .95, sat: .85 }; };
  let cur = 0, timer = 0, started = false;
  const words = t => t.split(/\s+/).map((w,i) => `<span style="--d:${i}">${w}</span>`).join(" ");
  function go(i, user){ cur = (i + n) % n;
    avs.forEach((b,k) => { const rel = (k - cur + n) % n, slot = rel === 0 ? slots.center : rel === 1 ? slots.next : rel === n - 1 ? slots.prev : queueSlot(rel - 2);
      // نفس المقاس للجميع في كل موضع (الوسط بمقاس واحد، والجانبان بمقاس واحد)
      for (const [key,v] of Object.entries(slot)) b.style.setProperty("--"+key, v);
      b.classList.toggle("is-queue", rel > 1 && rel < n - 1);
      b.classList.remove("is-center"); b.tabIndex = rel === 0 ? -1 : 0; });
    void avs[cur].offsetWidth; avs[cur].classList.add("is-center");
    const q = Q[cur];
    body.innerHTML = `<span class="tm-mark" aria-hidden="true">“</span><p class="tm-text">${reduce ? q.q : words(q.q)}</p><div class="tm-who"><span class="rule" aria-hidden="true"></span><b>${q.n}</b><small>${q.r}</small></div>`;
    $("[data-tm-count]").textContent = `${ar(cur+1)} / ${ar(n)}`;
    clearTimeout(timer); if (!reduce && started) timer = setTimeout(() => go(cur+1), DUR); }
  $("[data-tm-next]").onclick = () => go(cur+1, true);
  $("[data-tm-prev]").onclick = () => go(cur-1, true);
  // سحب يمين/يسار
  let sx = null; box.addEventListener("pointerdown", e => sx = e.clientX);
  box.addEventListener("pointerup", e => { if (sx === null) return; const dx = e.clientX - sx; sx = null; if (Math.abs(dx) > 45) go(cur + (dx > 0 ? 1 : -1), true); });
  // إيقاف مؤقت عند مرور المؤشر
  box.addEventListener("pointerenter", e => { if (e.pointerType !== "mouse") return; clearTimeout(timer); avs[cur].classList.add("paused"); });
  box.addEventListener("pointerleave", e => { if (e.pointerType !== "mouse" || reduce) return; const av = avs[cur]; av.classList.remove("paused","is-center"); void av.offsetWidth; av.classList.add("is-center"); clearTimeout(timer); timer = setTimeout(() => go(cur+1), DUR); });
  // يبدأ دائمًا بصاحب السمو السيد أسعد، ولا يبدأ التبديل إلا عند وصول الزائر للقسم
  go(0);
  const tmIo = new IntersectionObserver(es => { if (es[0].isIntersecting) { tmIo.disconnect(); started = true; go(0); } }, { threshold: .35 });
  tmIo.observe(box);
})();


/* نافذة العرض */
const lb = $("[data-lb]");
const openLb = (src,title,sub,en) => { lb.classList.toggle("no-img", !src); if (src) { $("[data-lb-img]").src = src; $("[data-lb-img]").alt = title; }
  $("[data-lb-title]").textContent = title; $("[data-lb-sub]").textContent = sub||"";
  $("[data-lb-en]").textContent = en||""; $("[data-lb-en]").hidden = !en; lb.showModal(); };
$("[data-lb-close]").onclick = () => lb.close(); lb.addEventListener("click", e => { if (e.target===lb) lb.close(); });

/* ٧) الفريق */
const cardsBox = $("[data-cards]"), tBar = $("[data-bar]");
const tSrc = m => m.img ? IMG+m.img : SVG+(m.g==="woman"?"icon-omani-woman-teal.svg":"icon-omani-man-teal.svg");
const TN = PAGE.team.length;
const tCards = PAGE.team.map((m,i) => { const c = document.createElement("div"); c.className = "tc";
  c.innerHTML = `<img alt="${m.n}" src="${tSrc(m)}"><div class="tc-cap"><b>${m.n}</b><small>${m.r}</small></div>`;
  c.onclick = () => { if (c.classList.contains("front")) openLb(tSrc(m), m.n, m.r); };
  cardsBox.append(c); return c; });
/* مواضع الرزمة: الأمامية مستقيمة، والبقية مائلة ومتدرّجة في العمق خلفها */
const tPos = p => ["rotate(-1deg)", "translate(16px,-6px) rotate(6deg) scale(.95)", "translate(-18px,-10px) rotate(-8deg) scale(.9)"][p] || "translateY(-12px) scale(.85)";
let tOrder = PAGE.team.map((_,i) => i), tTimer = null, tBusy = false;
const tLay = (skip) => tOrder.forEach((ci,p) => { const c = tCards[ci]; if (c === skip) return;
  c.style.zIndex = 20 - p; c.style.transform = tPos(p); c.style.filter = `brightness(${1 - Math.min(p,3)*.07})`;
  c.style.opacity = 1; c.classList.toggle("front", p === 0); });
const tShow = i => {
  const cur = tOrder[0]; if (i === cur || tBusy) return;
  const old = tCards[cur];
  tOrder = Array.from({length:TN}, (_,k) => (i + k) % TN);
  tBar.classList.remove("go"); tBar.offsetWidth; if (!reduce) tBar.classList.add("go");
  if (reduce) return tLay();
  tBusy = true; old.classList.remove("front"); old.style.zIndex = 25;
  // البطاقة الأمامية تخرج كاملةً من حدود الرزمة، وفي منتصف المدة تمامًا (وهي خارجها) تنتقل للخلف ثم تعود خلف البطاقات
  const back = tPos(TN - 1), D = 1000;
  old.style.transition = "none"; old.style.transform = back;
  old.animate([
    { transform: tPos(0), easing: "cubic-bezier(.4,0,.2,1)" },
    { transform: "translate(-235px,10px) rotate(-16deg) scale(.94)", offset: .5, easing: "cubic-bezier(.4,0,.2,1)" },
    { transform: back }
  ], { duration: D, easing: "linear" });
  setTimeout(() => tLay(old), 120);
  setTimeout(() => { old.style.zIndex = 1; }, D / 2);
  setTimeout(() => { old.style.transition = ""; tLay(); tBusy = false; }, D + 20);
};
const tRestart = () => { clearInterval(tTimer); if (!reduce) tTimer = setInterval(() => tShow((tOrder[0] + 1) % TN), 3000); };
/* البداية: الصور مجمّعة (٣ في كل سطر) لمدة ٣ ثوانٍ، ثم تنتقل إلى رزمة البطاقات */
const tdeck = $("[data-team]");
const tGrid = () => { const W = tdeck.clientWidth, gap = 8, cw = Math.min(110, (W - gap*2) / 3), s = cw / 200, ch = 268 * s;
  const rows = Math.ceil(TN / 3), gh = rows * ch + (rows - 1) * gap;
  cardsBox.style.height = gh + "px";
  tCards.forEach((c,i) => { const col = i % 3, row = Math.floor(i / 3);
    const x = (1 - col) * (cw + gap), y = row * (ch + gap) + ch/2 - 134;
    c.style.zIndex = 10; c.style.opacity = 1; c.style.filter = "none"; c.classList.remove("front");
    c.style.transform = `translate(${x}px, ${y}px) scale(${s})`; }); };
const tStart = () => { tdeck.classList.remove("is-grid"); cardsBox.style.height = "";
  tCards.forEach((c,i) => { c.style.transition = `transform 1s cubic-bezier(.65,0,.25,1.1) ${i * 70}ms, filter .6s, border-radius .6s`; });
  tLay(); setTimeout(() => tCards.forEach(c => c.style.transition = ""), 1500);
  if (!reduce) tBar.classList.add("go"); tRestart(); };
if (reduce) { tLay(); }
else {
  tdeck.classList.add("is-grid"); tCards.forEach(c => c.style.transition = "none"); tGrid(); cardsBox.offsetWidth; tCards.forEach(c => c.style.transition = "");
  const tio = new IntersectionObserver(es => { if (es[0].isIntersecting) { tio.disconnect(); setTimeout(tStart, 3000); } }, { threshold: .4 });
  tio.observe(tdeck);
}

/* ٨) الإنجازات */
const row = $("[data-ach-row]"), fBar = $("[data-filters]"), bar = $("[data-row-bar]");
PAGE.achCats.forEach((c,i) => { const b = document.createElement("button"); b.type="button"; b.textContent=c; b.setAttribute("aria-pressed",i===0); b.onclick=()=>filterAch(c,b); fBar.append(b); });
const achEls = PAGE.achievements.map(a => { const el = tpl("tpl-ach"); $("img",el).src = IMG+a.img; $("img",el).alt = ""; $(".cat",el).textContent = a.c; $("b",el).textContent = a.t;
  el.dataset.c = a.c; el.onclick = () => openLb(IMG+a.img, a.t, a.c); row.append(el); return el; });
function filterAch(c,btn){ $$("button",fBar).forEach(b=>b.setAttribute("aria-pressed",b===btn)); achEls.forEach(el=>el.hidden = c!=="الكل" && el.dataset.c!==c); row.scrollTo({left:0}); rowBar(); }
function rowBar(){ const max = row.scrollWidth - row.clientWidth, w = row.clientWidth/row.scrollWidth; bar.style.width = (w*100)+"%";
  const p = max>0 ? Math.abs(row.scrollLeft)/max : 0; bar.style.transform = `translateX(${-p*(1/w-1)*100}%)`; }
row.addEventListener("scroll", rowBar, {passive:true});
const cardStep = () => { const c = achEls.find(el => !el.hidden); return c ? c.offsetWidth + 12 : 240; };
$("[data-row-next]").onclick = () => row.scrollBy({left:-cardStep(), behavior:"smooth"});
$("[data-row-prev]").onclick = () => row.scrollBy({left: cardStep(), behavior:"smooth"});

/* سحب أفقي بالماوس للصفوف القابلة للتمرير */
function dragScroll(el){ el.classList.add("drag-x"); let down = false, sx = 0, sl = 0, moved = false;
  el.addEventListener("pointerdown", e => { if (e.pointerType !== "mouse" || e.button !== 0) return; down = true; moved = false; sx = e.clientX; sl = el.scrollLeft; });
  addEventListener("pointermove", e => { if (!down) return; const dx = e.clientX - sx; if (!moved && Math.abs(dx) > 5) { moved = true; el.classList.add("dragging"); } if (moved) el.scrollLeft = sl - dx; });
  addEventListener("pointerup", () => { if (!down) return; down = false; el.classList.remove("dragging"); });
  el.addEventListener("click", e => { if (moved) { e.preventDefault(); e.stopPropagation(); moved = false; } }, true); }
[row, chips, fBar].forEach(dragScroll);

/* ===== الرسالة والرؤية والقيم ===== */
(() => {
  const vc = $$(".vmv-card");
  const stars = $("[data-vmv-stars]");
  for (let i = 0; i < 38; i++) { const e = document.createElement("i"); e.style.cssText = `left:${Math.random()*100}%;top:${Math.random()*100}%;animation-delay:${(Math.random()*3).toFixed(2)}s;opacity:${(.3+Math.random()*.6).toFixed(2)}`; stars.append(e); }
  const parts = $("[data-vmv-parts]");
  for (let i = 0; i < 12; i++) { const e = document.createElement("i"); e.style.cssText = `left:${40+Math.random()*20}%;--dx:${(Math.random()*80-40).toFixed(0)}px;animation-delay:${(Math.random()*4).toFixed(2)}s;animation-duration:${(3+Math.random()*2.5).toFixed(2)}s`; parts.append(e); }
  // تظهر القيم تدريجيًا عند الوصول للقسم
  const vio = new IntersectionObserver(es => es.forEach(e => { if (e.isIntersecting) { vc.forEach(c => c.classList.add("seen")); vio.disconnect(); } }), {threshold:.25});
  vio.observe($(".vmv-deck"));
  // لمسة على بطاقة تبرزها، وضوء يتبع الإصبع / المؤشر
  vc.forEach(c => {
    c.addEventListener("pointermove", e => { const r = c.getBoundingClientRect(); c.style.setProperty("--mx", (e.clientX - r.left) + "px"); c.style.setProperty("--my", (e.clientY - r.top) + "px"); });
    c.addEventListener("pointerdown", () => vc.forEach(x => x.classList.toggle("active", x === c)));
  });
})();


/* عجلة الماوس على شريط الأقسام تحرّكه أفقيًا (الشريط رفيع فلا يعيق تمرير الصفحة) */
chips.addEventListener("wheel", e => { if (Math.abs(e.deltaY) <= Math.abs(e.deltaX)) return; e.preventDefault(); chips.scrollBy({left:-e.deltaY}); }, {passive:false});

/* ٩) المكتبة */
const media = $("[data-media]");
PAGE.media.forEach(m => { const el = tpl("tpl-media"); $(".bg",el).src = IMG+m.img; $(".m-in img",el).src = SVG+m.ico; $(".m-in > span",el).textContent = m.t;
  el.onclick = () => m.url ? window.open(m.url, "_blank", "noopener") : openLb(IMG+m.img, m.t, "المكتبة الإعلامية"); media.append(el); });

/* التمرير: التقدّم، القسم الحالي، المسار، الظهور */
const prog = $("[data-progress]"), topBtn = $("[data-top]"), navLinks = $$("a",chips);
const sections = PAGE.nav.map(([h])=>$(h));
/* يحرّك شريط الأقسام أفقيًا فقط، دون تحريك الصفحة */
function centerChip(a){ const r = a.getBoundingClientRect(), u = chips.getBoundingClientRect();
  chips.scrollBy({left:(r.left + r.width/2) - (u.left + u.width/2), behavior: reduce ? "auto" : "smooth"}); }
function onScroll(){ const h = document.documentElement, y = h.scrollTop;
  prog.style.width = (y/(h.scrollHeight-h.clientHeight)*100)+"%"; topBtn.classList.toggle("show", y>600);
  let cur = 0; sections.forEach((s,i)=>{ if (s && s.getBoundingClientRect().top < 150) cur = i; });
  navLinks.forEach((a,i)=>{ const on = i===cur; if (on !== a.classList.contains("on")) { a.classList.toggle("on",on); if(on) centerChip(a); } });
  if (pLen){ const r = pWrap.getBoundingClientRect(), vh = innerHeight; const p = Math.min(1, Math.max(0, (vh*0.7 - r.top)/r.height));
    pFg.style.strokeDashoffset = reduce ? 0 : pLen*(1-p);
    $$(".stop",stopsBox).forEach(s=>{ const nr = $(".node",s).getBoundingClientRect(); s.classList.toggle("reached", nr.top < vh*0.7 || reduce); }); } }
addEventListener("scroll", onScroll, {passive:true});
addEventListener("resize", () => { drawPath(); rowBar(); });
topBtn.onclick = () => scrollTo({top:0,behavior:reduce?"auto":"smooth"});

const io = new IntersectionObserver(es => es.forEach(e => { if (!e.isIntersecting) return; e.target.classList.add("in");
  if (e.target.matches("[data-count]") && !reduce) countUp(e.target); io.unobserve(e.target); }), {threshold:.2});
$$(".reveal,[data-count]").forEach(el => io.observe(el));

addEventListener("load", () => { drawPath(); rowBar(); });
drawPath(); rowBar();

/* الرؤية على الكمبيوتر: إطار الرسم فقط (البطاقة بمقاسها الأصلي) حتى يظهر الشارع والنجمة كاملين */
if (matchMedia('(min-width: 1100px)').matches) { var vs = document.querySelector('.art-vision svg'); if (vs) { vs.setAttribute('viewBox', '-100 62 520 188'); vs.setAttribute('preserveAspectRatio', 'xMidYMax slice'); } }
})();
