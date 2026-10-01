@if ($paginator->hasPages())
  <nav class="pager mt-[6.5rem] gap-[3.5rem]" role="navigation" aria-label="{{ __('site.pagination.label') }}">
    {{-- Next (visually first in the LTR pager; chevron matches the design) --}}
    @if ($paginator->hasMorePages())
      <a href="{{ $paginator->nextPageUrl() }}" class="arrow" rel="next" aria-label="{{ __('site.pagination.next') }}">
        <svg class="w-[2.2rem] h-[2.2rem]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="m14.5 6-6 6 6 6"/></svg>
      </a>
    @else
      <span class="arrow opacity-40" aria-disabled="true" aria-label="{{ __('site.pagination.next') }}">
        <svg class="w-[2.2rem] h-[2.2rem]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="m14.5 6-6 6 6 6"/></svg>
      </span>
    @endif

    {{-- Page numbers, highest first to match the design's LTR pager --}}
    @foreach (array_reverse($elements) as $element)
      @if (is_string($element))
        <span class="num !cursor-default text-[#1b1e24]">{{ $element }}</span>
      @endif

      @if (is_array($element))
        @foreach (array_reverse($element, true) as $page => $url)
          @if ($page == $paginator->currentPage())
            <span class="num is-active" aria-current="page">{{ $page }}</span>
          @else
            <a href="{{ $url }}" class="num" aria-label="{{ __('site.pagination.page', ['page' => $page]) }}">{{ $page }}</a>
          @endif
        @endforeach
      @endif
    @endforeach

    {{-- Previous --}}
    @if ($paginator->onFirstPage())
      <span class="arrow opacity-40" aria-disabled="true" aria-label="{{ __('site.pagination.previous') }}">
        <svg class="w-[2.2rem] h-[2.2rem]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="m9.5 6 6 6-6 6"/></svg>
      </span>
    @else
      <a href="{{ $paginator->previousPageUrl() }}" class="arrow" rel="prev" aria-label="{{ __('site.pagination.previous') }}">
        <svg class="w-[2.2rem] h-[2.2rem]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="m9.5 6 6 6-6 6"/></svg>
      </a>
    @endif
  </nav>
@endif
