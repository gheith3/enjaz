// Ported from design «تصميم 9» js/aj-split.js (kept as in the design; only the icon path and the story image lookup changed).
import omanMap from '../../../assets/svg/icon-oman-map.svg';

(function(){
  function buildMedal(old){
    var dj=document.getElementById('aj-data');if(!old||!dj)return old;
    var NS='http://www.w3.org/2000/svg',C=100,R=84,reduce=matchMedia('(prefers-reduced-motion: reduce)').matches;
    var AR=function(v){return String(v).replace(/\d/g,function(d){return'٠١٢٣٤٥٦٧٨٩'[d]})};
    var esc=function(s){return String(s==null?'':s).replace(/[&<>"]/g,function(c){return{'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]})};
    var A=JSON.parse(dj.textContent).items.slice().sort(function(a,b){return a.iso<b.iso?-1:1});
    var I=A.filter(function(a){return a.scope==='international'}),L=A.filter(function(a){return a.scope!=='international'}),n=A.length;
    var pi=Math.round(I.length*100/n),pl=100-pi,star=A.filter(function(a){return a.featured===1})[0];
    var places=[];A.forEach(function(a){if(a.country&&a.country!=='—'&&places.indexOf(a.country)<0)places.push(a.country)});
    function pts(o,i){var p=[];for(var k=0;k<16;k++){var r=k%2?i:o,a=Math.PI/8*k-Math.PI/2;p.push((C+r*Math.cos(a)).toFixed(2)+','+(C+r*Math.sin(a)).toFixed(2))}return p.join(' ')}
    var len=(2*Math.PI*R).toFixed(1),br=Math.max(4,Math.min(11,Math.PI*R/n*.42)),beads='';
    I.concat(L).forEach(function(a,i){var g=2*Math.PI*i/n-Math.PI/2;beads+='<circle class="km-b '+(a.scope==='international'?'i':'l')+'" cx="'+(C+R*Math.cos(g)).toFixed(2)+'" cy="'+(C+R*Math.sin(g)).toFixed(2)+'" r="'+br.toFixed(2)+'" style="transition-delay:'+(reduce?0:(.6+i*.12).toFixed(2))+'s"><title>'+esc(a.short||a.title)+'</title></circle>'});
    var svg='<svg class="km-svg" viewBox="0 0 200 200" aria-hidden="true"><defs><linearGradient id="km-g" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#FFF1D2"/><stop offset=".35" stop-color="#C9A35C"/><stop offset=".6" stop-color="#F4DFAE"/><stop offset="1" stop-color="#9C7638"/></linearGradient><radialGradient id="km-c" cx=".5" cy=".4" r=".7"><stop offset="0" stop-color="#0E7C85"/><stop offset="1" stop-color="#04262A"/></radialGradient></defs>'+
      '<circle class="km-tbg" cx="100" cy="100" r="'+R+'"/><circle class="km-th" cx="100" cy="100" r="'+R+'" style="--len:'+len+'"/>'+
      '<g class="km-star"><polygon points="'+pts(58,49)+'" fill="url(#km-c)" stroke="url(#km-g)" stroke-width="2"/><polygon class="km-si" points="'+pts(50,42.5)+'"/></g>'+beads+
      '<g class="km-sp"><circle cx="100" cy="'+(C-R)+'" r="4" fill="#FFF7E3" style="filter:drop-shadow(0 0 5px #FFE2A0)"/>'+(reduce?'':'<animateTransform attributeName="transform" type="rotate" from="0 100 100" to="360 100 100" dur="9s" repeatCount="indefinite"/>')+'</g></svg>';
    var row=function(k,arr,p,ico,lbl){return '<div class="km-row '+k+'"><div class="km-rh"><span class="km-ic">'+ico+'</span><b data-n="'+arr.length+'">٠</b><span class="km-rl">'+lbl+'</span><i data-n="'+p+'" data-s="٪">٠٪</i></div><div class="km-dots">'+arr.map(function(a,j){return '<span class="km-d" tabindex="0" style="--j:'+j+'"><em>'+esc(a.short||a.title)+'</em></span>'}).join('')+'</div></div>'};
    var globe='<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><ellipse cx="12" cy="12" rx="4.5" ry="9"/><path d="M3 12h18"/></svg>';
    var m=document.createElement('div');m.className='km';m.setAttribute('aria-label','إحصاءات الإنجازات: '+n+' إجمالًا، '+I.length+' دولية و'+L.length+' محلية');
    m.innerHTML='<div class="km-top"><div class="km-badge">'+svg+'<strong data-n="'+n+'">٠</strong></div><div class="km-tt"><em>إجمالي الإنجازات</em><small>'+AR(I.length)+' دولية · '+AR(L.length)+' محلية</small></div></div>'+
      '<div class="km-rows">'+row('i',I,pi,globe,'إنجازات دولية')+row('l',L,pl,'<img src="'+omanMap+'" alt="">','إنجازات محلية')+'</div>';
    old.replaceWith(m);
    function count(el){var to=+el.dataset.n,suf=el.dataset.s||'',t0=null,D=reduce?1:1300;(function f(t){t0=t0||t;var x=Math.min(1,(t-t0)/D);el.textContent=AR(Math.round(to*(1-Math.pow(1-x,3))))+suf;if(x<1)requestAnimationFrame(f)})(performance.now())}
    function play(){m.classList.add('on');[].forEach.call(m.querySelectorAll('[data-n]'),count)}
    if('IntersectionObserver'in window&&!reduce){var io=new IntersectionObserver(function(e){if(e[0].isIntersecting){play();io.disconnect()}},{threshold:.3});io.observe(m)}else play();
    return m;
  }
  var story=document.querySelector('[data-story]'),ks=buildMedal(document.querySelector('.ks'));
  if(story&&ks){var w=document.createElement('div');w.className='sk';story.parentNode.insertBefore(w,story);w.appendChild(story);w.appendChild(ks)}
  var txt=document.getElementById('story-text'),btn=story&&story.querySelector('.toggle');if(!txt||!btn)return;
  btn.querySelector('span').textContent='اقرأ القصة كاملة';btn.setAttribute('aria-haspopup','dialog');btn.removeAttribute('aria-expanded');
  var img=document.querySelector('.jp-side img, .profile-hero img');
  var name=(document.querySelector('h1')||{}).textContent||'';
  /* الفقرات كما في البطاقة (يقسّمها الخادم عند نهايات الجمل)، وإلا فثلاث جمل في كل فقرة */
  var blocks=txt.textContent.trim().split(/\n\s*\n/).map(function(b){return b.replace(/\s+/g,' ').trim()}).filter(Boolean),paras=[];
  if(blocks.length>1)paras=blocks;else{var sents=(blocks[0]||'').split(/(?<=[.؟!])\s+/),cur=[];sents.forEach(function(s,i){cur.push(s);if(cur.length>=3||i===sents.length-1){paras.push(cur.join(' '));cur=[]}})}
  var words=txt.textContent.trim().split(/\s+/).length,mins=Math.max(1,Math.round(words/180));
  var AR=function(v){return String(v).replace(/\d/g,function(d){return'٠١٢٣٤٥٦٧٨٩'[d]})};
  var m=document.createElement('div');m.className='sr';m.setAttribute('role','dialog');m.setAttribute('aria-modal','true');m.setAttribute('aria-label','قصتي');
  m.innerHTML='<article class="sr-pg"><header class="sr-hd">'+(img?'<img src="'+img.getAttribute('src')+'" alt="">':'')+'<div><small>قصتي</small><h3>'+name.trim()+'</h3></div><button type="button" class="sr-x" aria-label="إغلاق">×</button><span class="sr-prog"></span></header><div class="sr-bd"></div><footer class="sr-ft"><span>'+AR(mins)+' دقائق قراءة · '+AR(words)+' كلمة</span><button type="button" data-close>العودة إلى الصفحة</button></footer></article>';
  var bd=m.querySelector('.sr-bd');paras.forEach(function(p){var e=document.createElement('p');e.textContent=p;bd.appendChild(e)});
  document.body.appendChild(m);
  var pg=m.querySelector('.sr-pg'),prog=m.querySelector('.sr-prog');
  pg.addEventListener('scroll',function(){var h=pg.scrollHeight-pg.clientHeight;prog.style.width=(h>0?pg.scrollTop/h*100:100)+'%'});
  function open(){m.classList.add('open');document.body.classList.add('sr-lock');pg.scrollTop=0;prog.style.width='0';m.querySelector('.sr-x').focus()}
  function close(){m.classList.remove('open');document.body.classList.remove('sr-lock');btn.focus()}
  btn.addEventListener('click',function(e){e.preventDefault();e.stopImmediatePropagation();open()},true);
  m.addEventListener('click',function(e){if(e.target===m||e.target.closest('.sr-x,[data-close]'))close()});
  document.addEventListener('keydown',function(e){if(e.key==='Escape'&&m.classList.contains('open'))close()});
})();
document.addEventListener('mouseover',function(e){var r=e.target.closest&&e.target.closest('.km-row'),m=document.querySelector('.km');if(!m)return;if(r)m.setAttribute('data-f',r.classList.contains('i')?'i':'l');else m.removeAttribute('data-f')});
