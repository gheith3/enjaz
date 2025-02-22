<div class="achievers-grid">
    @foreach($this->achievers as $achiever)
        <a href="{{route('achievers.show', ['achiever' => $achiever->id])}}">
            <div class="achiever-card">
                <div class="card-image" style="background-image: url('{{ Storage::url($achiever->image_path) }}')">
                    {{--            <div class="category-badge">{{ $achiever->name_ar }}</div>--}}
                    <div class="name-badge">{{ $achiever->name_ar }}</div>
                </div>
            </div>
        </a>
    @endforeach
</div>

@push('styles')

<style>

</style>
@endpush
