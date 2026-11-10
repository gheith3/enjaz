/* الربط الديناميكي بين المؤسسات (الجامعات والكليات) والمنجزين والإنجازات.
   تُقرأ صفحتا الإنجازات والمنجزين مرة واحدة، فلا يُكرَّر أي إنجاز يدويًا. نوعان من العلاقة:
   1) إنجاز للمؤسسة نفسها: بطاقة الإنجاز تحمل data-orgs="squ" (المؤسسة طرف في الإنجاز).
   2) انتساب شخص للمؤسسة: بطاقة المنجز تحمل
        data-person="jokha-alharthi" data-affil="squ|staff|أستاذة مشاركة في قسم اللغة العربية"
      (عدة انتسابات تُفصل بـ ; والنوع staff أو alum أو student).
      كل إنجازات هذا الشخص (بطاقات data-person بالقيمة نفسها) تُعرض في صفحة المؤسسة تحت «إنجازات منتسبيها»
      مع توضيح أنها إنجاز شخصي، حتى لو لم يكن للمؤسسة دور فيه (مثل فوز أستاذة جامعية بجائزة أدبية). */
window.InjazLinks = (function () {
  var cache = {};
  function load(page) {
    if (!cache[page]) cache[page] = fetch(page, { cache: 'no-cache' })
      .then(function (r) { if (!r.ok) throw new Error(page); return r.text(); })
      .then(function (h) { return new DOMParser().parseFromString(h, 'text/html'); });
    return cache[page];
  }
  function parseAffil(s) {
    return String(s || '').split(';').map(function (x) { return x.trim(); }).filter(Boolean).map(function (x) {
      var p = x.split('|'); return { org: p[0].trim(), kind: (p[1] || '').trim(), role: (p[2] || '').trim() };
    });
  }
  var KIND = { staff: ['هيئة التدريس', 'هيئة التدريس'], alum: ['خريج', 'خريجة'], student: ['طالب', 'طالبة'] };
  function kindLabel(kind, gender) { var k = KIND[kind]; return k ? k[gender === 'أنثى' ? 1 : 0] : 'منتسب'; }
  var txt = function (el, s) { var x = el.querySelector(s); return x ? x.textContent.trim() : ''; };
  var attr = function (el, s, a) { var x = el.querySelector(s); return x ? x.getAttribute(a) || '' : ''; };
  var hasOrg = function (el, slug) { return (' ' + (el.getAttribute('data-orgs') || '') + ' ').indexOf(' ' + slug + ' ') > -1; };

  function person(el, a) {
    return { id: el.getAttribute('data-person') || txt(el, 'h3'), name: txt(el, 'h3'), href: attr(el, '.ach-link', 'href'),
      img: attr(el, '.ach-img', 'src'), style: attr(el, '.ach-img', 'style'), headline: txt(el, '.ach-headline'),
      gender: el.getAttribute('data-gender') || '', cat: el.getAttribute('data-cat') || '', meta: txt(el, '.ach-meta'), affil: a, kindLabel: kindLabel(a.kind, el.getAttribute('data-gender')) };
  }
  function ach(el, who) {
    return { title: txt(el, 'h3'), href: attr(el, '.ach-link', 'href'), img: attr(el, '.ach-img', 'src'), style: attr(el, '.ach-img', 'style'),
      who: txt(el, '.ach-meta span'), when: txt(el, 'time'), date: el.getAttribute('data-date') || '', cat: el.getAttribute('data-cat') || '', by: who || null };
  }
  var real = function (list) { return [].filter.call(list, function (el) { return !el.hasAttribute('data-sample'); }); };

  // كل ما يرتبط بمؤسسة واحدة: { people, own, members }
  function forOrg(slug) {
    return Promise.all([load('achievements.html'), load('achievers.html')]).then(function (d) {
      var people = [], seen = {};
      real(d[1].querySelectorAll('[data-results] > .person-card')).forEach(function (el) {
        var a = parseAffil(el.getAttribute('data-affil')).filter(function (x) { return x.org === slug; })[0];
        if (!a) return; var p = person(el, a); if (seen[p.id]) return; seen[p.id] = p; people.push(p);
      });
      var own = [], members = [];
      real(d[0].querySelectorAll('[data-results] > .ach')).forEach(function (el) {
        if (hasOrg(el, slug)) own.push(ach(el));
        else { var p = seen[el.getAttribute('data-person') || '']; if (p) members.push(ach(el, p)); }
      });
      var byDate = function (a, b) { return b.date.localeCompare(a.date); };
      own.sort(byDate); members.sort(byDate);
      return { people: people, own: own, members: members };
    });
  }
  // ملخص لكل المؤسسات (لصفحة القائمة): { slug: { people, own, members } } أعداد فقط
  function summary() {
    return Promise.all([load('achievements.html'), load('achievers.html')]).then(function (d) {
      var out = {}, owner = {}, get = function (s) { return out[s] || (out[s] = { people: 0, own: 0, members: 0 }); };
      real(d[1].querySelectorAll('[data-results] > .person-card')).forEach(function (el) {
        var id = el.getAttribute('data-person'); if (!id || owner[id]) return;
        owner[id] = parseAffil(el.getAttribute('data-affil')).map(function (a) { return a.org; });
        owner[id].forEach(function (s) { get(s).people++; });
      });
      real(d[0].querySelectorAll('[data-results] > .ach')).forEach(function (el) {
        (el.getAttribute('data-orgs') || '').split(/\s+/).filter(Boolean).forEach(function (s) { get(s).own++; });
        (owner[el.getAttribute('data-person') || ''] || []).forEach(function (s) { if (!hasOrg(el, s)) get(s).members++; });
      });
      return out;
    });
  }
  // كل إنجازات المؤسسة في قائمة واحدة مصنّفة (لمربع الأخبار وصفحة «إنجازات الجامعة»):
  // يدخل الإنجاز إذا وُسم بالمؤسسة في لوحة التحكم (data-orgs="squ")، أو كان صاحبه منتسبًا لها (data-affil).
  // النوع: org المؤسسة نفسها (بلا شخص)، staff أكاديميون، student طلبة وخريجون. ويمكن تحديده يدويًا على البطاقة بـ data-role.
  var TYPE = { org: 'المؤسسة', student: 'طلبة', staff: 'أكاديميون' };
  function achievementsFor(slug) {
    return forOrg(slug).then(function (d) {
      var people = {}; d.people.forEach(function (p) { people[p.id] = p; });
      return load('achievements.html').then(function (doc) {
        var out = [];
        real(doc.querySelectorAll('[data-results] > .ach')).forEach(function (el) {
          var pid = el.getAttribute('data-person') || '', p = people[pid];
          if (!hasOrg(el, slug) && !p) return;
          var role = el.getAttribute('data-role') || (p ? (p.affil.kind === 'staff' ? 'staff' : 'student') : pid ? 'student' : 'org');
          var a = ach(el, p);
          a.type = role; a.typeLabel = TYPE[role] || TYPE.org;
          a.gender = el.getAttribute('data-gender') || (p && p.gender) || '';
          a.desc = txt(el, '.desc');
          out.push(a);
        });
        return out.sort(function (a, b) { return b.date.localeCompare(a.date); });
      });
    });
  }
  return { load: load, parseAffil: parseAffil, kindLabel: kindLabel, forOrg: forOrg, summary: summary, achievementsFor: achievementsFor, TYPE: TYPE };
})();
