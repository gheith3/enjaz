<div id="achievements-section">
    @foreach($this->achivments as $category)
        <div class="container">
            <div class="mb-5">
                <x-section-title :title="$category->title_ar"/>
                <div class="row mt-4 pt-2">
                    @foreach($category->achievements as $achievement)
                        <div class="col-md-3">
                            <div class="achievements-section-item p-2 shadow-sm">
                                <div class="achievements-section-card shadow-sm"
                                     style="background-image: url('{{asset($achievement->cover_image_path)}}');">
                                    <p class="achievements-section-card-top-title">
                                        {{$achievement->subAchievementType->title_ar}}
                                    </p>
                                    <div class="achievements-section-card-bottom-title">
                                        <p>
                                            عايشة السيفي
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-5">
                    <div class="d-flex justify-content-end mt-5">
                        <button class="btn app-btn px-5 mx-3">
                            المزيد
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

</div>
