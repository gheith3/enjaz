<x-layouts.app>
    @section('title', 'Home')

    <div>
        <livewire:home-landing-images />
    </div>

    <div id="page-content-section">
        <div class="pt-5">
            <livewire:statics />
        </div>

        <div style="margin-top: 80px">
            <livewire:new-achievements />
        </div>
    </div>

    <div>
        <livewire:public-figure />
    </div>

    <div>
        <livewire:achievements />
    </div>

</x-layouts.app>


