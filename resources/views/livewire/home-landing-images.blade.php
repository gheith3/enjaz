<div id="landing-section">
    <div>
        {{view("components.layouts.nav")}}
        <div class="pt-5" style="min-height: 290px; background-size: auto; background-repeat: no-repeat">
            <div class="container">
                <div class="row justify-content-center pt-5">
                    <div class="col-md-4 text-center">
                        <img data-aos="fade-in" data-aos-duration="1800"
                             src="{{asset($image['image_path'])}}"
                             alt="Landing Image"
                             id="landing-image">
                    </div>
                    <div class="col-md-5 d-flex align-items-center pb-3">
                        <div data-aos="fade-in" data-aos-duration="1800">
                            <div class="pt-3">
                                <p id="landing-image-text" class="text-center">
                                    {{$image['description_ar']}}
                                </p>
                                <p id="landing-image-subtitle" class="text-center">
                                    {{$image['subtitle_ar']}}
                                </p>
                            </div>
                        </div>
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
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
        }
    </style>
@endpush
