{{-- Design: institution.html (ministries, military, companies). --}}
@php
    $logo = $organization->logoUrl();
    $about = array_values(array_filter(preg_split('/\R\s*\R/u', (string) $organization->about)));
    $feature = $news->first();
    $rows = $news->slice(1, 4);
    $letter = $organization->letter?->is_published ? $organization->letter : null;
@endphp
<x-site.layout :title="$organization->name" :description="$organization->tagline ?: \Illuminate\Support\Str::limit(strip_tags((string) $organization->about), 160)"
    :image="$organization->imageUrl('cover')" styles="institution" body-class="flush" :noindex="! $organization->isLive()"
    :crumb="['title' => $listPage->title, 'url' => route('organizations.index')]">
  <section class="org-hero2">
    @if ($logo)<span class="oh-logo"><img src="{{ $logo }}" alt="{{ $organization->name }}"></span>@endif
    @if ($organization->type)<span class="oh-type">{{ $organization->type->label }}</span>@endif
    <h1>{{ $organization->name }}</h1>
    @if ($areas = $organization->focusAreaLabels())
      <div class="oh-fields">@foreach ($areas as $area)<span>{{ $area }}</span>@endforeach</div>
    @endif
  </section>

  @if ($about)
    <section class="wrap"><div class="org-about" data-org-about><div class="oa-clip">@foreach ($about as $paragraph)<p>{{ $paragraph }}</p>@endforeach</div><button type="button" class="oa-more" data-org-more aria-expanded="false">{{ __('site.organizations.read_more') }}</button></div></section>
  @endif

  @if ($feature)
    <section class="section wrap" aria-labelledby="oach-h">
      <h2 class="orgs-h" id="oach-h"><img src="{{ Vite::asset('resources/site/assets/svg/ornament.svg') }}" alt="">{{ __('site.organizations.achievements') }}</h2>
      <a class="oa-feature" href="{{ $feature->url() }}">
        @if ($img = $feature->imageUrl('cover'))<img src="{{ $img }}" alt="">@endif
        <span class="oa-feature-b"><small>{{ __('site.organizations.latest') }} · {{ $feature->dateLabel('daymonth') }}</small><b>{{ $feature->title }}</b><span class="oa-read">{{ __('site.organizations.read_details') }}</span></span>
      </a>
      @if ($rows->isNotEmpty())
        <div class="oa-list">
          @foreach ($rows as $item)
            <a class="oa-row" href="{{ $item->url() }}">
              @if ($img = $item->imageUrl('cover', 'card'))<img src="{{ $img }}" alt="" loading="lazy">@endif
              <span class="oa-txt"><small>{{ $item->dateLabel('daymonth') }}</small><b>{{ $item->title }}</b></span>
              <span class="org-arrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg></span>
            </a>
          @endforeach
        </div>
      @endif
      <a class="oa-all" href="{{ route('organizations.achievements', $organization) }}">{{ __('site.organizations.all_achievements') }}</a>
    </section>
  @endif

  @if ($letter)
    <x-site.organization.word-d5 :letter="$letter" />
  @endif

  @if ($sameField->isNotEmpty())
    <section class="section wrap" aria-labelledby="same-h">
      <h2 class="orgs-h" id="same-h"><img src="{{ Vite::asset('resources/site/assets/svg/ornament.svg') }}" alt="">{{ __('site.organizations.same_field') }}</h2>
      <div class="org-rail" data-org-rail>
        <div class="rail-view"><div class="rail-track">
          @foreach ($sameField as $other)
            <a class="org-sq" href="{{ $other->url() }}" title="{{ $other->name }}">@if ($otherLogo = $other->logoUrl())<img src="{{ $otherLogo }}" alt="" loading="lazy">@endif<span>{{ $other->displayName() }}</span></a>
          @endforeach
        </div></div>
        <a class="more-round" href="{{ route('organizations.index') }}" aria-label="{{ __('site.organizations.more_orgs') }}"><span class="btn-round"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg></span></a>
      </div>
    </section>
  @endif
</x-site.layout>
