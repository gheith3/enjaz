<div class="about-section">
    {{view("components.layouts.nav")}}
    <div class="container py-5">
        <div style="margin-top: 80px" class="row align-items-center">
            <!-- Text Content - Left Half -->
            <div class="col-lg-6">
                <p class="lead mb-4 text-white">
                    {{$teemSettings->introText}}
                </p>
            </div>

            <!-- Images Grid - Right Half -->
            <div class="col-lg-6">
                <div class="row g-3">
                    <div class="col-6">
                        <img src="{{ asset($teemSettings->introImageOne) }}"
                             style="height: 280px; width: 100%"
                             alt="About Us Image 1"
                             class="img-fluid rounded shadow">
                    </div>
                    <div class="col-6">
                        <img src="{{ asset($teemSettings->introImageTwo) }}"
                             style="height: 280px; width: 100%"
                             alt="About Us Image 2" class="img-fluid rounded shadow">
                    </div>
                    <div class="col-6">
                        <img src="{{ asset($teemSettings->introImageThree) }}"
                             style="height: 280px; width: 100%"
                             alt="About Us Image 3" class="img-fluid rounded shadow">
                    </div>
                    <div class="col-6">
                        <img src="{{ asset($teemSettings->introImageFour) }}"
                             style="height: 280px; width: 100%"
                             alt="About Us Image 4" class="img-fluid rounded shadow">
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
