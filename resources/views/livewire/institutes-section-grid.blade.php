<div class="container">
    <div class="row justify-content-center">
        @foreach($this->institutes as $institute)
            <div class="col-md-2">
                <a href="{{route('institutes.show', ['institute' => $institute->id])}}">
                    <div class="card shadow-sm border-none mb-3" style="height: 180px">
                        <div class="card-body d-flex justify-content-center">
                            <img src="{{ asset($institute->logo_path) }}"
                                 style="width: 100%"
                                 alt="logo"/>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</div>


