@props([
    'active' => false,
    'icon',
    'url',
])

<li class="filament-sidebar-item">
    <a
        href="{{ $url }}"
        @class([
            'flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition',
            'hover:bg-gray-500/5 focus:bg-gray-500/5' => ! $active,
            'dark:text-gray-300 dark:hover:bg-gray-700' => (! $active) && config('filament.dark_mode'),
            'bg-primary-500 text-white' => $active,
        ])
    >
        <x-dynamic-component :component="$icon" class="h-5 w-5" />

        <span>
            {{ $slot }}
        </span>
    </a>
</li>
