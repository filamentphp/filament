@props([
    'actions' => [],
    'details' => [],
    'title',
    'url',
])

<li
    {{ $attributes->class(['fi-global-search-result transition duration-75 focus-within:bg-gray-50 hover:bg-gray-50 dark:focus-within:bg-white/5 dark:hover:bg-white/5']) }}
>
    <a
        {{ \Filament\Support\generate_href_html($url) }}
        @class([
            'block outline-none',
            'pe-4 ps-4 pt-4' => $actions,
            'p-4' => ! $actions,
        ])
    >
        <h4 class="text-sm font-medium text-gray-950 dark:text-white">
            {{ $title }}
        </h4>

        @if ($details)
            <dl class="mt-1">
                @foreach ($details as $label => $value)
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        <dt class="inline font-medium">{{ $label }}:</dt>

                        <dd class="inline">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
        @endif
    </a>

    @if ($actions)
        <x-filament-panels::global-search.actions :actions="$actions" />
    @endif
</li>
