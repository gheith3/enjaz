<div class="container">
    <div class="row justify-content-center">
        @foreach($this->institutes as $institute)
            <div class="col-md-4">
                <div class="card shadow-sm border-none mb-3" style="height: 250px">
                    <div class="card-body"
                         style="background-image: url('{{ asset($institute->logo_path) }}');
                          background-repeat: no-repeat;
                            background-size: auto;
                          background-position: center; ">

                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>


