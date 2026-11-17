// Ported from design «تصميم 9» js/aj-pro.js (kept as in the design): stats bar from #aj-data and the journey timeline.
(function(){
  var AR=function(v){return String(v).replace(/\d/g,function(d){return'٠١٢٣٤٥٦٧٨٩'[d]})};
  var esc=function(s){return String(s==null?'':s).replace(/[&<>"]/g,function(c){return{'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]})};
  var reduce=matchMedia('(prefers-reduced-motion: reduce)').matches;
  function seen(el,fn){if('IntersectionObserver'in window){var io=new IntersectionObserver(function(es){if(es[0].isIntersecting){fn();io.disconnect()}},{threshold:.3});io.observe(el)}else fn()}

  /* ===== العدّاد ===== */
  (function(){
    var old=document.querySelector('.kc'),dj=document.getElementById('aj-data'); if(!old||!dj)return;
    var A=JSON.parse(dj.textContent).items.slice().sort(function(a,b){return a.iso<b.iso?-1:1});
    var I=A.filter(function(a){return a.scope==='international'}),L=A.filter(function(a){return a.scope!=='international'}),n=A.length;
    var pi=Math.round(I.length*100/n),pl=100-pi,ys=A.map(function(a){return +a.iso.slice(0,4)}),y0=Math.min.apply(null,ys),y1=Math.max.apply(null,ys);
    var places=[];A.forEach(function(a){if(a.country&&a.country!=='—'&&places.indexOf(a.country)<0)places.push(a.country)});
    var star=A.filter(function(a){return a.featured===1})[0];
    var seg=function(a,i){return '<button type="button" class="ks-seg '+(a.scope==='international'?'i':'l')+(a===star?' star':'')+'" style="--i:'+i+'" data-go="'+a.slug+'" aria-label="'+esc(a.short||a.title)+'"><span>'+esc(a.short||a.title)+' · '+AR(a.iso.slice(0,4))+'</span></button>'};
    var d=document.createElement('div');d.className='ks';
    d.innerHTML='<button type="button" class="ks-total" data-st="all"><span class="ks-ring"><svg viewBox="0 0 120 120" aria-hidden="true"><circle class="bg" cx="60" cy="60" r="52"/><circle class="fg i" cx="60" cy="60" r="52" pathLength="100" style="--p:'+pi+'"/><circle class="fg l" cx="60" cy="60" r="52" pathLength="100" style="--p:'+pl+';--o:'+pi+'"/></svg><b data-n="'+n+'">٠</b></span><span><em>إجمالي الإنجازات</em><small>'+AR(y0)+' – '+AR(y1)+'</small></span></button>'+
      '<div class="ks-mid"><div class="ks-leg"><button type="button" class="ks-lg i" data-st="intl"><b data-n="'+I.length+'">٠</b>دولية<i>'+AR(pi)+'٪</i></button><button type="button" class="ks-lg l" data-st="local"><i>'+AR(pl)+'٪</i>محلية<b data-n="'+L.length+'">٠</b></button></div>'+
      '<div class="ks-bar">'+I.concat(L).map(seg).join('')+'</div><p class="ks-tip">كل شريحة تمثّل إنجازًا — مرّر عليها لمعرفته واضغط لفتحه</p></div>'+
      '<div class="ks-facts"><div class="ks-f"><b data-n="'+places.length+'">٠</b><small>دول</small></div><div class="ks-f"><b data-n="'+(y1-y0+1)+'">٠</b><small>أعوام</small></div>'+
      (star?'<button type="button" class="ks-star" data-go="'+star.slug+'"><small>✦ الإنجاز الأبرز</small><b>'+esc(star.short||star.title)+'</b></button>':'')+'</div>';
    old.replaceWith(d);
    d.addEventListener('click',function(e){var t=e.target.closest('[data-go]');if(!t)return;var dot=document.querySelector('[data-aj] [data-dot="'+t.dataset.go+'"]');if(dot)dot.click()});
    seen(d,function(){d.classList.add('on');[].forEach.call(d.querySelectorAll('[data-n]'),function(el){var to=+el.dataset.n,t0=null,D=reduce?1:1300;
      (function f(t){t0=t0||t;var x=Math.min(1,(t-t0)/D);el.textContent=AR(Math.round(to*(1-Math.pow(1-x,3))));if(x<1)requestAnimationFrame(f)})(performance.now())})});
  })();

  /* ===== مسيرتي ===== */
  (function(){
    var sec=document.getElementById('journey'),gj=sec&&sec.querySelector('[data-journey-gold]'); if(!gj)return;
    var IC={book:'<path d="M3 6c3-1.5 6-1.5 9 0v13c-3-1.5-6-1.5-9 0zM12 6c3-1.5 6-1.5 9 0v13c-3-1.5-6-1.5-9 0z"/>',cap:'<path d="M2 9l10-5 10 5-10 5zM6 11v5c3 2.5 9 2.5 12 0v-5M22 9v6"/>',doc:'<path d="M6 3h9l3 3v15H6zM9 9h6M9 13h6M9 17h4"/>',feather:'<path d="M20 4C12 4 6 10 6 18l-2 2M6 18h7c4-3 7-8 7-14M9 15h5M11 11h5"/>',uni:'<path d="M3 9l9-5 9 5zM5 9v8M9 9v8M15 9v8M19 9v8M3 20h18M4 17h16"/>',trophy:'<path d="M7 4h10v5a5 5 0 0 1-10 0zM7 6H4c0 3 1.5 4.5 3.5 5M17 6h3c0 3-1.5 4.5-3.5 5M12 14v4M8 21h8M9 18h6"/>',star:'<path d="M12 3l2.7 5.6 6.1.8-4.5 4.2 1.1 6.1L12 16.8 6.6 19.7l1.1-6.1L3.2 9.4l6.1-.8z"/>',medal:'<path d="M8 3h8l-2.5 6h-3zM12 9.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11z"/>'};
    var CAT={book:'إصدارات',doc:'إصدارات',feather:'إصدارات',cap:'تعليم',uni:'مسار مهني',trophy:'جوائز',star:'جوائز',medal:'جوائز'};
    var it=[].map.call(gj.querySelectorAll('.gj-list li'),function(li){var y=li.querySelector('.gj-y').textContent.trim().replace(/[٠-٩]/g,function(d){return'٠١٢٣٤٥٦٧٨٩'.indexOf(d)});
      return{ic:li.dataset.icon,t:li.querySelector('.gj-t').textContent.trim(),y:+y,cat:CAT[li.dataset.icon]||'محطات'}});
    var n=it.length; if(!n)return;
    var img=gj.querySelector('.gj-ph'),lead=gj.querySelector('.gj-lead');
    var y0=it[0].y,y1=it[n-1].y,span=Math.max(1,y1-y0),used={};
    it.forEach(function(x){var p=2+(x.y-y0)/span*96;while(used[p.toFixed(1)])p+=1.6;used[p.toFixed(1)]=1;x.p=p});
    var cats=[];it.forEach(function(x){if(cats.indexOf(x.cat)<0)cats.push(x.cat)});
    var ticks='';for(var y=Math.ceil(y0/5)*5;y<=y1;y+=5)ticks+='<span class="jp-tk" style="right:'+(2+(y-y0)/span*96)+'%">'+AR(y)+'</span>';
    var d=document.createElement('div');d.className='jp';
    d.innerHTML='<div class="jp-side">'+(lead?'<div>'+lead.innerHTML+'</div>':'')+'<div class="jp-now" aria-live="polite"></div>'+(img?'<img src="'+img.getAttribute('src')+'" alt="'+esc(img.alt||'')+'">':'')+'</div>'+
      '<div class="jp-main"><div class="jp-head"><div class="jp-chips"><button type="button" class="jp-chip on" data-f="">الكل<b>'+AR(n)+'</b></button>'+cats.map(function(c){return '<button type="button" class="jp-chip" data-f="'+c+'">'+c+'<b>'+AR(it.filter(function(x){return x.cat===c}).length)+'</b></button>'}).join('')+'</div>'+
      '<div class="jp-ctl"><button type="button" class="jp-arw" data-s="-1" aria-label="السابق">→</button><span></span><button type="button" class="jp-arw" data-s="1" aria-label="التالي">←</button></div></div>'+
      '<div class="jp-rule"><i class="jp-fill"></i>'+ticks+it.map(function(x,i){return '<button type="button" class="jp-dot" style="right:'+x.p+'%" data-i="'+i+'" title="'+esc(x.t)+' — '+AR(x.y)+'" aria-label="'+esc(x.t)+' — '+AR(x.y)+'"></button>'}).join('')+'</div>'+
      '<div class="jp-grid" style="--c:'+Math.ceil(n/2)+'">'+it.map(function(x,i){return '<button type="button" class="jp-c" style="--i:'+i+'" data-i="'+i+'"><span class="jp-c-top"><span class="jp-ic"><svg viewBox="0 0 24 24">'+(IC[x.ic]||IC.book)+'</svg></span><b class="jp-y">'+AR(x.y)+'</b></span><span class="jp-t">'+esc(x.t)+'</span><span class="jp-k">'+x.cat+'</span></button>'}).join('')+'</div></div>';
    gj.parentNode.insertBefore(d,gj.nextSibling);
    var cur=0,f='',timer,now=d.querySelector('.jp-now'),fill=d.querySelector('.jp-fill'),cnt=d.querySelector('.jp-ctl span');
    var ok=function(i){return !f||it[i].cat===f};
    function set(i){cur=i;[].forEach.call(d.querySelectorAll('[data-i]'),function(e){var k=+e.dataset.i;e.classList.toggle('cur',k===cur);e.classList.toggle('done',k<cur);e.classList.toggle('m',ok(k))});
      fill.style.width='calc('+it[cur].p+'% + 8px)';cnt.textContent=AR(cur+1)+' / '+AR(n);
      now.innerHTML='<u>'+it[cur].cat+'</u><b>'+AR(it[cur].y)+'</b><span>'+esc(it[cur].t)+'</span>'}
    function step(s){var i=cur;for(var k=0;k<n;k++){i=(i+s+n)%n;if(ok(i))break}set(i)}
    function run(){clearInterval(timer);if(reduce)return;timer=setInterval(function(){step(1)},3000)}
    d.addEventListener('click',function(e){var t=e.target.closest('[data-i],[data-s],.jp-chip');if(!t)return;clearInterval(timer);
      if(t.classList.contains('jp-chip')){f=t.dataset.f;[].forEach.call(d.querySelectorAll('.jp-chip'),function(c){c.classList.toggle('on',c===t)});if(f)d.setAttribute('data-f',f);else d.removeAttribute('data-f');if(!ok(cur))step(1);else set(cur)}
      else if(t.dataset.i!=null)set(+t.dataset.i);else step(+t.dataset.s)});
    d.addEventListener('mouseenter',function(){clearInterval(timer)});d.addEventListener('mouseleave',run);
    set(0);seen(d,function(){d.classList.add('on');run()});
  })();
})();
