{{-- Design: institution-achievements.html: the organization's achievements and news. --}}
<x-site.layout :title="__('site.organizations.achievements_of', ['name' => $organization->name])" styles="institution" body-class="flush"
    :crumb="['title' => $listPage->title, 'url' => route('organizations.index')]">
  <section class="oi-hero">
    <a class="oi-back" href="{{ $organization->url() }}"><span class="btn-round"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg></span>{{ $organization->displayName() }}</a>
    @if ($logo = $organization->logoUrl())<span class="oh-logo"><img src="{{ $logo }}" alt="{{ $organization->name }}"></span>@endif
    <span class="orgs-eyebrow">{{ __('site.organizations.record') }}</span>
    <h1>{{ __('site.organizations.achievements_of', ['name' => $organization->name]) }}</h1>
    <span class="oi-count"><b>{{ \App\Support\ArabicDate::digits($news->count()) }}</b> {{ __('site.organizations.documented') }}</span>
  </section>
  <section class="section wrap" aria-label="{{ __('site.organizations.achievements') }}">
    <div class="oi-grid">
      @foreach ($news as $item)
        <a class="oi-card" href="{{ $item->url() }}">
          <span class="oi-img">@if ($img = $item->imageUrl('cover', 'card'))<img src="{{ $img }}" alt="" loading="lazy">@endif</span>
          <span class="oi-b"><small>{{ $item->dateLabel('daymonth') }}</small><b>{{ $item->title }}</b></span>
        </a>
      @endforeach
    </div>
  </section>
</x-site.layout>
