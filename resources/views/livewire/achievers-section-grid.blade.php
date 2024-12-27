<div class="achievers-grid">
    @foreach($this->achievers as $achiever)
        <a href="{{route('achievers.show', ['achiever' => $achiever->id])}}">
            <div class="achiever-card">
                <div class="card-image" style="background-image: url('{{ asset($achiever->image_path) }}')">
                    {{--            <div class="category-badge">{{ $achiever->name_ar }}</div>--}}
                    <div class="name-badge">{{ $achiever->name_ar }}</div>
                </div>
            </div>
        </a>
    @endforeach
</div>

@push('styles')

<style>
.achievers-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.5rem;
    padding-bottom: 3rem;
    padding-left: 12rem;
    padding-right: 12rem;
    padding-top: 3rem;

    max-width: 1600px;
    margin: 0 auto;
}

.achiever-card {
    border-radius: 26px;
    overflow: hidden;
    position: relative;
    aspect-ratio: 3/4;
    transition: transform 0.3s ease;
    height: 300px;
}

.card-image {
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    position: relative;
    border-radius: 26px;
}

.category-badge {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: white;
    padding: 0.5rem 1rem;
    border-radius: 12px;
    font-weight: 500;
}

.name-badge {
    position: absolute;
    bottom: 1rem;
    left: 50%;
    transform: translateX(-50%);
    background: white;
    padding: 0.5rem 1rem;
    border-radius: 12px;
    color: #0B626A;
    font-weight: 500;
    border: 2px solid white;
    white-space: nowrap;
}

@media (max-width: 768px) {
    .achievers-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        padding: 1rem;
    }

    .category-badge {
        top: 0.5rem;
        right: 0.5rem;
        padding: 0.4rem 0.8rem;
        font-size: 0.9rem;
    }

    .name-badge {
        bottom: 0.5rem;
        padding: 0.4rem 1rem;
        font-size: 0.9rem;
    }
}
</style>
@endpush
