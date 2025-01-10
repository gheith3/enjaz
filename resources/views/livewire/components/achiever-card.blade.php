<a class="link-text" href="{{route('achievers.show', ['achiever' => $achiever->id])}}">
    <div class="achievers-image-card">
        <div class="app-card">
            <div class="achievers-image-d">
                <div style="position: relative;">
                    <img src="{{ asset($achiever->image_path) }}"
                         alt="{{$achiever->name_ar}}"/>
                    <div class="achievers-image-card-name">
                        {{ $achiever->name_ar }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</a>
