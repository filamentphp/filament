@php
    use Filament\Tables\Columns\IconColumn\IconColumnSize;
@endphp

<div
    {{
        $attributes
            ->merge($getExtraAttributes(), escape: false)
            ->class([
                'fi-ta-icon flex flex-wrap gap-1.5',
                'px-3 py-4' => ! $isInline(),
                'flex-col' => $isListWithLineBreaks(),
            ])
    }}
>
    @if (count($arrayState = \Illuminate\Support\Arr::wrap($getState())))
        @foreach ($arrayState as $state)
            @if ($icon = $getIcon($state))
                @php
                    $color = $getColor($state) ?? 'gray';
                    $size = $getSize($state) ?? IconColumnSize::Large;
                @endphp

                <x-filament::icon
                    :icon="$icon"
                    @class([
                        'fi-ta-icon-item',
                        match ($size) {
                            IconColumnSize::ExtraSmall, 'xs' => 'fi-ta-icon-item-size-xs h-3 w-3',
                            IconColumnSize::Small, 'sm' => 'fi-ta-icon-item-size-sm h-4 w-4',
                            IconColumnSize::Medium, 'md' => 'fi-ta-icon-item-size-md h-5 w-5',
                            IconColumnSize::Large, 'lg' => 'fi-ta-icon-item-size-lg h-6 w-6',
                            IconColumnSize::ExtraLarge, 'xl' => 'fi-ta-icon-item-size-xl h-7 w-7',
                            default => $size,
                        },
                        match ($color) {
                            'gray' => 'fi-color-gray text-gray-400 dark:text-gray-500',
                            default => 'fi-color-custom text-custom-500 dark:text-custom-400',
                        },
                    ])
                    @style([
                        \Filament\Support\get_color_css_variables(
                            $color,
                            shades: [400, 500],
                        ) => $color !== 'gray',
                    ])
                />
            @endif
        @endforeach
    @elseif (($placeholder = $getPlaceholder()) !== null)
        <x-filament-tables::columns.placeholder>
            {{ $placeholder }}
        </x-filament-tables::columns.placeholder>
    @endif
</div>
