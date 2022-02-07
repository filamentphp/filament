@props([
    'breadcrumbs' => [],
])

<div {{ $attributes->class(['flex-1 filament-breadcrumbs']) }}>
    <ul @class([
        'hidden gap-4 items-center font-medium text-sm lg:flex',
        'dark:text-white' => config('filament.dark_mode'),
    ])>
        @foreach ($breadcrumbs as $url => $label)
            <li>
                <a
                    href="{{ is_int($url) ? '#' : $url }}"
                    @class([
                        'dark:text-gray-300' => (! $loop->last) && config('filament.dark_mode'),
                        'text-gray-500' => $loop->last,
                        'dark:text-gray-400' => $loop->last && config('filament.dark_mode'),
                    ])
                >
                    {{ $label }}
                </a>
            </li>

            @if (! $loop->last)
                <li @class([
                    'h-6 border-r border-gray-300 -skew-x-12',
                    'dark:border-gray-500' => config('filament.dark_mode'),
                ])></li>
            @endif
        @endforeach
    </ul>
</div>
