<div
    {{
        $attributes
            ->merge($getExtraAttributes(), escape: false)
            ->class([
                'fi-ta-icon-column flex flex-wrap gap-1',
                'flex-col' => $isListWithLineBreaks(),
                'px-4 py-3' => ! $isInline(),
            ])
    }}
>
    @foreach (\Illuminate\Support\Arr::wrap($getState()) as $state)
        @if ($icon = $getIcon($state))
            <x-filament::icon
                :name="$icon"
                :style="\Filament\Support\get_color_css_variables($getColor($state) ?? 'gray', shades: [500])"
                @class([
                    'fi-ta-icon-column-icon text-custom-500',
                    match ($size = ($getSize($state) ?? 'lg')) {
                        'xs' => 'fi-ta-icon-column-icon-size-xs h-3 w-3',
                        'sm' => 'fi-ta-icon-column-icon-size-sm h-4 w-4',
                        'md' => 'fi-ta-icon-column-icon-size-md h-5 w-5',
                        'lg' => 'fi-ta-icon-column-icon-size-lg h-6 w-6',
                        'xl' => 'fi-ta-icon-column-icon-size-xl h-7 w-7',
                        default => $size,
                    },
                ])
            />
        @endif
    @endforeach
</div>
