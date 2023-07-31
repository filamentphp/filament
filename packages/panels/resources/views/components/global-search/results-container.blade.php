@props([
    'results',
])

<div
    x-data="{
        isOpen: false,

        open: function (event) {
            this.isOpen = true
        },

        close: function (event) {
            this.isOpen = false
        },
    }"
    x-init="$nextTick(() => open())"
    x-on:click.away="close()"
    x-on:keydown.escape.window="close()"
    x-on:open-global-search-results.window="$nextTick(() => open())"
    x-show="isOpen"
    x-transition:enter-start="opacity-0"
    x-transition:leave-end="opacity-0"
    {{
        $attributes->class([
            'fi-global-search-results-ctn absolute end-0 z-10 mt-2 max-h-96 w-screen max-w-sm overflow-auto rounded-lg bg-white shadow-lg ring-1 ring-gray-950/5 transition dark:bg-gray-900 dark:ring-white/10',
        ])
    }}
>
    @if ($results->getCategories()->isEmpty())
        <x-filament-panels::global-search.no-results-message />
    @else
        <ul class="divide-y divide-gray-200 dark:divide-white/10">
            @foreach ($results->getCategories() as $group => $groupedResults)
                <x-filament-panels::global-search.result-group
                    :label="$group"
                    :results="$groupedResults"
                />
            @endforeach
        </ul>
    @endif
</div>
