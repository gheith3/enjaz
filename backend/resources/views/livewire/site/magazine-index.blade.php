<section class="wrap mag-sec" id="mag-all" aria-labelledby="all-h">
  <h2 class="mag-h" id="all-h"><img src="{{ Vite::asset('resources/site/assets/svg/ornament.svg') }}" alt="">{{ __('site.magazine.all') }}</h2>
  <div class="mag-tabs" role="group" aria-label="{{ __('site.magazine.tabs') }}">
    <button type="button" @class(['chip', 'active' => $tab === '']) aria-pressed="{{ $tab === '' ? 'true' : 'false' }}" wire:click="setTab('')"><span>{{ __('site.magazine.all_tab') }}</span></button>
    @foreach ($this->categories as $c)
      <button type="button" @class(['chip', 'active' => $tab === $c->slug]) aria-pressed="{{ $tab === $c->slug ? 'true' : 'false' }}" wire:click="setTab(@js($c->slug))"><span>{{ $c->name }}</span></button>
    @endforeach
  </div>
  <div class="mag-list">
    @foreach ($rows as $a)<x-site.magazine.row :article="$a" :with-id="true" />@endforeach
  </div>
  <p class="mag-empty" @if ($rows->isNotEmpty()) hidden @endif>{{ __('site.magazine.empty') }}</p>
</section>
