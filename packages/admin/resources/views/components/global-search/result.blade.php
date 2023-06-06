@props([
    'actions' => [],
    'details' => [],
    'title',
    'url',
])

<li {{ $attributes->class(['filament-global-search-result']) }}>
    <div
        class="relative block px-6 py-4 hover:bg-gray-500/5 focus:bg-gray-500/5 focus:ring-1 focus:ring-gray-300"
    >
        <a href="{{ $url }}" class="">
            <p
                @class([
                    'font-medium',
                    'dark:text-gray-200' => config('filament.dark_mode'),
                ])
            >
                {{ $title }}
            </p>

            <p
                @class([
                    'space-x-2 text-sm font-medium text-gray-500 rtl:space-x-reverse',
                    'dark:text-gray-400' => config('filament.dark_mode'),
                ])
            >
                @foreach ($details as $label => $value)
                    <span>
                        <span
                            @class([
                                'font-medium text-gray-700',
                                'dark:text-gray-200' => config('filament.dark_mode'),
                            ])
                        >
                            {{ $label }}:
                        </span>

                        <span>
                            {{ $value }}
                        </span>
                    </span>
                @endforeach
            </p>
        </a>

        @if ($actions)
            <x-filament::global-search.actions :actions="$actions" />
        @endif
    </div>
</li>
