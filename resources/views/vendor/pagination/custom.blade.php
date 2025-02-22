@if ($paginator->hasPages())
    <nav>
        <ul class="pagination">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link bg-secondary text-white border-0 rounded-circle mx-1">&lsaquo;</span>
                </li>
            @else
                <li class="page-item">
                    <button type="button" class="page-link bg-primary text-white border-0 rounded-circle mx-1" wire:click="previousPage" wire:loading.attr="disabled" rel="prev">&lsaquo;</button>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link bg-secondary text-white border-0 rounded-circle mx-1">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page">
                                <span class="page-link bg-primary text-white border-0 rounded-circle mx-1">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <button type="button" class="page-link bg-light text-primary border-0 rounded-circle mx-1" wire:click="gotoPage({{ $page }})">{{ $page }}</button>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <button type="button" class="page-link bg-primary text-white border-0 rounded-circle mx-1" wire:click="nextPage" wire:loading.attr="disabled" rel="next">&rsaquo;</button>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link bg-secondary text-white border-0 rounded-circle mx-1">&rsaquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif 