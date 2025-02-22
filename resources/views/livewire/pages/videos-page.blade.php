<div>
    <div class="nav-bg">
        {{view("components.layouts.nav")}}
        <div class="mt-5 mb-2">
            <h1 class="text-white text-center">
                لقائات مسجلة
            </h1>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-4">
                        <div class="search-box">
                            <input type="text" 
                                   wire:model.live="searchQuery"
                                   class="form-control"
                                   placeholder="ابحث...">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>
    </div>
    <div style="min-height: 80vh">
        <div class="container mt-5">
            <!-- Add search box -->
           

            <!-- Existing video list -->
            <div class="row justify-content-center">
                <div class="col-md-10">
                    @foreach($this->getList() as $item)

                    <div class="bg-white shadow-sm rounded rounded-lg mb-3">
                        <div class="row m-0 p-0">
                            <div class="col-md-4 m-0 p-0">
                                <img src="{{Storage::url($item->cover_image)}}"
                                     class="rounded"
                                     style="width: 100%; height: 250px"
                                     alt="">
                            </div>
                            <div class="col-md-8 d-flex align-items-center">
                                <div class="w-100">
                                    <p class="new-achievement-title" style="font-size: 20px; font-weight: bolder">
                                        {{$item->title}}
                                    </p>
    
                                    <p class="mt-1" style="font-size: 15px">
                                        {{str($item->description)->stripTags()}}
                                    </p>
                                    <div class="d-flex justify-content-end m-1">
                                        <a href="{{$item->url}}"
                                           target="_blank"
                                           class="btn app-btn read-more-btn">
                                            مشاهدة
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

        <div class="mt-4 d-flex justify-content-center">
            <div class="pagination">
                {{ $videos->links('vendor.pagination.custom') }}
            </div>
        </div>
    </div>
</div>


