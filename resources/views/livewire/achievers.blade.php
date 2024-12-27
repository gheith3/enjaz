<div id="achievements-section">
    @foreach($this->publicFigures as $category)
        <div class="container">
            <div class="mb-5">
                <x-section-title :title="$category->title_ar"/>
                <div class="row mt-4 pt-2">
                    @foreach($category->achievements as $achievement)
                        <div class="col-md-3">
                            <a href="{{route('achievers.show', ['achiever' => $achievement->publicFigures->first()->id])}}">
                                <div class="achievements-section-item p-2 shadow-sm">
                                    <div class="achievements-section-card shadow-sm"
                                         style="background-image: url('{{asset($achievement->cover_image_path)}}');">
                                        <p class="achievements-section-card-top-title">
                                            {{$achievement->achievementMainType->title_ar}}
                                        </p>
                                        <div class="achievements-section-card-bottom-title">
                                            <p>
                                                @foreach($achievement->publicFigures as $achiever)
                                                    <span class="mx-1">
                                                    {{$achiever->name_ar}}
                                                </span>
                                                @endforeach
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
                <div class="mt-5">
                    <div class="d-flex justify-content-end mt-5">
                        <a class="btn app-btn px-5 mx-3">
                            المزيد
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

</div>
