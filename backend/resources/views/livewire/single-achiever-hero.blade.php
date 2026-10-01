@section('title', $achiever->name_ar)

<div>

    <livewire:page-header :title="$achiever->name_ar" :description="$achiever->description_ar" :background-image="null" :header-image="$achiever->image_path" />

    <div class="mt-5">


        <div class="mt-5">
            <div class="container">
                <div class="app-card p-3" id="statistics-card">
                    <div class="story-section">
                        <h2 class="story-title">قصتي</h2>
                        <div class="story-text" id="story-text" style="margin-top: 35px;">
                            {!! $achiever->story_ar !!}
                        </div>
                    </div>
                    @if (count($achiever->getSocialMediaLinks()) > 0)

                        <hr>
                        <div class="row justify-content-center">
                            @foreach ($achiever->getSocialMediaLinks() as $social_media)
                                <div class="col-md-1 col-4 p-4 d-flex justify-content-center">
                                    <a data-aos="fade-in" data-aos-duration="{{ $loop->index * 1500 }}" target="_blank"
                                        href="{{ $social_media['url'] }}">
                                        <img src="{{ $social_media['icon'] }}" class="social-icon"
                                            alt="{{ $social_media['account'] }}">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                @if ($achievements != 0 || $types != 0)
                    <div class="app-card" style="margin-top: 80px">
                        <div class="row justify-content-center">
                            <div class="col-md-4 pb-3 pt-3">
                                <div data-aos="fade-in" data-aos-duration="1800">
                                    <div class="text-center">
                                        <img src="{{ asset('images/badge.svg') }}" class="statistics-image"
                                            alt="achievement">
                                    </div>
                                    <div class=" mt-4">
                                        <h5 class="text-center statistics-title text-black-50">
                                            الإنجازات
                                        </h5>
                                        <h3 class="text-center statistics-data text-black-50">
                                            {{ $achievements }}
                                        </h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 pb-3 pt-3">
                                <div data-aos="fade-in" data-aos-duration="1800">
                                    <div class="text-center">
                                        <img src="{{ asset('images/categories.svg') }}" class="statistics-image"
                                            alt="achievement">
                                    </div>
                                    <div class=" mt-4">
                                        <h5 class="text-center statistics-title text-black-50">
                                            المجالات
                                        </h5>
                                        <h3 class="text-center statistics-data text-black-50">
                                            {{ $types }}
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div>
                    <livewire:new-achievements :section-title="'الإنجازات'" :show-all="true" :achiever-id="$achiever->id" />
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
