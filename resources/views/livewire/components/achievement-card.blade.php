<div>
    <div class="app-card mb-3">
        <img src="{{asset($achievement->cover_image_path)}}"
             class="rounded"
             style="width: 100%; height: 210px"
             alt="">
        <div class="px-2 pb-2" style="min-height: 170px">
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
                    {{str($achievement->description_ar)->stripTags()->limit(100)}}
                </p>
            </div>
            <div class="d-flex justify-content-end">
                <a href="{{route('achievement.show', ['achievement' => $achievement->id])}}"
                   class="btn app-btn read-more-btn">
                    اقرأ المزيد
                </a>
            </div>
        </div>
    </div>
</div>
