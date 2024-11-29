<div>
@if($this->person != null)
    <div id="public-figure">
        <div class="container">
            <div class="row justify-content-center pt-5">
                <div class="col-md-4 text-center">
                    <img data-aos="fade-down" data-aos-duration="1500"
                         src="{{asset($this->person->image_path)}}"
                         alt="Landing Image"
                         style="max-width: 450px">
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                    <div class="">
                        <p class="public-figure-card-title px-2">
                            {{$this->person->name_ar}}
                        </p>
                        <div class="card public-figure-card">
                            <div class="card-body">
                                <p id="public-figure-text">
                                    {{str($this->person->description_ar)->limit(300)->toHtmlString()}}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endif

</div>
