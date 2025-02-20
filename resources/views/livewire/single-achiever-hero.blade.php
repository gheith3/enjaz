<div>

    <livewire:page-header
        :title="$achiever->name_ar"
        :header-image="$achiever->image_path"/>

    <div class="mt-5">
        <div class="social-icons" style="display: flex; justify-content: center; align-items: center; gap: 10px;">
            <div class="social-icon-container">
                <span class="social-text">@twitter</span>
                <img src="{{ asset('images/app/twitterAch.svg') }}" alt="Phone" class="social-icon">

            </div>
            <div class="social-icon-container">
                <span class="social-text">@Instagram</span>
                <img src="{{ asset('images/app/instagramAch.svg') }}" alt="Email" class="social-icon">

            </div>
            <div class="social-icon-container">
                <span class="social-text">email@email.com</span>
                <img src="{{ asset('images/app/emailAch.svg') }}" alt="Email" class="social-icon">

            </div>
            <div class="social-icon-container">
                <span class="social-text">@LinkedIn</span>
                <img src="{{ asset('images/app/linkedinAch.svg') }}" alt="Email" class="social-icon">
            </div>
        </div>
    </div>

    <div class="mt-5">
        <div class="container">
            <div class="app-card p-3" id="statistics-card">
                <div class="story-section">
                    <h2 class="story-title">قصتي</h2>
                    <div class="story-text" id="story-text" style="margin-top: 35px;">
                        {!! $achiever->description_ar !!}
                    </div>
                </div>
            </div>
            <div class="app-card" style="margin-top: 80px">
                <div class="row justify-content-center">
                    <div class="col-md-4 pb-3 pt-3">
                        <div data-aos="fade-in" data-aos-duration="1800">
                            <div class="text-center">
                                <img src="{{asset("images/badge.svg")}}"
                                     class="statistics-image"
                                     alt="achievement">
                            </div>
                            <div class=" mt-4">
                                <h5 class="text-center statistics-title text-black-50">
                                    الإنجازات
                                </h5>
                                <h3 class="text-center statistics-data text-black-50">
                                    {{$achievements}}
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 pb-3 pt-3">
                        <div data-aos="fade-in" data-aos-duration="1800">
                            <div class="text-center">
                                <img src="{{asset("images/categories.svg")}}"
                                     class="statistics-image"
                                     alt="achievement">
                            </div>
                            <div class=" mt-4">
                                <h5 class="text-center statistics-title text-black-50">
                                    المجالات
                                </h5>
                                <h3 class="text-center statistics-data text-black-50">
                                    {{$types}}
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <livewire:new-achievements
                    :section-title="'الإنجازات'"
                    :show-all="true"
                    :achiever-id="$achiever->id"/>
            </div>
        </div>
    </div>

    <br>
</div>
@push('scripts')
    <script>
        function toggleStory() {
            var moreText = document.getElementById("more-text");
            var btn = document.getElementById("show-more-btn");
            if (moreText.style.display === "none") {
                moreText.style.display = "inline";
                btn.innerHTML = "اقرأ أقل <span class='up-arrow'>↑</span>";
            } else {
                moreText.style.display = "none";
                btn.innerHTML = "اقرأ المزيد <span class='down-arrow'>↓</span>";
            }
        }
    </script>

@endpush
