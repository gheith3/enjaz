{{-- Design: institutions.html, list part (chips, search, grouped cards). --}}
<div>
  <div class="wrap sh-search">
    <form class="search" role="search" wire:submit.prevent><label class="sr-only" for="orgs-q">{{ __('site.organizations.search_label') }}</label><input id="orgs-q" type="search" placeholder="{{ __('site.organizations.search_label') }}" wire:model.live.debounce.300ms="q"><button type="submit" aria-label="{{ __('site.search') }}"><img src="{{ Vite::asset('resources/site/assets/svg/icon-search.svg') }}" alt=""></button></form>
    @php $rings = $this->rings; $d = fn ($n) => \App\Support\ArabicDate::digits($n); @endphp
    @if ($rings['total'])
      <div class="orgs-rings" data-orgs-pulse wire:ignore
           aria-label="{{ __('site.organizations.counts_label', ['total' => $d($rings['total']), 'parts' => collect($rings['items'])->map(fn ($i) => $d($i['count']).' '.$i['type']->chipLabel())->implode(' و')]) }}">
        <svg width="0" height="0" style="position:absolute" aria-hidden="true"><defs><linearGradient id="orGold" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#F4DFAE"/><stop offset="1" stop-color="#A88344"/></linearGradient><linearGradient id="orGreen" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#14A0AB"/><stop offset="1" stop-color="#0A737C"/></linearGradient></defs></svg>
        <div class="or-item or-total">
          <div class="or-ring"><svg viewBox="0 0 60 60"><circle class="or-track" cx="30" cy="30" r="25"/><circle class="or-arc" cx="30" cy="30" r="25" pathLength="100" style="--p:100"/></svg><b data-count="{{ $rings['total'] }}">٠</b></div>
          <small>{{ __('site.organizations.total') }}</small>
        </div>
        @foreach ($rings['items'] as $ring)
          <div class="or-item or-{{ $ring['type']->cssKey() }}">
            <div class="or-ring"><svg viewBox="0 0 60 60"><circle class="or-track" cx="30" cy="30" r="25"/><circle class="or-arc" cx="30" cy="30" r="25" pathLength="100" style="--p:{{ round($ring['count'] / $rings['total'] * 100, 1) }}"/></svg><b data-count="{{ $ring['count'] }}">٠</b></div>
            <small>{{ $ring['type']->chipLabel() }}</small>
          </div>
        @endforeach
      </div>
    @endif
  </div>
  @if ($featured = $this->featured)
    <section class="wrap orgs-featured-wrap" aria-labelledby="feat-h">
      <h2 class="orgs-h" id="feat-h"><img src="{{ Vite::asset('resources/site/assets/svg/ornament.svg') }}" alt="">{{ __('site.organizations.featured') }}</h2>
      <a class="orgs-featured" href="{{ $featured->url() }}">
        <span class="of-logo">@if ($logo = \App\Support\Media\Src::of($featured->image('logo_white')) ?? \App\Support\Media\Src::of($featured->image('logo')))<img src="{{ $logo }}" alt="">@endif</span>
        <span class="of-txt"><small>{{ $featured->type?->label }}</small><b>{{ $featured->name }}</b>@if ($featured->tagline)<span>{{ $featured->tagline }}</span>@endif</span>
        <span class="of-go">{{ __('site.organizations.featured_go') }} <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg></span>
      </a>
    </section>
  @endif
  <section class="section wrap">
    <h2 class="orgs-h"><img src="{{ Vite::asset('resources/site/assets/svg/ornament.svg') }}" alt="">{{ __('site.organizations.list_title') }}</h2>
    <div class="orgs-chips" data-chips>
      <button type="button" @class(['chip', 'active' => $type === '']) aria-pressed="{{ $type === '' ? 'true' : 'false' }}" wire:click="setType('')"><span>{{ __('site.all') }}</span></button>
      @foreach ($this->types as $item)
        <button type="button" @class(['chip', 'active' => $type === $item->slug]) aria-pressed="{{ $type === $item->slug ? 'true' : 'false' }}" wire:click="setType('{{ $item->slug }}')"><span>{{ $item->chipLabel() }}</span></button>
      @endforeach
    </div>
    <div class="orgs-list" data-results>
      @foreach ($groups as $group)
        <div class="orgs-group g-{{ $group->type->cssKey() }}"><span>{{ $group->type->group_label }}</span><b>{{ \App\Support\ArabicDate::digits($group->total) }}</b></div>
        @foreach ($group->items as $organization)
          <x-site.cards.organization :organization="$organization" wire:key="org-{{ $organization->id }}" />
        @endforeach
        @if ($group->more)
          <a class="org-card" href="{{ route('universities.index') }}">
            <span class="org-logo">@if ($logo = \App\Support\Media\Src::of($group->items->first()?->image('logo')))<img src="{{ $logo }}" alt="" loading="lazy">@endif</span>
            <span class="org-info"><b>{{ __('site.organizations.all_universities') }}</b><small class="org-type {{ $group->type->css_class }}">{{ $group->type->label }}</small></span>
            <span class="org-arrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg></span>
          </a>
        @endif
      @endforeach
    </div>
    @if ($groups->isEmpty())<p class="empty">{{ __('site.organizations.empty') }}</p>@endif
  </section>
</div>
