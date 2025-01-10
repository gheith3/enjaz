<a href="{{route('institutes.show', ['institute' => $organization->id])}}">
    <div class="app-card shadow-sm">
        <div style="position: relative;">
            <div class="p-2">
                <img src="{{ asset($organization->logo_path) }}"
                     style="width: 100%; height: 180px"
                     alt="{{$organization->name_ar}}"/>
            </div>
            <div
                style="font-size: 16px; padding: 5px 15px; position: absolute; bottom: 0; width: 100%; background: rgb(19,96,104, 0.7); color: white; text-align: center;">
                {{ $organization->name_ar }}
            </div>
        </div>
    </div>
</a>
