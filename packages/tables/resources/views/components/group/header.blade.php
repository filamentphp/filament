@props([
    'collapsible' => false,
    'description' => null,
    'label' => null,
    'start' => null,
    'title',
])

<div
    @if ($collapsible)
        x-on:click="toggleCollapseGroup(@js($title))"
    @endif
    {{
        $attributes->class([
            'fi-ta-group-header flex w-full items-center gap-x-3 bg-gray-50 px-3 py-2 dark:bg-white/5',
            'cursor-pointer' => $collapsible,
        ])
    }}
>
    {{ $start }}

    <div class="grid">
        <h4 class="text-sm font-medium text-gray-950 dark:text-white">
            @if (filled($label))
                {{ $label }}:
            @endif

            {{ $title }}
        </h4>

        @if (filled($description))
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ $description }}
            </p>
        @endif
    </div>

    @if ($collapsible)
        <x-filament::icon-button
            color="gray"
            icon="heroicon-m-chevron-up"
            icon-alias="tables::grouping.collapse-button"
            :label="filled($label) ? ($label . ': ' . $title) : $title"
            size="sm"
            :x-bind:aria-expanded="'! isGroupCollapsed(' . \Illuminate\Support\Js::from($title) . ')'"
            :x-bind:class="'isGroupCollapsed(' . \Illuminate\Support\Js::from($title) . ') && \'-rotate-180\''"
        />
    @endif
</div>
