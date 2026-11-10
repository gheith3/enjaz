{{-- Design: achievers.html. Same markup and classes; content and filters from the database. --}}
<div>
  <section class="page-hero ap-hero ap-intro" data-wall="{{ json_encode($this->wall, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}" wire:ignore>
    <div class="hero-text">
      <h1 class="ap-title"><span class="ap-shine" data-t="{{ $sitePage->hero_title ?: $sitePage->title }}">{{ $sitePage->hero_title ?: $sitePage->title }}</span></h1>
      @if ($sitePage->hero_lead)<p class="ap-sub">{!! nl2br(e($sitePage->hero_lead)) !!}</p>@endif
      @if ($this->faces->isNotEmpty())
        <div class="ap-faces" aria-label="{{ __('site.achievers.faces') }}">
          @foreach ($this->faces as $face)
            <img src="{{ $face->imageUrl('portrait', 'card') }}" alt="{{ $face->name }}" width="48" height="48" style="object-position:{{ \App\Models\Achiever::focal($face->image('portrait')) }}">
          @endforeach
          <span class="ap-more" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg></span>
        </div>
      @endif
      <form class="search ap-search" role="search" wire:submit.prevent>
        <label class="sr-only" for="achievers-q">{{ __('site.achievers.search_label') }}</label>
        <input id="achievers-q" type="search" placeholder="{{ __('site.achievers.search_placeholder') }}" wire:model.live.debounce.350ms="q">
        <button type="submit" aria-label="{{ __('site.search') }}"><img src="{{ Vite::asset('resources/site/assets/svg/icon-search.svg') }}" alt=""></button>
      </form>
    </div>
    <div class="ap-deck" data-hero-deck aria-hidden="true"></div>
  </section>

  @php $weekCards = collect([$week['m'] ?? null, $week['f'] ?? null])->filter()->values(); @endphp
  @if ($weekCards->isNotEmpty())
    <section @class(['wrap', 'week', 'week-duo' => $weekCards->count() > 1]) aria-labelledby="week-title" wire:ignore>
      @foreach ($weekCards as $weekAchiever)
        <x-site.week-card :achiever="$weekAchiever" :first="$loop->first" />
      @endforeach
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
        <button type="button" @class(['flt-btn', 'on' => $gender !== '']) x-ref="button" x-bind:aria-expanded="open.toString()" aria-controls="flt-box" x-on:click="open = !open">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M7 12h10M10 18h4"/></svg>
          <span>{{ __('site.filters.button') }}</span>@if ($gender !== '')<b class="flt-n">١</b>@endif
        </button>
        <div class="flt-box" id="flt-box" role="dialog" aria-label="{{ __('site.achievers.filter_dialog') }}" x-show="open" x-cloak>
          <div class="flt-row"><span class="flt-l">{{ __('site.filters.gender') }}</span>
            <div class="flt-seg" role="group">
              <button type="button" aria-pressed="{{ $gender === 'm' ? 'true' : 'false' }}" wire:click="toggleGender('m')">{{ __('site.filters.male') }}</button>
              <button type="button" aria-pressed="{{ $gender === 'f' ? 'true' : 'false' }}" wire:click="toggleGender('f')">{{ __('site.filters.female') }}</button>
            </div>
          </div>
          <div class="flt-foot"><button type="button" class="flt-clear" wire:click="$set('gender', '')">{{ __('site.filters.clear_all') }}</button><button type="button" class="flt-done" x-on:click="open = false; $refs.button.focus()">{{ __('site.filters.done') }}</button></div>
        </div>
      </div>
    </div>
    @if ($gender !== '')
      <div class="flt-chips">
        <button type="button" class="flt-chip" aria-label="{{ __('site.filters.remove', ['label' => __('site.filters.'.($gender === 'f' ? 'female' : 'male'))]) }}" wire:click="$set('gender', '')">{{ __('site.filters.'.($gender === 'f' ? 'female' : 'male')) }}<span aria-hidden="true">✕</span></button>
      </div>
    @endif

    <div class="grid-2 cards-ov" data-results wire:loading.class="is-loading">
      @foreach ($results as $achiever)
        <x-site.cards.person :achiever="$achiever" wire:key="achiever-{{ $achiever->id }}" />
      @endforeach
    </div>

    @if ($results->isEmpty())
      <div class="empty">
        <p>{{ __('site.achievers.empty') }}</p>
        <button type="button" class="chip" wire:click="clearAll"><span>{{ __('site.achievers.show_all') }}</span></button>
      </div>
    @endif

    <x-site.pager :paginator="$results" />
  </section>
</div>
