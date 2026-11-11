@php
    $d = fn ($n) => \App\Support\ArabicDate::digits($n);
    $segment = $kind === 'invention' ? 'inventions' : 'publications';
@endphp
<div class="wrap lb-all" data-lab-all data-t="{{ $kind === 'invention' ? 'inv' : 'pubs' }}">
  <header class="lb-all-hero"><a class="lb-back" href="{{ route('lab.index') }}">{{ __('site.lab.back') }}</a><h1 data-lab-h>{{ __('site.lab.'.$segment) }}</h1>
    <p>{{ __('site.lab.intro_'.$kind, ['count' => $d($total)]) }}</p></header>
  <div class="lb-filters" data-lab-filters>
    @foreach ($groups as $key => [$label, $options])
      <div class="lb-fg" role="group" aria-label="{{ $label }}"><span>{{ $label }}</span>
        <button type="button" wire:click="pick('{{ $key }}', '')" aria-pressed="{{ $this->{$key} === '' ? 'true' : 'false' }}">{{ __('site.lab.all') }}</button>
        @foreach ($options as $value => $text)<button type="button" wire:click="pick('{{ $key }}', @js((string) $value))" aria-pressed="{{ $this->{$key} === (string) $value ? 'true' : 'false' }}">{{ $text }}</button>@endforeach
      </div>
    @endforeach
    <div class="lb-fg" role="group" aria-label="{{ __('site.lab.f_sort') }}"><span>{{ __('site.lab.f_sort') }}</span>
      @foreach (['new' => 'newest', 'old' => 'oldest'] as $value => $text)<button type="button" wire:click="pick('sort', '{{ $value }}')" aria-pressed="{{ $sort === $value ? 'true' : 'false' }}">{{ __('site.lab.'.$text) }}</button>@endforeach
    </div>
  </div>
  <p class="lb-count" data-lab-count aria-live="polite">{{ __('site.lab.count_of', ['n' => $d($list->count()), 'total' => $d($total)]) }}</p>
  <div data-lab-out>
    @if ($list->isNotEmpty())
      <div class="lb-grid lz-grid lz-{{ $kind === 'invention' ? 'inv' : 'pubs' }}">@foreach ($list as $x)<x-site.lab.card :item="$x" variant="lz" />@endforeach</div>
    @else
      <p class="lb-empty">{{ __('site.lab.empty') }}</p>
    @endif
  </div>
</div>
