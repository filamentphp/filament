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
                <x-filament::icon
                    :name="$icon"
                    :style="\Filament\Support\get_color_css_variables($getColor($state) ?? 'gray', shades: [500])"
                    @class([
                        'fi-in-icon-icon text-custom-500',
                        match ($size = ($getSize($state) ?? 'lg')) {
                            'xs' => 'fi-in-icon-icon-size-xs h-3 w-3',
                            'sm' => 'fi-in-icon-icon-size-sm h-4 w-4',
                            'md' => 'fi-in-icon-icon-size-md h-5 w-5',
                            'lg' => 'fi-in-icon-icon-size-lg h-6 w-6',
                            'xl' => 'fi-in-icon-icon-size-xl h-7 w-7',
                            default => $size,
                        },
                    ])
                />
            @endif
        @endforeach
    </div>
</x-dynamic-component>
