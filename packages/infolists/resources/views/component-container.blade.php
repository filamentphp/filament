<dl>
    <x-filament::grid
        :default="$getColumns('default')"
        :sm="$getColumns('sm')"
        :md="$getColumns('md')"
        :lg="$getColumns('lg')"
        :xl="$getColumns('xl')"
        :two-xl="$getColumns('2xl')"
        class="filament-infolists-component-container gap-6"
    >
        @foreach ($getComponents() as $infolistComponent)
            <x-filament::grid.column
                :default="$infolistComponent->getColumnSpan('default')"
                :sm="$infolistComponent->getColumnSpan('sm')"
                :md="$infolistComponent->getColumnSpan('md')"
                :lg="$infolistComponent->getColumnSpan('lg')"
                :xl="$infolistComponent->getColumnSpan('xl')"
                :twoXl="$infolistComponent->getColumnSpan('2xl')"
                :defaultStart="$infolistComponent->getColumnStart('default')"
                :smStart="$infolistComponent->getColumnStart('sm')"
                :mdStart="$infolistComponent->getColumnStart('md')"
                :lgStart="$infolistComponent->getColumnStart('lg')"
                :xlStart="$infolistComponent->getColumnStart('xl')"
                :twoXlStart="$infolistComponent->getColumnStart('2xl')"
                @class([
                    match ($maxWidth = $infolistComponent->getMaxWidth()) {
                        'xs' => 'max-w-xs',
                        'sm' => 'max-w-sm',
                        'md' => 'max-w-md',
                        'lg' => 'max-w-lg',
                        'xl' => 'max-w-xl',
                        '2xl' => 'max-w-2xl',
                        '3xl' => 'max-w-3xl',
                        '4xl' => 'max-w-4xl',
                        '5xl' => 'max-w-5xl',
                        '6xl' => 'max-w-6xl',
                        '7xl' => 'max-w-7xl',
                        default => $maxWidth,
                    },
                ])
            >
                {{ $infolistComponent }}
            </x-filament::grid.column>
        @endforeach
    </x-filament::grid>
</dl>
