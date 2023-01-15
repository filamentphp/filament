<x-dynamic-component
    :component="$getEntryWrapperView()"
    :entry="$entry"
>
    <ul {{
        $attributes
            ->merge([
                'id' => $getId(),
            ], escape: false)
            ->merge($getExtraAttributes(), escape: false)
            ->class(['filament-infolists-repeatable-entry'])
    }}>
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
                <li class="block p-6 rounded-xl bg-white shadow-sm ring-1 ring-gray-900/10 dark:bg-gray-800 dark:ring-gray-50/10">
                    {{ $container }}
                </li>
            @endforeach
        </x-filament::grid>
    </ul>
</x-dynamic-component>
