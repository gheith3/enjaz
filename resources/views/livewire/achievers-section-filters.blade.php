<div class="filters-section">
    <div class="filters-row primary-filters">
        @foreach($primaryFilters as $filter)
            <button 
                wire:click="setFilter('{{ $filter['name'] }}')" 
                class="filter-btn {{ $filter['name'] === $selectedFilter ? 'active' : '' }}"
            >
                @if($filter['icon'])
                    <img src="{{ asset($filter['icon']) }}" alt="{{ $filter['name'] }}" class="filter-icon">
                @endif
                <span>{{ $filter['name'] }}</span>
            </button>
        @endforeach
    </div>

    <div class="filters-row secondary-filters">
        @foreach($secondaryFilters as $filter)
            <button 
                wire:click="setFilter('{{ $filter['name'] }}')" 
                class="filter-btn {{ $filter['name'] === $selectedFilter ? 'active' : '' }}"
            >
                <img src="{{ asset($filter['icon']) }}" alt="{{ $filter['name'] }}" class="filter-icon">
                <span>{{ $filter['name'] }}</span>
            </button>
        @endforeach
    </div>
</div>
@push('styles')

<style>
.filters-section {
    padding: 2rem 1rem;
    background-color: white;
}

.filters-row {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1rem;
    justify-content: center;
}

.filter-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.8rem 1.5rem;
    border-radius: 12px;
    border: 1px solid #666;
    background: white;
    color: #666;
    transition: all 0.3s ease;
    cursor: pointer;
    font-size: var(--font-size-5xl);
}

.filter-btn.active {
    background: #0B626A;
    color: white;
    border-color: #0B626A;
}

.filter-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.filter-icon {
    width: 24px;
    height: 24px;
    object-fit: contain;
}

.filter-btn.active .filter-icon {
    filter: brightness(0) invert(1);
}

@media (max-width: 768px) {
    .filters-section {
        padding: 1rem 0.5rem;
    }

    .filter-btn {
        padding: 0.6rem 1rem;
        font-size: 0.9rem;
    }

    .filter-icon {
        width: 20px;
        height: 20px;
    }
}
</style>
@endpush