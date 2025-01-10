<a href="{{route('institutes.show', ['institute' => $organization->id])}}">
    <div class="bg-white shadow-sm pt-2">
        <div style="position: relative;">
            <img src="{{ asset($organization->logo_path) }}"
                 style="width: 100%; height: 180px"
                 alt="{{$organization->name_ar}}"/>
            <div
                style="position: absolute; bottom: 0; width: 100%; background: rgb(19,96,104, 0.7); color: white; text-align: center;">
                {{ $organization->name_ar }}
            </div>
        </div>
    </div>
</a>
