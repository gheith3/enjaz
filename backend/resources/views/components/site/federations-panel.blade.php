{{-- Design: achievements.html / achievers.html «مستطيل الاتحادات» (css federations). Shown under a field that has
     federations (sports): one card per federation with its logo, short name and the number of results; pressing a
     card filters the grid (Livewire setOrg), pressing it again clears it. Logos are balanced by their ratio
     (--w = √ratio) so wide plates and square emblems look the same size. --}}
{{-- preview: drawn hidden (no counts) under the other fields; the federations chip opens it at once in the browser (تصاميم ٢٠٤٠). --}}
@props(['federations', 'active' => '', 'unit' => 'achievements', 'preview' => false])
<div @class(['fed-panel open', 'x-preview' => $preview]) data-fed-panel data-unit="{{ $unit }}" @if ($preview) hidden @endif>
  <div class="fed-inner">
    <div class="fed-head"><b>{{ __('site.federations.title') }}</b><span>{{ __('site.federations.hint_'.$unit) }}</span></div>
    <div class="fed-track" role="group" aria-label="{{ __('site.federations.title') }}">
      @foreach ($federations as $f)
        @php $label = $f['count'] === null ? null : \App\Support\ArabicCount::of($f['count'], $unit); @endphp
        <button type="button" @class(['fed', 'fed-ph' => ! $f['logo'], 'is-empty' => $f['count'] === 0])
          aria-pressed="{{ $active === $f['slug'] ? 'true' : 'false' }}" title="{{ $f['name'] }}" aria-label="{{ $f['name'] }}@if ($label)، {{ $label }}@endif"
          wire:click="setOrg('{{ $f['slug'] }}')" wire:key="fed-{{ $f['slug'] }}">
          <span class="fed-logo" style="--r: {{ $f['ratio'] }}; --w: {{ round(sqrt(max($f['ratio'], .2)), 3) }}">
            <img src="{{ $f['logo'] ?: Vite::asset('resources/site/assets/svg/ornament.svg') }}" alt="" loading="lazy">
          </span>
          @if ($f['count'])<span class="fed-count" aria-hidden="true">{{ \App\Support\ArabicDate::digits($f['count']) }}</span>@endif
          <span class="fed-name" aria-hidden="true">{{ $f['short'] }}</span>
          <i class="x-glint" aria-hidden="true"></i>
        </button>
      @endforeach
    </div>
  </div>
</div>
