{{-- «ديوان المؤسسات», the list (css orgs-diwan): search and type tabs with counts, the featured organization, then
     the organizations grouped by type as compact cards (x-site.cards.organization). --}}
@php
    $d = fn ($n) => \App\Support\ArabicDate::digits($n);
    $searching = trim($q) !== '';
    $tabs = $this->types->where('organizations_count', '>', 0);
@endphp
<div class="od-list" id="od-list">
  <div class="wrap od-tools">
    <form class="od-search" role="search" wire:submit.prevent>
      <label class="sr-only" for="orgs-q">{{ __('site.organizations.search_label') }}</label>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.6-3.6"/></svg>
      <input id="orgs-q" type="search" placeholder="{{ __('site.organizations.search_label') }}" autocomplete="off" wire:model.live.debounce.300ms="q">
    </form>
    <div class="od-tabs" role="group" aria-label="{{ __('site.organizations.tabs') }}">
      <button type="button" @class(['od-tab', 'on' => $type === '']) aria-pressed="{{ $type === '' ? 'true' : 'false' }}" wire:click="setType('')">{{ __('site.all') }} <b>{{ $d($tabs->sum('organizations_count')) }}</b></button>
      @foreach ($tabs as $item)
        <button type="button" @class(['od-tab', 's-'.$item->cssKey(), 'on' => $type === $item->slug]) aria-pressed="{{ $type === $item->slug ? 'true' : 'false' }}" wire:click="setType('{{ $item->slug }}')">{{ $item->chipLabel() }} <b>{{ $d($item->organizations_count) }}</b></button>
      @endforeach
    </div>
  </div>

  @if ($type === '' && ! $searching && ($featured = $this->featured)->isNotEmpty())
    <section @class(['wrap od-spot-wrap', 'od-spots' => $featured->count() > 1]) aria-labelledby="od-spot-h">
      <h2 class="sr-only" id="od-spot-h">{{ __('site.organizations.featured') }}</h2>
      @foreach ($featured as $org)
        <a class="od-spot s-{{ $org->type?->cssKey() ?? 'gov' }}" href="{{ $org->url() }}">
          <span class="od-spot-logo"><x-site.organization.emblem :organization="$org" :lazy="false" /></span>
          <span class="od-spot-t">
            <small>{{ __('site.organizations.featured') }}@if ($org->type) · {{ $org->type->label }}@endif</small>
            <b>{{ $org->name }}</b>
            @if ($org->tagline)<span class="od-spot-line">{{ $org->tagline }}</span>@endif
          </span>
          @if ($areas = $org->focusAreaLabels())<span class="od-spot-areas">@foreach ($areas as $area)<i>{{ $area }}</i>@endforeach</span>@endif
          <em>{{ __('site.organizations.featured_go') }} <span aria-hidden="true">←</span></em>
        </a>
      @endforeach
    </section>
  @endif

  @foreach ($groups as $group)
    @php $key = $group->type->cssKey(); @endphp
    <section class="wrap od-sector s-{{ $key }}" id="od-{{ $group->type->slug }}" aria-labelledby="od-h-{{ $group->type->slug }}" wire:key="sector-{{ $group->type->id }}">
      <header class="od-sec-h">
        <span class="od-glyph"><x-site.organization.glyph :kind="$key" /></span>
        <h2 id="od-h-{{ $group->type->slug }}">{{ $group->type->group_label }}</h2>
        <b class="od-count">{{ $d($group->total) }}</b>
      </header>
      <div class="od-cards">
        @foreach ($group->items as $organization)
          <x-site.cards.organization :organization="$organization" wire:key="org-{{ $organization->id }}" />
        @endforeach
        @if ($group->more)
          <a class="oc oc-all s-{{ $key }}" href="{{ route('universities.index') }}">
            <span class="oc-logo"><span class="oc-plus">@if ($group->total > $group->items->count())+{{ $d($group->total - $group->items->count()) }}@else<x-site.organization.glyph :kind="$key" />@endif</span></span>
            <span class="oc-t"><b>{{ __('site.organizations.all_universities') }}</b><small>{{ $group->type->group_label }}</small></span>
            <svg class="oc-go" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg>
          </a>
        @endif
      </div>
    </section>
  @endforeach
  @if ($groups->isEmpty())<p class="wrap od-empty">{{ __('site.organizations.empty') }}</p>@endif
</div>
