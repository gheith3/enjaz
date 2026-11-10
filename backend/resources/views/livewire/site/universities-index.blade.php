{{-- Design: universities.html, list part (js/universities.js, rendered here). --}}
@php $d = fn ($n) => \App\Support\ArabicDate::digits($n); $counts = $this->counts; @endphp
<div class="wrap">
  <div class="ul-tools">
    <label class="ul-q"><span class="sr-only">{{ __('site.organizations.universities_search') }}</span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg><input type="search" placeholder="{{ __('site.organizations.universities_placeholder') }}" data-u-q wire:model.live.debounce.300ms="q"></label>
    <div class="ul-chips" role="group" aria-label="{{ __('site.organizations.universities_kind') }}" data-u-chips>
      @foreach (['' => __('site.all'), 'uni' => __('site.organizations.group_uni'), 'college' => __('site.organizations.group_college'), 'tech' => __('site.organizations.group_tech')] as $value => $label)
        <button type="button" @class(['active' => $g === $value]) aria-pressed="{{ $g === $value ? 'true' : 'false' }}" data-g="{{ $value }}" wire:click="setGroup('{{ $value }}')">{{ $label }}</button>
      @endforeach
    </div>
  </div>
  <div data-unis>
    @php $shown = 0; @endphp
    @foreach ($groups as $group => $items)
      @php $list = $group === 'uni' && $spot ? $items->reject(fn ($o) => $o->is($spot)) : $items; @endphp
      @continue($items->isEmpty())
      @php $shown += $items->count(); @endphp
      <section class="u-group" data-g="{{ $group }}">
        @if ($group === 'tech')
          @php $tech = $items->first(); $branches = $tech->units; @endphp
          <h2 class="u-gh"><span>{{ __('site.organizations.tech_title') }}</span><b>{{ __('site.organizations.tech_branches', ['n' => $d($branches->count())]) }}</b></h2>
          <a class="u-tech" href="{{ $tech->url() }}">
            <span class="u-tech-top"><span class="u-logo" data-r="{{ $tech->logo_ratio ?: 1 }}">@if ($logo = $tech->logoUrl())<img src="{{ $logo }}" alt="" loading="lazy">@endif</span>
              <span class="u-info">@if ($tech->formerly)<small>{{ __('site.organizations.formerly', ['name' => $tech->formerly]) }}</small>@endif<b>{{ $tech->name }}</b></span></span>
            <span class="u-branches">@foreach ($branches as $branch)<span>{{ $branch->name }}</span>@endforeach</span>
          </a>
        @else
          <h2 class="u-gh"><span>{{ __('site.organizations.group_'.$group) }}</span><b>{{ $d($items->count()) }}</b></h2>
          @if ($group === 'uni' && $spot)
            <a class="u-spot" href="{{ $spot->url() }}" @if ($spot->color_primary) style="--c1:{{ $spot->color_primary }};--c2:{{ $spot->color_secondary ?: $spot->color_primary }}" @endif>
              <span class="u-spot-logo">@if ($logo = $spot->image('logo_wide')?->getUrl() ?? $spot->logoUrl())<img src="{{ $logo }}" alt="" loading="lazy">@endif</span>
              <span class="u-spot-txt"><small>{{ $spot->tagline ?: __('site.organizations.profile') }}</small><b>{{ $spot->name }}</b>
                <span class="u-spot-stats">@foreach ($spot->stats->take(3) as $stat)<span><b>{{ $stat->value }}</b>{{ $stat->label }}</span>@endforeach</span>
                <em>{{ __('site.organizations.profile_cta') }} <span aria-hidden="true">←</span></em></span>
              @php $c = $counts[$spot->id] ?? []; $links = collect([
                  ($c['own'] ?? 0) ? trans_choice('site.organizations.own_count', $c['own'], ['n' => $d($c['own'])]) : null,
                  ($c['people'] ?? 0) ? trans_choice('site.organizations.people_count', $c['people'], ['n' => $d($c['people'])]) : null,
                  ($c['members'] ?? 0) ? trans_choice('site.organizations.members_count', $c['members'], ['n' => $d($c['members'])]) : null,
              ])->filter()->implode(' · '); @endphp
              @if ($links)<span class="u-links">{{ $links }}</span>@endif
            </a>
          @endif
          <div class="u-grid">
            @foreach ($list as $organization)
              <x-site.cards.university :organization="$organization" :counts="$counts[$organization->id] ?? null" wire:key="u-{{ $organization->id }}" />
            @endforeach
          </div>
        @endif
      </section>
    @endforeach
    @if (! $shown)<p class="u-empty">{{ __('site.organizations.no_results') }}</p>@endif
  </div>
</div>
