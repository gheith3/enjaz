@props(['path', 'current'])
{{-- "موقع الإنجاز في المسيرة": the achiever's achievements in time order (design js/achievement-enhance.js, rendered here). --}}
@php
    $index = max(0, $path->search(fn ($a) => $a->is($current)));
    $previous = $path->get($index - 1);
    $next = $path->get($index + 1);
    $year = fn ($a) => \App\Support\ArabicDate::digits($a->achieved_at?->year ?? '');
@endphp
<section class="ach-path" aria-labelledby="path-title">
  <h2 id="path-title" class="path-title">{{ __('site.achievements.path_title') }}</h2>
  <div class="path" data-path style="--at: {{ number_format($path->count() > 1 ? $index / ($path->count() - 1) : 0, 3, '.', '') }}">
    <span class="path-line" aria-hidden="true"><i></i></span>
    <ol>
      @foreach ($path as $item)
        <li class="{{ $item->scope?->cssGroup() }} {{ $loop->index === $index ? 'here' : '' }}">
          @if ($loop->index === $index)
            <span class="path-dot" aria-current="step"></span>
          @else
            <a class="path-dot" href="{{ $item->url() }}" aria-label="{{ $item->title }} — {{ $year($item) }}"></a>
          @endif
          <small>{{ $year($item) }}</small>
        </li>
      @endforeach
    </ol>
  </div>
  <nav class="path-nav" data-path-nav aria-label="{{ __('site.achievements.path_nav') }}">
    @if ($previous)<a class="pn prev" href="{{ $previous->url() }}"><small>→ {{ __('site.achievements.previous') }} · {{ $year($previous) }}</small><b>{{ $previous->title }}</b></a>@else<span></span>@endif
    @if ($next)<a class="pn next" href="{{ $next->url() }}"><small>{{ __('site.achievements.next') }} · {{ $year($next) }} ←</small><b>{{ $next->title }}</b></a>@else<span></span>@endif
  </nav>
</section>
