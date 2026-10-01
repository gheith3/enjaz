<div>
    @if (count($this->achievements))
        <div class="container" id="new-achievement">
            <x-section-title :title="$sectionTitle" />
            <div class="row" id="new-achievements-boxes">
                @foreach ($this->achievements as $achievement)
                    <div class="col-md-3">
                        <div data-aos="fade-in" data-aos-duration="{{ $loop->index * 1500 }}">
                            <livewire:components.achievement-card :achievement="$achievement" />
                        </div>
                    </div>
                @endforeach
            </div>
            @if (!$showAll)
                <div class="d-flex justify-content-end mt-5">
                    <a href="{{ route('achievements') }}" data-aos="fade-up" data-aos-duration="1000"
                        class="btn app-btn px-5 mx-3">
                        المزيد
                    </a>
                </div>
            @endif
        </div>
    @endif
</div>
