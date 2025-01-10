<div>
    <div style="background-image: url('{{asset($backgroundImage ?? 'images/app/ABG.svg')}}')">
        {{view("components.layouts.nav")}}

        <div class="pt-5"
             style="background-image: url('{{ asset('images/app/AchieversBG.svg') }}'); min-height: 290px; background-size: auto; background-repeat: no-repeat">
            <div class="container p-3 mb-3">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div>
                            <h2 class="text-center text-white">
                                الإنجازات
                            </h2>
                        </div>
                        <div class="d-flex gap-1 mt-5">
                            <input type="text"
                                   class="form-control"
                                   wire:model="searchQuery"
                                   placeholder="العنوان">
                            <button class="btn btn-md text-white"
                                    wire:click="search">
                                <img src="{{ asset('images\app\searchIcon.svg') }}" alt="Search">
                            </button>
                        </div>
                        <div class="row justify-content-center mt-3">
                            @foreach($this->getCategories() as $key => $item)
                                <button
                                    wire:click="setType('{{$key}}')"
                                    class="col-auto m-1 btn bt-sm rounded rounded-lg {{$key == $selected ? 'border-white text-white' : 'bg-white text-black-50'}}">
                                    {{$item}}
                                </button>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>


    <div class="container mt-5">
            @foreach($this->achievements as $achievement)

                    <div class="bg-white shadow-sm rounded rounded-lg mb-3">
                        <div class="row m-0 p-0">
                            <div class="col-md-4 m-0 p-0">
                                <img src="{{asset($achievement->cover_image_path)}}"
                                     class="rounded"
                                     style="width: 100%; height: 250px"
                                     alt="">
                            </div>
                            <div class="col-md-8 d-flex align-items-center">
                                <div class="w-100">
                                    <p class="new-achievement-title" style="font-size: 20px; font-weight: bolder">
                                        {{$achievement->title_ar}}
                                    </p>
                                    <div class="d-flex gap-1 mt-1">
                                        <h6><span class="badge achievement-badge p-2">
                                        {{$achievement->achievementMainType->title_ar}}
                                    </span></h6>
                                        <h6><span class="badge achievement-badge p-2">
                                        {{$achievement->subAchievementType->title_ar}}
                                    </span></h6>
                                    </div>
                                    <p class="mt-1" style="font-size: 15px">
                                        {{str($achievement->description_ar)->stripTags()->limit(200)}}
                                    </p>
                                    <div class="d-flex justify-content-end m-1">
                                        <a href="{{route('achievement.show', ['achievement' => $achievement->id])}}"
                                           class="btn app-btn read-more-btn">
                                            اقرأ المزيد
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                {{--                <div class="col-md-4 pb-2" wire:key="{{$achievement->id}}">--}}
                {{--                    @livewire('components.achievement-card', ['achievement' => $achievement],--}}
                {{--                    key("card-{$achievement->id}"))--}}
                {{--                </div>--}}
            @endforeach
    </div>

    <br>
    <br>
    <br>
</div>
