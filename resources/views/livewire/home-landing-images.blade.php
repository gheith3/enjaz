<div id="landing-section">

    {{view("components.layouts.nav")}}

    <div id="landing-section-image" class="w-100">
        <div class="container">
            <div>
                <div class="row justify-content-center">
                    <div class="col-md-5">
                        <div class="h-100 d-flex align-items-center justify-content-center">
                            <div data-aos="fade-right" data-aos-duration="1500">
                                <p id="landing-text">
                                    {{$image['description_ar']}}
                                </p>
                                @if($image['subtitle_ar'] != null || $image['subtitle_ar'] = "")
                                    <div id="landing-name" class="mt-5">
                                        <p>
                                            {{$image['subtitle_ar']}}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <img data-aos="fade-left" data-aos-duration="1500"
                             src="{{asset($image['image_path'])}}"
                             alt="{{$image['image_alt']}}"
                             style="max-width: 350px">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <style>
        #landing-section {
            background-image: url('{{asset("images/app/bg02.png")}}');
        }
    </style>
@endpush
