{{-- Design: achievements.html. Same markup and classes; content and filters from the database. --}}
<div x-data x-init="if (matchMedia('(min-width: 1100px)').matches && $wire.perPage !== 16) $wire.set('perPage', 16)">
  <section class="page-hero ah2-hero ah2-intro" wire:ignore.self>
    <div class="ah2-media" aria-hidden="true" wire:ignore>
      @php $tiles = $this->heroTiles; @endphp
      @if (count($tiles))
        <div class="ah2-wall">
          @foreach (range(0, 4) as $row)
            <div class="ah2-row ah2-row{{ $row }}">
              @foreach (range(0, 27) as $i)
                @php $tile = $tiles[($i + $row * 3) % count($tiles)]; @endphp
                <span @class(['ah2-tile', 'is-gold' => $tile['gold']])><img src="{{ $tile['url'] }}" alt="" width="216" height="240" decoding="async"></span>
              @endforeach
            </div>
          @endforeach
        </div>
      @endif
    </div>
    <div class="ah2-text">
      <h1 class="ah2-title"><span class="ah2-shine" data-t="{{ $sitePage->hero_title ?: $sitePage->title }}">{{ $sitePage->hero_title ?: $sitePage->title }}</span></h1>
      @if ($sitePage->hero_lead)
        <p class="ah2-sub">{!! nl2br(e($sitePage->hero_lead)) !!}</p>
      @endif
      <form class="search ah2-search" role="search" wire:submit.prevent>
        <label class="sr-only" for="achievements-q">{{ __('site.achievements.search_label') }}</label>
        <input id="achievements-q" type="search" placeholder="{{ __('site.achievements.search_placeholder') }}" wire:model.live.debounce.350ms="q">
        <button type="submit" aria-label="{{ __('site.search') }}"><img src="{{ Vite::asset('resources/site/assets/svg/icon-search.svg') }}" alt=""></button>
      </form>
    </div>
  </section>

  @if ($honor = $this->honor)
    @php $honorAchiever = $honor->primaryAchiever(); @endphp
    <section class="wrap honor" aria-labelledby="honor-title">
      <a class="honor-card" href="{{ $honor->url() }}">
        <img class="honor-img" src="{{ $honor->imageUrl('cover') }}" alt="{{ \App\Models\Achievement::alt($honor->image('cover'), $honor->title) }}" width="1000" height="665" style="object-position:{{ \App\Models\Achievement::focal($honor->image('cover')) }}">
        <span class="honor-body">
          <span class="honor-kicker"><img src="{{ Vite::asset('resources/site/assets/svg/ornament.svg') }}" alt="">{{ __('site.achievements.honor') }}</span>
          <span class="honor-title" id="honor-title" role="heading" aria-level="2">{{ $honor->title }}</span>
          <span class="honor-meta">{{ collect([$honorAchiever?->name, $honor->field?->name])->filter()->implode(' · ') }} · <time datetime="{{ $honor->achieved_at?->toDateString() }}">{{ $honor->dateLabel() }}</time></span>
          <span class="honor-cta">{{ __('site.achievements.read_story') }} <span aria-hidden="true">←</span></span>
        </span>
      </a>
    </section>
  @endif

  <section class="section wrap ach-filters" aria-label="{{ __('site.categories') }}">
    <div class="cats-wrap" data-cats>
      <div class="chips-flow" data-chips>
        <button type="button" @class(['chip', 'active' => $field === '']) aria-pressed="{{ $field === '' ? 'true' : 'false' }}" wire:click="setField('')"><span>{{ __('site.all') }}</span></button>
        @foreach ($this->fields as $item)
          <button type="button" @class(['chip', 'active' => $field === $item->slug]) aria-pressed="{{ $field === $item->slug ? 'true' : 'false' }}" wire:click="setField('{{ $item->slug }}')">@if ($item->iconUrl())<img src="{{ $item->iconUrl() }}" alt="">@endif<span>{{ $item->name }}</span></button>
        @endforeach
      </div>
    </div>
  </section>

  <section class="section wrap">
    <div class="res-bar">
      <p class="res-count" aria-live="polite">{{ $countLabel }}</p>
      <div class="flt" x-data="{ open: false }" x-on:click.outside="open = false" x-on:keydown.escape.window="if (open) { open = false; $refs.button.focus() }">
        <button type="button" @class(['flt-btn', 'on' => count($activeFilters)]) x-ref="button" x-bind:aria-expanded="open.toString()" aria-controls="flt-box" x-on:click="open = !open">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M7 12h10M10 18h4"/></svg>
          <span>{{ __('site.filters.button') }}</span>@if (count($activeFilters))<b class="flt-n">{{ \App\Support\ArabicDate::digits(count($activeFilters)) }}</b>@endif
        </button>
        <div class="flt-box" id="flt-box" role="dialog" aria-label="{{ __('site.filters.dialog') }}" x-show="open" x-cloak>
          <div class="flt-row"><span class="flt-l">{{ __('site.filters.gender') }}</span>
            <div class="flt-seg" role="group">
              @foreach (['' => __('site.all'), 'm' => __('site.filters.male'), 'f' => __('site.filters.female')] as $value => $label)
                <button type="button" aria-pressed="{{ $gender === $value ? 'true' : 'false' }}" wire:click="$set('gender', '{{ $value }}')">{{ $label }}</button>
              @endforeach
            </div>
          </div>
          @if (count($this->years))
            <div class="flt-row"><span class="flt-l">{{ __('site.filters.year') }}</span>
              <div class="flt-years">
                <select aria-label="{{ __('site.filters.from') }}" wire:model.live="from"><option value="">{{ __('site.filters.from') }}</option>@foreach ($this->years as $year)<option value="{{ $year }}">{{ \App\Support\ArabicDate::digits($year) }}</option>@endforeach</select>
                <span>–</span>
                <select aria-label="{{ __('site.filters.to') }}" wire:model.live="to"><option value="">{{ __('site.filters.to') }}</option>@foreach ($this->years as $year)<option value="{{ $year }}">{{ \App\Support\ArabicDate::digits($year) }}</option>@endforeach</select>
              </div>
            </div>
          @endif
          <div class="flt-row"><span class="flt-l">{{ __('site.filters.scope') }}</span>
            <div class="flt-seg" role="group">
              <button type="button" aria-pressed="{{ $scope === '' ? 'true' : 'false' }}" wire:click="$set('scope', '')">{{ __('site.all') }}</button>
              @foreach ($this->scopes as $item)
                <button type="button" aria-pressed="{{ $scope === $item->slug ? 'true' : 'false' }}" wire:click="$set('scope', '{{ $item->slug }}')">{{ $item->name }}</button>
              @endforeach
            </div>
          </div>
          <div class="flt-row"><span class="flt-l">{{ __('site.filters.sort') }}</span>
            <div class="flt-seg" role="group">
              <button type="button" aria-pressed="{{ $sort === 'new' ? 'true' : 'false' }}" wire:click="$set('sort', 'new')">{{ __('site.filters.newest') }}</button>
              <button type="button" aria-pressed="{{ $sort === 'old' ? 'true' : 'false' }}" wire:click="$set('sort', 'old')">{{ __('site.filters.oldest') }}</button>
            </div>
          </div>
          <div class="flt-foot"><button type="button" class="flt-clear" wire:click="clearFilters">{{ __('site.filters.clear_all') }}</button><button type="button" class="flt-done" x-on:click="open = false; $refs.button.focus()">{{ __('site.filters.done') }}</button></div>
        </div>
      </div>
    </div>
    @if (count($activeFilters))
      <div class="flt-chips">
        @foreach ($activeFilters as $filter)
          <button type="button" class="flt-chip" aria-label="{{ __('site.filters.remove', ['label' => $filter['label']]) }}" wire:click="removeFilter('{{ $filter['key'] }}')">{{ $filter['label'] }}<span aria-hidden="true">✕</span></button>
        @endforeach
      </div>
    @endif

    <div class="grid-2 cards-ov" data-results wire:loading.class="is-loading">
      @foreach ($results as $achievement)
        <x-site.cards.achievement :achievement="$achievement" :wide="$loop->index === 6" wire:key="ach-{{ $achievement->id }}" />
      @endforeach
    </div>

    @if ($results->isEmpty())
      <div class="empty">
        <p>{{ __('site.achievements.empty') }}</p>
        <button type="button" class="chip" wire:click="clearAll"><span>{{ __('site.achievements.show_all') }}</span></button>
      </div>
    @endif

    <x-site.pager :paginator="$results" />
  </section>
</div>
