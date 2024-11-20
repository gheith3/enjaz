<div id="landing-section">
    {{view("components.layouts.nav")}}
    <div id="landing-section-image" class="w-100">
        <div class="container">
            <div>
                <div class="row justify-content-center">
                    <div class="col-md-5 ">
                        <div class="d-flex align-items-center justify-content-center">
                        <div data-aos="fade-down" data-aos-duration="1500">
                            <p id="landing-text">
                                إن الشباب هم ثروة الأمم وموردها الذي
                                لا ينضب وسواعدها التي تبني، هم حاضر الأمة ومستقبلها، وسوف نحرص على الاستماع لهم
                                وتلمس احتياجاتهم واهتماماتهم وتطلعاتهم ولا شك أنها ستجد العناية التي تستحق
                            </p>
                            <div id="landing-name" class="mt-5">
                                <p>
                                    حضرة صاحب الجلالة السلطان هيثم حفظه الله ورعاه
                                </p>
                            </div>
                        </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <img data-aos="fade-down" data-aos-duration="1500" src="{{asset('images/app/hm.png')}}"
                             alt="Landing Image"
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
