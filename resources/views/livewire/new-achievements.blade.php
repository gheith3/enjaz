<div>
    <div class="container" id="new-achievement">
        <x-section-title :title="'أحدث الإنجازات'" />
        <div class="row" id="new-achievements-boxes">
            @foreach($this->achievements as $achievement)
                <div class="col-md-4">
                    <div data-aos="fade-up"
                         data-aos-duration="{{ 500 * ($loop->index + 1) }}"
                         class="card m-3 new-achievement-card">
                        <div class="card-body">
                            <img src="{{asset($achievement->cover_image_path)}}"
                                 class="rounded"
                                 style="width: 100%; height: 250px"
                                 alt="">
                            <div class="d-flex gap-1 mt-3">
                                <h6><span class="badge achievement-badge p-2">
                                        {{$achievement->achievementMainType->title_ar}}
                                    </span></h6>
                                <h6><span class="badge achievement-badge p-2">
                                        {{$achievement->subAchievementType->title_ar}}
                                    </span></h6>
                            </div>
                            <div class="mt-3">
                                <p class="text-black-50" style="font-size: 15px">
                                    {{$achievement->created_at->format('Y/m/d')}}
                                </p>
                                <p class="new-achievement-title" style="font-size: 18px">
                                    {{$achievement->title_ar}}
                                </p>
                                <p class="mt-1" style="font-size: 15px">
                                    {{str($achievement->description_ar)->stripTags()->limit(180)}}
                                </p>
                            </div>
                            <div class="d-flex justify-content-end">
                                <a class="btn app-btn read-more-btn" href="">
                                    اقرأ المزيد
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="d-flex justify-content-end mt-5">
            <button data-aos="fade-up" data-aos-duration="1000" class="btn app-btn px-5 mx-3">
                المزيد
            </button>
        </div>
    </div>
</div>
