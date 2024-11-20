<div>
    @if($this->profile != null)
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-4">
                    <img src="{{$this->profile['src']}}"
                         style="width: 350px"
                         alt="public figure">
                </div>
                <div class="col-md-5">
                    <div class="d-flex flex-column h-100">
                        <div class="d-flex flex-grow-1 justify-content-center align-items-center">
                            <div class="p-3 rounded" style="background: rgba(255,255,255,0.6)">
                                <p style="font-size: 18px">
                                    {{$this->profile['id']}}

                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
