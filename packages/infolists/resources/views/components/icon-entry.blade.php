<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div
        {{
            $attributes
                ->merge($getExtraAttributes(), escape: false)
                ->class([
                    'fi-in-icon flex flex-wrap gap-1',
                ])
        }}
    >
        @foreach (\Illuminate\Support\Arr::wrap($getState()) as $state)
            @if ($icon = $getIcon($state))
                @php
                    $color = $getColor($state) ?? 'gray';
                    $size = $getSize($state) ?? 'lg';
                @endphp

                <x-filament::icon
                    :name="$icon"
                    @class([
                        'fi-in-icon-icon',
                        match ($size) {
                            'xs' => 'fi-in-icon-size-xs h-3 w-3',
                            'sm' => 'fi-in-icon-size-sm h-4 w-4',
                            'md' => 'fi-in-icon-size-md h-5 w-5',
                            'lg' => 'fi-in-icon-size-lg h-6 w-6',
                            'xl' => 'fi-in-icon-size-xl h-7 w-7',
                            default => $size,
                        },
                        match ($color) {
                            'gray' => 'text-gray-400 dark:text-gray-500',
                            default => 'text-custom-500 dark:text-custom-400',
                        },
                    ])
                    @style([
                        \Filament\Support\get_color_css_variables($color, shades: [400, 500]) => $color !== 'gray',
                    ])
                />
            @endif
        @endforeach
    </div>
</x-dynamic-component>
