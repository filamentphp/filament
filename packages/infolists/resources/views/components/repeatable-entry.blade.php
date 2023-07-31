@php
    $isContained = $isContained();
@endphp

<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <ul
        {{
            $attributes
                ->merge([
                    'id' => $getId(),
                ], escape: false)
                ->merge($getExtraAttributes(), escape: false)
                ->class(['fi-in-repeatable'])
        }}
    >
        <x-filament::grid
            :default="$getGridColumns('default')"
            :sm="$getGridColumns('sm')"
            :md="$getGridColumns('md')"
            :lg="$getGridColumns('lg')"
            :xl="$getGridColumns('xl')"
            :two-xl="$getGridColumns('2xl')"
            class="gap-4"
        >
            @foreach ($getChildComponentContainers() as $container)
                <li
                    @class([
                        'block',
                        'rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10' => $isContained,
                    ])
                >
                    {{ $container }}
                </li>
            @endforeach
        </x-filament::grid>
    </ul>
</x-dynamic-component>
