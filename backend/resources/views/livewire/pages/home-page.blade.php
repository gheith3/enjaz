<div>
    @section('title', 'الرئيسية')

    <div >
        <livewire:home-landing-images/>
    </div>

    <div id="page-content-section">
        <div class="pt-5">
            <livewire:statics/>
        </div>

        <div class="mt-5">
            <livewire:new-achievements/>
        </div>
    </div>

    <div>
        <livewire:components.selected-achiever />
    </div>

    <div>
        <livewire:achievers/>
    </div>
</div>
