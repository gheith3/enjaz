@props(['paginator'])
{{-- Design pager (js/main.js renderPager): arrows, Arabic page numbers. Livewire pagination, real links for crawlers. --}}
@if ($paginator->hasPages())
  @php $current = $paginator->currentPage(); @endphp
  @php $last = $paginator->lastPage(); @endphp
  <nav class="pager" aria-label="{{ __('site.pagination') }}" x-data x-on:click="if ($event.target.closest('a[wire\\:click]')) document.querySelector('[data-results]')?.scrollIntoView({ behavior: matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth', block: 'start' })">
    <a class="arr" href="{{ $paginator->previousPageUrl() ?? '#' }}" aria-label="{{ __('site.previous_page') }}" @if ($current === 1) aria-disabled="true" @else wire:click.prevent="previousPage" @endif><img src="{{ Vite::asset('resources/site/assets/svg/arrow-right.svg') }}" alt=""></a>
    @foreach (range(1, $last) as $n)
      <a href="{{ $paginator->url($n) }}" aria-label="{{ __('site.page_n', ['n' => $n]) }}" @if ($n === $current) aria-current="page" @else wire:click.prevent="gotoPage({{ $n }})" @endif>{{ \App\Support\ArabicDate::digits($n) }}</a>
    @endforeach
    <a class="arr" href="{{ $paginator->nextPageUrl() ?? '#' }}" aria-label="{{ __('site.next_page') }}" @if ($current === $last) aria-disabled="true" @else wire:click.prevent="nextPage" @endif><img src="{{ Vite::asset('resources/site/assets/svg/arrow-left.svg') }}" alt=""></a>
  </nav>
@endif
