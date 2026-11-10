@props(['achievement', 'achiever' => null, 'photo' => null])
{{-- Floating share button, share sheet and the "بطاقة الإنجاز" image card (drawn by resources/site/js/sections/achievement-detail.js). --}}
@php
    $url = $achievement->url();
    $message = $achievement->title."\n".$url;
    $general = app(\App\Settings\GeneralSettings::class);
@endphp
<button type="button" class="fab-share" data-share-open aria-haspopup="dialog" aria-controls="share-sheet" aria-label="{{ __('site.achievements.share') }}">
  <img src="{{ Vite::asset('resources/site/assets/svg/icon-share.svg') }}" alt=""><span>{{ __('site.achievements.share_short') }}</span>
</button>
<dialog class="share-sheet" id="share-sheet" aria-labelledby="share-title">
  <p id="share-title">{{ __('site.achievements.share_title') }}</p>
  <div class="share-grid">
    <a href="https://wa.me/?text={{ rawurlencode($message) }}" target="_blank" rel="noopener noreferrer"><span class="sh-ico wa"><x-site.social-icon platform="whatsapp" /></span>{{ __('site.achievements.whatsapp') }}</a>
    <a href="https://twitter.com/intent/tweet?text={{ rawurlencode($achievement->title) }}&url={{ rawurlencode($url) }}" target="_blank" rel="noopener noreferrer"><span class="sh-ico x"><svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h4l12 16h-4ZM20 4 4 20"/></svg></span>X</a>
    <button type="button" data-copy-link><span class="sh-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M10 14a4.5 4.5 0 0 0 6.4 0l3-3a4.5 4.5 0 0 0-6.4-6.4l-1 1"/><path d="M14 10a4.5 4.5 0 0 0-6.4 0l-3 3a4.5 4.5 0 0 0 6.4 6.4l1-1"/></svg></span><b data-copy-label>{{ __('site.achievements.copy_link') }}</b></button>
    @if ($photo)
      <button type="button" data-acard-open><span class="sh-ico gold"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="3" width="16" height="18" rx="2.5"/><path d="M8 15l3-3 2 2 3-3M8 7h5"/></svg></span>{{ __('site.achievements.card') }}</button>
    @endif
  </div>
  <button type="button" class="sheet-close" data-share-close>{{ __('site.achievements.close') }}</button>
</dialog>

@if ($photo)
  <dialog class="idcard-dialog acard-dialog" id="acard" aria-label="{{ __('site.achievements.card') }}">
    <div class="idcard-stage">
      <article class="idcard acard" data-acard-sheet
        data-photo="{{ $photo }}"
        data-avatar="{{ $achiever?->imageUrl('cutout') }}"
        data-name="{{ $achiever?->name }}"
        data-year="{{ \App\Support\ArabicDate::digits($achievement->achieved_at?->year ?? '') }}"
        data-scope="{{ $achievement->scope?->achievement_label ?: $achievement->scope?->name }}"
        data-field="{{ $achievement->field?->name }}"
        data-title="{{ $achievement->title }}"
        data-site="{{ $general->t('site_name') }}"
        data-logo="{{ \App\Support\Site\SiteMedia::url($general->logo, 'svg/logo.svg') }}"
        data-mark="{{ Vite::asset('resources/site/assets/svg/watermark.svg') }}"></article>
      <div class="idcard-side">
        <button type="button" data-acard-pdf><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 3h7l5 5v13H7z"/><path d="M14 3v5h5M10 13h6M10 17h4"/></svg></span>{{ __('site.achievements.pdf') }}</button>
        <button type="button" data-acard-image><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="3"/><circle cx="9" cy="10" r="2"/><path d="m21 16-5-5-9 9"/></svg></span>{{ __('site.achievements.image') }}</button>
        <button type="button" data-acard-share><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 15V4"/><path d="m8 7 4-4 4 4"/><path d="M6 11H5a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7a2 2 0 0 0-2-2h-1"/></svg></span>{{ __('site.achievements.share_short') }}</button>
        <button type="button" data-acard-close><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg></span>{{ __('site.achievements.close') }}</button>
        <p class="idcard-note" data-acard-note hidden></p>
      </div>
    </div>
  </dialog>
@endif
