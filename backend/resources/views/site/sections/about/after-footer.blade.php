{{-- about.html: back to top and the image viewer (inside .page, after the footer). --}}
<button class="to-top" type="button" aria-label="{{ __('site.about.to_top') }}" data-top><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M6 15l6-6 6 6"/></svg></button>

<dialog class="lb" data-lb aria-label="{{ __('site.about.image_view') }}">
  <button class="x" type="button" aria-label="{{ __('site.about.close') }}" data-lb-close><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg></button>
  <img alt="" data-lb-img>
  <div class="lb-body"><b data-lb-title></b><small data-lb-sub></small><span class="lb-en" lang="en" data-lb-en hidden></span></div>
</dialog>
