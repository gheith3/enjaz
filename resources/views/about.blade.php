<x-layouts.app>
    @section('title', 'About')


    <div>
        <livewire:about-section-intro/>
    </div>

    <div style="margin-top: -70px; background-color: white; padding: 2rem;">
        <livewire:about-section-messages/>
    </div>
    <div class="container mx-auto px-4">
        <livewire:about-section-ceo/>
    </div>
    <div style="margin-top: 70px;">
        <livewire:about-section-team/>
    </div>

{{--    <div style="margin-top: 70px;">--}}
{{--        <livewire:about-section-achievements/>--}}
{{--    </div>--}}

{{--    <div class="container mx-auto px-4">--}}
{{--        <livewire:about-section-media/>--}}
{{--    </div>--}}


</x-layouts.app>
