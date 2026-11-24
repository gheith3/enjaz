{{-- Level of an achievement as chips (محلي، خليجي، عربي، إقليمي، دولي). --}}
<div class="chips">
    @foreach ($scopes as $id => $label)
        <label class="chip"><input type="radio" name="{{ $name }}" value="{{ $id }}" @checked((string) ($value ?? '') === (string) $id)><span>{{ $label }}</span></label>
    @endforeach
</div>
