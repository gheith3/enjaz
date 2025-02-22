<div class="about-section">
    {{view("components.layouts.nav")}}
    <div class="container py-5">
        <div class="row justify-content-center">
            <!-- Text Content - Left Half -->
            <div class="col-md-6 d-flex align-items-center pb-3">
                <p class="lead mb-4 text-white">
                    {{$teemSettings->introText}}
                </p>
            </div>

            <!-- Images Grid - Right Half -->
            <div class="col-lg-4">
                <div class="row">
                    <div class="col-6 mb-2">
                        <div class="app-card">
                            <img src="{{ Storage::url($teemSettings->introImageOne) }}"
                                 style="height: 180px; width: 100%"
                                 alt="About Us Image 1">
                        </div>
                    </div>
                    <div class="col-6 mb-2">
                        <div class="app-card">
                            <img src="{{ Storage::url($teemSettings->introImageTwo) }}"
                                 style="height: 180px; width: 100%"
                                 alt="About Us Image 2">
                        </div>
                    </div>
                    <div class="col-6 mb-2">
                        <div class="app-card">
                            <img src="{{ Storage::url($teemSettings->introImageThree) }}"
                                 style="height: 180px; width: 100%"
                                 alt="About Us Image 3">
                        </div>
                    </div>
                    <div class="col-6 mb-2">
                        <div class="app-card">
                            <img src="{{ Storage::url($teemSettings->introImageFour) }}"
                                 style="height: 180px; width: 100%"
                                 alt="About Us Image 4">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <style>
        .about-section {
            position: relative;
            width: 100%;
            min-height: 50vh;
            border: 1px solid #707070;
            background: transparent linear-gradient(180deg, #0B626A 0%, #063135 100%) 0% 0% no-repeat padding-box;
            border-radius: 0px 0px 250px 0px;
            opacity: 1;
        }
    </style>
@endpush
