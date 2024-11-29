<div class="institutes-grid">
    @foreach($this->filteredInstitutes as $institute)
    <div class="institute-card">
        <div class="card-image" style="background-image: url('{{ asset($institute['image']) }}')">

        </div>
    </div>
    @endforeach
</div>

@push('styles')

<style>
.institutes-grid {
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


.institute-card {
    border-radius: 26px;
    overflow: hidden;
    position: relative;
    aspect-ratio: 4/4;
    transition: transform 0.3s ease;
    height: 300px;
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.29); /* Shadow effect */

}

.card-image {
    width: calc(100% - 18.5px);
    max-width: 100%;
    height: 108.8px;
    position: absolute;
    top: calc(50% - 46.42px);
    left: 23.26px;
    right: 23.24px;
    overflow: hidden;
}


@media (max-width: 768px) {
    .institutes-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        padding: 1rem;
    }


}
</style>
@endpush