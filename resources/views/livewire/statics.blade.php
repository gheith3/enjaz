<div>
    <div class="container">
        <div class="app-card pt-4 pb-2">
            <div class="row m-0 p-0 justify-content-center">
                @foreach($this->getStatics() as $item)
                    <div class="col-6 col-md-4 pb-3">
                        <div data-aos="fade-in" data-aos-duration="{{$loop->index * 800}}">
                            <div class="text-center">
                                <img src="{{$item['icon']}}"
                                     class="statistics-image"
                                     alt="achievement">
                            </div>
                            <div class=" mt-4">
                                <h5 class="text-center statistics-title text-black-50">
                                    {{$item['title']}}
                                </h5>
                                <h3 class="text-center statistics-data text-black-50">
                                    {{$item['count']}}
                                </h3>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
