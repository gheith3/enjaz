<div>
    <div class="container mt-5">
        @foreach($this->getList() as $item)
            <div class="row mb-5">
                <div class="col-12 pb-3">
                    <x-section-title :title="$item['category']->title_ar"/>
                </div>
                @foreach(collect($item['achievers'])->take(4) as $achiever)
                    <div class="col-md-3 p-3"
                         data-aos="fade-in"
                         data-aos-duration="{{$loop->index * 800 + 1500}}" wire:key="{{$achiever->id}}">
                        @livewire('components.achiever-card', ['achiever' => $achiever],
                        key("card-{$achiever->id}"))
                    </div>
                @endforeach
                <div class="col-12">
                    <div data-aos="fade-in" data-aos-duration="800">
                        <div class="d-flex justify-content-end mt-5">
                            <a class="btn app-btn px-5 mx-3">
                                المزيد
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        @endforeach

    </div>
</div>
