<div>
    <div class="pb-3" style="background-image: url('{{asset($backgroundImage ?? 'images/app/ABG.svg')}}');
     height: 450px;
    display: flex; flex-direction: column; justify-content: space-between; background-size: cover; background-repeat: no-repeat">

        <div>
            {{view("components.layouts.nav")}}
        </div>
        <div class="header-content">
            <div class="container">
                <div class="row w-100 h-100">
                    @if($headerImage)
                        <div class="col-md-auto">
                            <img src="{{ asset($headerImage) }}"
                                 style="max-width: 200px; min-width: 180px"
                                 alt="logo"
                                 class="achiever-image">
                        </div>
                    @endif
                    <div class="col-md-6">
                        <div class="d-flex align-items-center" style="height: 100%;">
                            <div>
                                @if($subTitle)
                                    <h4 class="header-title text-white">
                                        {{ $subTitle }}
                                    </h4>
                                @endif
                                @if($title)
                                    <h1 class="header-title text-white">
                                        {{ $title }}
                                    </h1>
                                @endif
                                @if($description)
                                    <p class="header-description text-white-50">
                                        {{ $description }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
