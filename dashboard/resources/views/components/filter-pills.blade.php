@props([
    'items' => [],
    'active' => null,
    'param' => 'type',
])

@foreach ($items as $item)
    @php
        $value = $item['value'] ?? null;
        $isActive = (string) $active === (string) $value || ($value === null && ($active === null || $active === ''));
        $query = array_merge(request()->except('page'), [$param => $value]);
    @endphp
    <a href="{{ url()->current() . '?' . http_build_query(array_filter($query, fn ($v) => $v !== null && $v !== '')) }}"
       @class(['filter-pill', 'is-active' => $isActive])>
        @if (! empty($item['icon']))
            <img src="{{ \Illuminate\Support\Str::startsWith($item['icon'], ['http', '/']) ? $item['icon'] : \Illuminate\Support\Facades\Storage::url($item['icon']) }}" alt="">
        @endif
        <span>{{ $item['label'] }}</span>
    </a>
@endforeach
