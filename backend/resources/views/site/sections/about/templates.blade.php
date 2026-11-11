{{-- about.html: the templates the page script fills. --}}
<template id="tpl-stat"><div class="stat"><b data-count></b><span data-label></span></div></template>

<template id="tpl-field">
  <button class="field-card" type="button"><span class="ico"><img alt=""></span><img class="ph" alt=""><span class="lbl"></span></button>
</template>

<template id="tpl-year"><li class="year"><b></b></li></template>

<template id="tpl-stop">
  <li class="stop"><span class="node" aria-hidden="true"></span>
    <button class="sq" type="button"><img alt="" loading="lazy"></button>
    <div class="tx"><span class="date"></span><span class="t"></span><span class="en" lang="en"></span></div>
  </li>
</template>

<template id="tpl-quote">
  <figure class="quote"><div class="band" aria-hidden="true"><span class="qm">”</span></div><div class="portrait"><img alt="" loading="lazy"></div><blockquote style="margin:0"><p></p></blockquote><figcaption class="who"><b></b><span></span></figcaption></figure>
</template>

<template id="tpl-member">
  <button class="member" type="button"><div class="ph"><img alt="" loading="lazy"></div><b></b><small></small></button>
</template>

<template id="tpl-ach">
  <button class="ach" type="button"><figure><img alt="" loading="lazy"><span class="cat"></span></figure><div class="body"><b></b><span class="go">{{ __('site.about.view_image') }} <span aria-hidden="true">←</span></span></div></button>
</template>

<template id="tpl-media">
  <button type="button"><img class="bg" alt="" loading="lazy"><span class="m-in"><i><img alt=""></i><span></span></span></button>
</template>
