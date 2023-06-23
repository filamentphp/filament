<div
    {{
        $attributes
            ->merge($getExtraAttributes(), escape: false)
            ->class([
                'filament-tables-icon-column flex flex-wrap gap-1',
                'flex-col' => $isListWithLineBreaks(),
                'px-4 py-3' => ! $isInline(),
            ])
    }}
>
    @foreach (\Illuminate\Support\Arr::wrap($getState()) as $state)
        @if ($icon = $getIcon($state))
            <x-filament::icon
                :name="$icon"
                alias="tables::columns.icon"
                color="text-custom-500"
                :size="
                    match ($size = ($getSize($state) ?? 'lg')) {
                        'xs' => 'h-3 w-3 filament-tables-icon-column-icon-size-xs',
                        'sm' => 'h-4 w-4 filament-tables-icon-column-icon-size-sm',
                        'md' => 'h-5 w-5 filament-tables-icon-column-icon-size-md',
                        'lg' => 'h-6 w-6 filament-tables-icon-column-icon-size-lg',
                        'xl' => 'h-7 w-7 filament-tables-icon-column-icon-size-xl',
                        default => $size,
                    }
                "
                :style="\Filament\Support\get_color_css_variables($getColor($state) ?? 'gray', shades: [500])"
            />
        @endif
    @endforeach
</div>
