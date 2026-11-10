/* وسام الإنجاز — يرسم الوسام من الأرقام في data-total / data-intl / data-local */
(function () {
  var NS = "http://www.w3.org/2000/svg";
  var C = 100, RING = 84, MAX_BEADS = 40;
  var reduce = window.matchMedia && matchMedia("(prefers-reduced-motion: reduce)").matches;
  var ar = function (n) { return String(n).replace(/\d/g, function (d) { return "٠١٢٣٤٥٦٧٨٩"[d]; }); };

  function el(name, attrs, parent) {
    var node = document.createElementNS(NS, name);
    for (var k in attrs) node.setAttribute(k, attrs[k]);
    if (parent) parent.appendChild(node);
    return node;
  }

  // نجمة ثمانية (نفس شكل علامة «مميز» في الموقع)
  function starPoints(outer, inner) {
    var pts = [];
    for (var i = 0; i < 16; i++) {
      var r = i % 2 ? inner : outer, a = (Math.PI / 8) * i - Math.PI / 2;
      pts.push((C + r * Math.cos(a)).toFixed(2) + "," + (C + r * Math.sin(a)).toFixed(2));
    }
    return pts.join(" ");
  }

  function countUp(node, to, delay) {
    if (reduce || to <= 0) { node.textContent = ar(to); return; }
    var start = null, dur = 1100;
    setTimeout(function () {
      requestAnimationFrame(function step(t) {
        if (!start) start = t;
        var p = Math.min(1, (t - start) / dur), v = Math.round(to * (1 - Math.pow(1 - p, 3)));
        node.textContent = ar(v);
        if (p < 1) requestAnimationFrame(step);
      });
    }, delay);
  }

  function build(box) {
    var total = +box.dataset.total || 0, intl = +box.dataset.intl || 0, local = +box.dataset.local || 0;
    var svg = box.querySelector(".ms-svg");
    if (!svg || !total) return;

    var defs = el("defs", {}, svg);
    var g = el("linearGradient", { id: "ms-gold-grad", x1: "0", y1: "0", x2: "1", y2: "1" }, defs);
    [["0", "#FFF1D2"], [".35", "#C9A35C"], [".6", "#F4DFAE"], ["1", "#9C7638"]].forEach(function (s) {
      el("stop", { offset: s[0], "stop-color": s[1] }, g);
    });
    var rg = el("radialGradient", { id: "ms-core-grad", cx: ".5", cy: ".4", r: ".7" }, defs);
    el("stop", { offset: "0", "stop-color": "#0E7C85" }, rg);
    el("stop", { offset: "1", "stop-color": "#04262A" }, rg);

    // الخيط الخلفي والخيط الذهبي
    el("circle", { class: "ms-thread-bg", cx: C, cy: C, r: RING }, svg);
    var len = (2 * Math.PI * RING).toFixed(1);
    var thread = el("circle", { class: "ms-thread", cx: C, cy: C, r: RING }, svg);
    thread.style.setProperty("--len", len);

    // النجمة
    var star = el("g", { class: "ms-star" }, svg);
    el("polygon", { points: starPoints(58, 49), fill: "url(#ms-core-grad)", stroke: "url(#ms-gold-grad)", "stroke-width": "2" }, star);
    el("polygon", { class: "ms-star-inner", points: starPoints(50, 42.5) }, star);

    // الخرز: واحدة لكل إنجاز (أو بالنسبة إذا كثرت الإنجازات)
    var n = Math.min(total, MAX_BEADS);
    var nIntl = total > MAX_BEADS ? Math.round((intl / total) * n) : Math.min(intl, n);
    var beadR = Math.max(4, Math.min(11, ((Math.PI * RING) / n) * 0.42));
    var beads = el("g", {}, svg);
    for (var i = 0; i < n; i++) {
      var a = (2 * Math.PI * i) / n - Math.PI / 2;
      var x = C + RING * Math.cos(a), y = C + RING * Math.sin(a);
      var kind = i < nIntl ? "intl" : "local";
      var b = el("circle", { class: "ms-bead " + kind, cx: x.toFixed(2), cy: y.toFixed(2), r: beadR.toFixed(2) }, beads);
      b.style.transitionDelay = reduce ? "0s" : (0.5 + i * (0.9 / n)).toFixed(2) + "s";
    }

    // شرارة تدور على الخيط
    var spark = el("g", { class: "ms-spark" }, svg);
    el("circle", { cx: C, cy: C - RING, r: "4", fill: "#FFF7E3", style: "filter:drop-shadow(0 0 5px #FFE2A0)" }, spark);
    if (!reduce) el("animateTransform", { attributeName: "transform", type: "rotate", from: "0 100 100", to: "360 100 100", dur: "9s", repeatCount: "indefinite" }, spark);

    // النسب
    box.querySelectorAll("[data-ms-pct]").forEach(function (node) {
      var v = node.dataset.msPct === "intl" ? intl : local;
      node.textContent = ar(Math.round((v / total) * 100)) + "٪";
    });

    // خيط الخرز في كل صف: خرزة لكل إنجاز (حتى ١٢)، والممتلئ بلون الفئة
    var slots = Math.min(total, 12);
    box.querySelectorAll("[data-ms-beads]").forEach(function (strip, r) {
      var v = strip.dataset.msBeads === "intl" ? intl : local;
      var on = Math.round((v / total) * slots);
      for (var j = 0; j < slots; j++) {
        var d = document.createElement("i");
        if (j < on) d.className = "on";
        d.style.transitionDelay = reduce ? "0s" : (0.6 + r * 0.25 + j * 0.07).toFixed(2) + "s";
        strip.appendChild(d);
      }
    });

    // الضغط على صف يُبرز خرزاته
    box.querySelectorAll(".ms-row").forEach(function (row) {
      row.addEventListener("click", function () {
        var on = row.getAttribute("aria-pressed") !== "true";
        box.querySelectorAll(".ms-row").forEach(function (r) { r.setAttribute("aria-pressed", "false"); });
        row.setAttribute("aria-pressed", on ? "true" : "false");
        if (on) box.dataset.focus = row.dataset.kind; else delete box.dataset.focus;
      });
    });

    // تبدأ الأرقام من الصفر ثم تعدّ عند الظهور
    if (!reduce) box.querySelectorAll("[data-ms-count],[data-ms-num]").forEach(function (node) { node.textContent = "٠"; });

    function play() {
      box.classList.add("is-on");
      countUp(box.querySelector("[data-ms-count]"), total, 200);
      box.querySelectorAll("[data-ms-num]").forEach(function (node, i) { countUp(node, +node.dataset.msNum, 500 + i * 200); });
    }
    if (reduce || !("IntersectionObserver" in window)) { play(); return; }
    var io = new IntersectionObserver(function (entries) {
      if (entries[0].isIntersecting) { play(); io.disconnect(); }
    }, { threshold: 0.35 });
    io.observe(box);
  }

  document.querySelectorAll("[data-medal-stats]").forEach(build);
})();
