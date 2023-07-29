@props([
    'label',
    'results',
])

<li
    {{ $attributes->class(['fi-global-search-result-group']) }}
>
    <div
        class="sticky top-0 z-10 border-b border-gray-200 bg-gray-50 dark:border-white/10 dark:bg-gray-900"
    >
        <h3
            class="px-4 py-2 text-sm font-semibold capitalize text-gray-950 dark:bg-white/5 dark:text-white"
        >
            {{ $label }}
        </h3>
    </div>

    <ul class="divide-y divide-gray-200 dark:divide-white/10">
        @foreach ($results as $result)
            <x-filament-panels::global-search.result
                :actions="$result->actions"
                :details="$result->details"
                :title="$result->title"
                :url="$result->url"
            />
        @endforeach
    </ul>
</li>
