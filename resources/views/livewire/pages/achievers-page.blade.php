<div>
    <div class="nav-bg">
        {{view("components.layouts.nav")}}
        <div class="pt-5" >
            <div class="container p-3 mb-3">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div>
                            <h2 class="text-center text-white">
                                المنجزين
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
        <div class="row">
            @foreach($achievers as $achiever)
                <div class="col-md-3 p-3" wire:key="{{$achiever->id}}">
                    @livewire('components.achiever-card', ['achiever' => $achiever],
                    key("card-{$achiever->id}"))
                </div>
            @endforeach
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-center">
        <div class="pagination">
            {{ $achievers->links('vendor.pagination.custom') }}
        </div>
    </div>

    <br>
    <br>
    <br>
</div>
