@props([
    'results',
])

<div
    x-data="{ isOpen: true }"
    x-show="isOpen"
    x-on:keydown.escape.window="isOpen = false"
    x-on:click.away="isOpen = false"
    x-on:open-global-search-results.window="isOpen = true"
    {{ $attributes->class(['filament-global-search-results-container absolute right-0 top-auto z-10 mt-2 w-screen max-w-xs overflow-hidden rounded-xl shadow-xl rtl:left-0 rtl:right-auto sm:max-w-lg']) }}
>
    <div
        @class([
            'max-h-96 overflow-x-hidden overflow-y-scroll rounded-xl bg-white shadow',
            'dark:bg-gray-800' => config('filament.dark_mode'),
        ])
    >
        @forelse ($results->getCategories() as $group => $groupedResults)
            <x-filament::global-search.result-group
                :label="$group"
                :results="$groupedResults"
            />
        @empty
            <x-filament::global-search.no-results-message />
        @endforelse
    </div>
</div>
