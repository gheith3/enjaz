{{-- Design: org-news.html. --}}
@php
    $organization = $news->organization;
    $cover = $news->imageUrl('cover');
    $ornament = Vite::asset('resources/site/assets/svg/ornament.svg');
    $body = str_replace('<h2>', '<h2><img src="'.$ornament.'" alt="">', (string) str($news->body)->sanitizeHtml());
    $parts = preg_split('/\s+/u', $news->dateLabel(), 2);
@endphp
<x-site.layout :title="$news->title" :description="$news->dek ?: $news->summary" :image="$cover" styles="org-news" body-class="flush" og-type="article"
    :noindex="! $news->isLive()"
    :crumb="['title' => $listPage->title, 'url' => route('organizations.index')]">
  <article class="nw" data-nw>
    @if ($organization)
      <header class="nw-mast">
        <a class="nw-org" href="{{ $organization->url() }}"><span class="nw-logo">@if ($logo = $organization->logoUrl())<img src="{{ $logo }}" alt="">@endif</span><span><small>{{ __('site.organizations.issued_by') }}</small><b>{{ $organization->name }}</b></span><span class="nw-org-go" aria-hidden="true">‹</span></a>
      </header>
    @endif
    <div class="wrap nw-head">
      <div class="nw-meta">@if ($news->tag)<span class="tl-tag t-{{ $news->kind === 'achievement' ? 'ach' : 'news' }}">{{ $news->tag }}</span>@endif @if ($news->happened_at)<span class="nw-date"><b>{{ $parts[0] ?? '' }}</b><small>{{ $parts[1] ?? '' }}</small></span>@endif</div>
      <h1>{{ $news->title }}</h1>
      @if ($news->dek)<p class="nw-dek">{{ $news->dek }}</p>@endif
    </div>
    @if ($cover)
      <figure class="nw-cover"><img src="{{ $cover }}" alt="{{ $news->title }}">@if ($news->cover_caption || $organization)<figcaption>{{ $news->cover_caption ?: __('site.organizations.source', ['name' => $organization->name]) }}</figcaption>@endif</figure>
    @endif
    @if ($facts = $news->factItems())
      <ul class="wrap nw-facts">@foreach ($facts as $fact)<li><small>{{ $fact['label'] }}</small><b>{{ $fact['value'] }}</b></li>@endforeach</ul>
    @endif
    <div class="wrap nw-body">
      <div class="nw-text">{!! $body !!}</div>
      @if (($gallery = $news->getMedia('gallery'))->isNotEmpty())
        <div class="nw-gallery" aria-label="{{ __('site.organizations.event_photos') }}">@foreach ($gallery as $image)<img src="{{ $image->hasGeneratedConversion('card') ? $image->getUrl('card') : $image->getUrl() }}" alt="{{ \App\Support\Localized::get($image->getCustomProperty('caption')) }}" loading="lazy">@endforeach</div>
      @endif
      <div class="ar-share nw-share"><span>{{ __('site.organizations.share_news') }}</span>
        <button type="button" data-share aria-label="{{ __('site.achievements.share_short') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="m8.6 13.5 6.8 4M15.4 6.5l-6.8 4"/></svg></button>
        <small class="ar-copied" role="status" aria-live="polite"></small>
      </div>
    </div>
    @if ($more->isNotEmpty())
      <section class="wrap nw-more" aria-labelledby="nw-more-h">
        <h2 class="orgs-h" id="nw-more-h"><img src="{{ $ornament }}" alt="">{{ __('site.organizations.more_news') }}</h2>
        <div class="nw-grid">
          @foreach ($more as $item)
            @php $p = preg_split('/\s+/u', $item->dateLabel(), 3); @endphp
            <a class="nw-mini" href="{{ $item->url() }}">@if ($img = $item->imageUrl('cover', 'card'))<img src="{{ $img }}" alt="" loading="lazy">@endif<span><small>{{ trim(($p[0] ?? '').' '.($p[1] ?? '')) }}</small><b>{{ $item->title }}</b></span></a>
          @endforeach
        </div>
        @if ($organization)<a class="oa-all" href="{{ route('organizations.achievements', $organization) }}">{{ __('site.organizations.all_achievements') }}</a>@endif
      </section>
    @endif
  </article>
</x-site.layout>
