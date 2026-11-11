@php
    $ornament = Vite::asset('resources/site/assets/svg/ornament.svg');
    $row = fn ($x) => match (true) {
        $x instanceof \App\Models\Organization => ['url' => $x->url(), 'title' => $x->name, 'kick' => $x->type?->name, 'img' => $x->logoUrl()],
        $x instanceof \App\Models\Award => ['url' => $x->url(), 'title' => $x->name, 'kick' => __('site.search_page.award'), 'img' => $x->imageUrl('logo', 'card')],
        $x instanceof \App\Models\LabItem => ['url' => $x->url(), 'title' => $x->title, 'kick' => \App\Models\LabItem::KINDS[$x->kind] ?? '', 'img' => $x->imageUrl('cover', 'card')],
        $x instanceof \App\Models\Article => ['url' => $x->url(), 'title' => $x->title, 'kick' => $x->writer?->name, 'img' => $x->imageUrl('cover', 'card')],
        default => ['url' => $x->url(), 'title' => $x->title, 'kick' => $x->tag, 'img' => $x->imageUrl('cover', 'card')],
    };
@endphp
<div class="wrap search-page">
  <div class="sec-title"><img src="{{ $ornament }}" alt=""><h1>{{ __('site.search_page.title') }}</h1></div>
  <form class="search ap-search" role="search" wire:submit.prevent action="{{ route('search') }}">
    <label class="sr-only" for="search-q">{{ __('site.search_site') }}</label>
    <input id="search-q" name="q" type="search" placeholder="{{ __('site.search_site') }}" wire:model.live.debounce.400ms="q" autofocus>
    <button type="submit" aria-label="{{ __('site.search') }}"><img src="{{ Vite::asset('resources/site/assets/svg/icon-search.svg') }}" alt=""></button>
  </form>
  <div wire:loading.class="is-loading">
    @if (mb_strlen($term) < 2)
      <p class="mag-empty">{{ __('site.search_page.hint') }}</p>
    @elseif (! $groups)
      <p class="mag-empty">{{ __('site.search_page.none', ['q' => $term]) }}</p>
    @else
      @foreach ($groups as $key => $items)
        <section class="mag-sec" aria-labelledby="sr-{{ $key }}">
          <h2 class="mag-h" id="sr-{{ $key }}"><img src="{{ $ornament }}" alt="">{{ __('site.search_page.'.$key) }}</h2>
          @if ($key === 'achievers')
            <div class="grid-2 cards-ov">@foreach ($items as $a)<x-site.cards.person :achiever="$a" wire:key="sa-{{ $a->id }}" />@endforeach</div>
          @elseif ($key === 'achievements')
            <div class="grid-2 cards-ov">@foreach ($items as $a)<x-site.cards.achievement :achievement="$a" wire:key="sb-{{ $a->id }}" />@endforeach</div>
          @else
            <div class="mag-list">
              @foreach ($items as $x)
                @php $r = $row($x); @endphp
                <article class="mag-row" wire:key="s{{ $key }}-{{ $x->id }}">
                  <a class="cover-link" href="{{ $r['url'] }}" aria-label="{{ $r['title'] }}"></a>
                  @if ($r['img'])<img class="gold-ph" src="{{ $r['img'] }}" alt="" loading="lazy">@else<span class="mr-type" aria-hidden="true">«</span>@endif
                  <div class="mr-b">@if ($r['kick'])<p class="kick">{{ $r['kick'] }}</p>@endif<h3>{{ $r['title'] }}</h3></div>
                </article>
              @endforeach
            </div>
          @endif
        </section>
      @endforeach
    @endif
  </div>
</div>
