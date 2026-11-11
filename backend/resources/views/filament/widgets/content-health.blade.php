<x-filament-widgets::widget>
    <x-filament::section heading="صحة المحتوى" description="ما يحتاج إلى استكمال قبل الإطلاق وبعده." icon="heroicon-o-heart">
        @php $issues = $this->issues(); @endphp
        @if (! $issues)
            <p class="text-sm text-gray-500">لا شيء يحتاج إلى متابعة.</p>
        @else
            <ul class="divide-y divide-gray-100 dark:divide-white/5">
                @foreach ($issues as $issue)
                    <li class="flex items-center justify-between gap-4 py-2">
                        <a href="{{ $issue['url'] }}" class="text-sm hover:underline">{{ $issue['label'] }}</a>
                        <x-filament::badge :color="$issue['level']">{{ $issue['count'] }}</x-filament::badge>
                    </li>
                @endforeach
            </ul>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
