@props([
    'label',
    'results',
])

<ul
    {{
        $attributes->class([
            'filament-global-search-result-group divide-y',
            'dark:divide-gray-700' => config('filament.dark_mode'),
        ])
    }}
>
    <li class="sticky top-0 z-10">
        <header
            @class([
                'bg-gray-50/80 px-6 py-2 backdrop-blur-xl backdrop-saturate-150',
                'dark:bg-gray-700' => config('filament.dark_mode'),
            ])
        >
            <p
                @class([
                    'text-xs font-bold uppercase tracking-wider text-gray-500',
                    'dark:text-gray-400' => config('filament.dark_mode'),
                ])
            >
                {{ $label }}
            </p>
        </header>
    </li>

    @foreach ($results as $result)
        <x-filament::global-search.result
            :actions="$result->actions"
            :details="$result->details"
            :title="$result->title"
            :url="$result->url"
        />
    @endforeach
</ul>
