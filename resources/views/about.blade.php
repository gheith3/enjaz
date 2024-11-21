<x-layouts.app>
    @section('title', 'About')


    <div>
    <livewire:about-section-intro />
    </div>

    <div style="margin-top: -70px; background-color: white; padding: 2rem; border-radius: 20px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);">
    <livewire:about-section-messages />
    </div>
    <div class="container mx-auto px-4">
            <livewire:about-section-ceo />
        </div>
    
</x-layouts.app> 

<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init();
</script> 