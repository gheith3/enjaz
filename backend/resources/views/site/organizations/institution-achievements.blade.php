{{-- «سجل إنجازات المؤسسة» (css org-profile): every achievement linked to the institution or to its people, with its
     news, newest first (App\Support\Site\OrganizationFeed). --}}
@php
    $o = $organization;
    $d = fn ($n) => \App\Support\ArabicDate::digits($n);
    $key = $o->type?->cssKey() ?? 'gov';
@endphp
<x-site.layout :title="__('site.organizations.achievements_of', ['name' => $o->name])" styles="institution" body-class="flush op-page"
    :crumb="['title' => $listPage->title, 'url' => route('organizations.index')]">
<article class="op s-{{ $key }}" @if ($o->color_primary) style="--brand: {{ $o->color_primary }}" @endif>
  <header class="op-head op-head-sm">
    <a class="op-logo" href="{{ $o->url() }}" aria-label="{{ $o->name }}"><x-site.organization.emblem :organization="$o" :lazy="false" /></a>
    <span class="op-type">{{ __('site.organizations.record') }}</span>
    <h1>{{ __('site.organizations.achievements_of', ['name' => $o->name]) }}</h1>
    <p class="op-line">{{ \App\Support\ArabicCount::of($feed->count(), 'achievements') }} · <a href="{{ $o->url() }}" style="color:#F1DDAE">{{ __('site.organizations.back_to', ['name' => $o->displayName()]) }}</a></p>
  </header>
  <section class="wrap op-sec op-ach" aria-label="{{ __('site.organizations.achievements') }}">
    @if ($feed->isNotEmpty())
      <div class="op-grid">
        @foreach ($feed as $item)
          <a class="op-card-a" href="{{ $item['url'] }}">
            <span class="op-img">@if ($item['image'])<img src="{{ $item['image'] }}" alt="" loading="lazy" style="object-position: {{ $item['focal'] }}">@endif</span>
            <span class="op-pill k-{{ $item['kind'] }}">{{ $item['tag'] ?? __('site.organizations.news_tag') }}</span><b>{{ $item['title'] }}</b>
            <small>{{ collect([$item['who'], $item['label']])->filter()->implode(' · ') }}</small>
          </a>
        @endforeach
      </div>
    @else
      <div class="op-empty">
        <p><b>{{ __('site.organizations.empty_title') }}</b><span>{{ __('site.organizations.empty_text') }}</span></p>
        <a href="{{ url('/contact') }}">{{ __('site.organizations.empty_cta') }} <span aria-hidden="true">←</span></a>
      </div>
    @endif
  </section>
</article>
</x-site.layout>
