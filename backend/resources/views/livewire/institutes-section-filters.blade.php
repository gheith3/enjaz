<div class="filters-section">
    <div class="filters-row primary-filters">
        <button
            wire:click="setFilter('0')"
            class="filter-btn {{ 0 === $selectedFilter ? 'active' : '' }}"
        >

            <span>الكل</span>
        </button>
        @foreach($this->types as $type)
            <button
                wire:click="setFilter('{{ $type->id }}')"
                class="filter-btn {{ $type->id === $selectedFilter ? 'active' : '' }}"
            >

                <span>{{ $type->title_ar }}</span>
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


}
</style>
@endpush
