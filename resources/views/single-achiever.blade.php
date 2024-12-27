<x-layouts.app>
    @section('title', $achiever['name'])


    <div>
        <livewire:single-achiever-hero 
            :name="$achiever['name']" 
            :english_name="$achiever['english_name']" 
            :title="$achiever['title']" 
        />
    </div>
   



</x-layouts.app> 