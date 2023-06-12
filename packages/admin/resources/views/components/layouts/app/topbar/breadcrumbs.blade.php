@props([
    'breadcrumbs' => [],
])

<div {{ $attributes->class(['filament-breadcrumbs flex-1']) }}>
    <ul
        @class([
            'hidden items-center gap-4 text-sm font-medium lg:flex',
            'dark:text-white' => config('filament.dark_mode'),
        ])
    >
        @foreach ($breadcrumbs as $url => $label)
            <li>
                <a
                    href="{{ is_int($url) ? '#' : $url }}"
                    @class([
                        'text-gray-500' => $loop->last && (! $loop->first),
                        'dark:text-gray-300' => ((! $loop->last) || $loop->first) && config('filament.dark_mode'),
                        'dark:text-gray-400' => $loop->last && (! $loop->first) && config('filament.dark_mode'),
                    ])
                >
                    {{ $label }}
                </a>
            </li>

            @if (! $loop->last)
                <li
                    @class([
                        'h-6 -skew-x-12 border-r border-gray-300',
                        'dark:border-gray-500' => config('filament.dark_mode'),
                    ])
                ></li>
            @endif
        @endforeach
    </ul>
</div>
