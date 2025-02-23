<div>
    <div class="bg-primary">
        {{view("components.layouts.nav")}}
        <div class="py-5">
            <h1 class="text-white text-center mb-4">
                الإصدارات
            </h1>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="text" 
                                   wire:model.live="searchQuery"
                                   class="form-control"
                                   placeholder="ابحث...">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="min-vh-75">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    @foreach($publications as $item)
                        <div class="card shadow-sm mb-4">
                            <div class="row g-0">
                                <div class="col-md-4">
                                    <img src="{{Storage::url($item->cover_image)}}"
                                         class="img-fluid rounded-start h-100 object-fit-cover"
                                         alt="{{$item->title}}">
                                </div>
                                <div class="col-md-8">
                                    <div class="card-body d-flex flex-column h-100">
                                        <h5 class="card-title fw-bold">
                                            {{$item->title}}
                                        </h5>
                                        <p class="card-text flex-grow-1">
                                            {{str($item->description)->stripTags()}}
                                        </p>
                                        <div class="text-end mt-3">
                                            <a href="{{$item->url}}"
                                               target="_blank"
                                               class="btn btn-primary">
                                                تحميل
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-center mb-5">
            {{ $publications->links('vendor.pagination.custom') }}
        </div>
    </div>
</div>

@push('styles')
    <style>
        {{--body {--}}
        {{--    background-image: url('{{asset("images/app/bg02.png")}}');--}}
        {{--    background-repeat: no-repeat;--}}
        {{--    background-size: cover;--}}
        {{--    background-position: center;--}}
        {{--}--}}
    </style>
@endpush
