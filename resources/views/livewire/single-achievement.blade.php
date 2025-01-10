<div>

    <livewire:page-header
        :background-image="$achievement->cover_image_path"
        :title="$achievement->title_ar"
        :sub-title="$achievement->achievementMainType->title_ar"/>


    <div class="mt-5">
        <div class="container">
            <div class="app-card p-3" id="statistics-card">
                <div class="story-text" id="story-text">
                    {!! $achievement->description_ar !!}
                </div>
                <div class="d-flex justify-content-between">
                    <div class="row">
                        <div class="col-auto pb-2">
                            <span class="achievement-label">{{$achievement->created_at->year}}</span>
                        </div>
                        <div class="col-auto pb-2">
                            <span class="achievement-label">{{$achievement->achievementMainType->title_ar}}</span>
                        </div>
                        <div class="col-auto pb-2">
                            <span class="achievement-label">{{$achievement->subAchievementType->title_ar}}</span>
                        </div>
                        <div class="col-auto pb-2">
                            <span class="achievement-label">{{$achievement->achievementScope->title_ar}}</span>
                        </div>
                    </div>
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="black" viewBox="0 0 24 24" width="24" height="24">
                            <path
                                d="M12 15.5l-4-4h3V3h2v8.5h3l-4 4zm0 5.5C6.48 21 2 16.52 2 11h2c0 4.42 3.58 8 8 8s8-3.58 8-8h2c0 5.52-4.48 10-10 10z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div>
        <livewire:new-achievements
            :section-title="'المزيد'"
            :active-achievement="$achievement->id"
            :achievementMainTypeId="$achievement->achievement_main_type_id"/>
    </div>
    <br>
</div>

@push('styles')
    <style>
        .achievement-label {
            background-color: rgba(168, 131, 68, 0.44);
            color: white;
            border-radius: 12px;
            padding: 5px 10px;
        }
    </style>
@endpush
