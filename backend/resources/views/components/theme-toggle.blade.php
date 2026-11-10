{{-- Theme switch (auto -> light -> dark). Needs <x-theme-script /> in <head>. --}}
<button type="button" data-theme-toggle aria-label="Change colour theme"
    {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 rounded-full border border-current/20 px-3 py-1 text-xs opacity-80 transition hover:opacity-100']) }}>
    <span aria-hidden="true" class="relative size-3 overflow-hidden rounded-full border border-current">
        <span class="absolute inset-y-0 right-0 w-1/2 bg-current"></span>
    </span>
    <span data-theme-label>Auto</span>
</button>
