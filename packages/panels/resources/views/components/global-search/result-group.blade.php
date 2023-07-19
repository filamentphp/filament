@props([
    'label',
    'results',
])

<li
    {{ $attributes->class(['fi-global-search-result-group']) }}
>
    <h3
        class="sticky top-0 z-10 border-b border-gray-950/5 bg-white px-4 py-2.5 text-xs font-medium capitalize text-gray-500 dark:border-white/10 dark:bg-gray-900 dark:text-gray-400"
    >
        {{ $label }}
    </h3>

    <ul class="divide-y divide-gray-100 dark:divide-white/10">
        @foreach ($results as $result)
            <x-filament::global-search.result
                :actions="$result->actions"
                :details="$result->details"
                :title="$result->title"
                :url="$result->url"
            />
        @endforeach
    </ul>
</li>
