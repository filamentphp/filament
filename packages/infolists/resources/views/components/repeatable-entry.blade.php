<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    @php
        $isWrappedInCard = $isWrappedInCard();
    @endphp

    <ul
        {{
            $attributes
                ->merge([
                    'id' => $getId(),
                ], escape: false)
                ->merge($getExtraAttributes(), escape: false)
                ->class(['filament-infolists-repeatable-entry'])
        }}
    >
        <x-filament::grid
            :default="$getGridColumns('default')"
            :sm="$getGridColumns('sm')"
            :md="$getGridColumns('md')"
            :lg="$getGridColumns('lg')"
            :xl="$getGridColumns('xl')"
            :two-xl="$getGridColumns('2xl')"
            class="gap-6"
        >
            @foreach ($getChildComponentContainers() as $container)
                <li
                    @class([
                        'block',
                        'rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-800 dark:ring-white/20' => $isWrappedInCard,
                    ])
                >
                    {{ $container }}
                </li>
            @endforeach
        </x-filament::grid>
    </ul>
</x-dynamic-component>
