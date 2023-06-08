@props([
    'breadcrumbs' => [],
])

<div {{ $attributes->class(['filament-breadcrumbs hidden flex-1 lg:block']) }}>
    <ul class="flex items-center gap-3 text-sm font-medium dark:text-white">
        @foreach ($breadcrumbs as $url => $label)
            <li>
                <a
                    href="{{ is_int($url) ? '#' : $url }}"
                    @class([
                        'text-gray-500 dark:text-gray-400' => $loop->last && (! $loop->first),
                        'dark:text-gray-300' => (! $loop->last) || $loop->first,
                    ])
                >
                    {{ $label }}
                </a>
            </li>

            @if (! $loop->last)
                <li
                    class="h-4 -skew-x-12 border-e border-gray-300 dark:border-gray-500"
                ></li>
            @endif
        @endforeach
    </ul>
</div>
