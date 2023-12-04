@php
    use Filament\Infolists\Components\IconEntry\IconEntrySize;
@endphp

<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div
        {{
            $attributes
                ->merge($getExtraAttributes(), escape: false)
                ->class([
                    'fi-in-icon flex flex-wrap gap-1.5',
                ])
        }}
    >
        @if (count($arrayState = \Illuminate\Support\Arr::wrap($getState())))
            @foreach ($arrayState as $state)
                @if ($icon = $getIcon($state))
                    @php
                        $color = $getColor($state) ?? 'gray';
                        $size = $getSize($state) ?? IconEntrySize::Large;
                    @endphp

                    <x-filament::icon
                        :icon="$icon"
                        @class([
                            'fi-in-icon-item',
                            match ($size) {
                                IconEntrySize::ExtraSmall, 'xs' => 'fi-in-icon-item-size-xs h-3 w-3',
                                IconEntrySize::Small, 'sm' => 'fi-in-icon-item-size-sm h-4 w-4',
                                IconEntrySize::Medium, 'md' => 'fi-in-icon-item-size-md h-5 w-5',
                                IconEntrySize::Large, 'lg' => 'fi-in-icon-item-size-lg h-6 w-6',
                                IconEntrySize::ExtraLarge, 'xl' => 'fi-in-icon-item-size-xl h-7 w-7',
                                IconEntrySize::TwoExtraLarge, IconEntrySize::ExtraExtraLarge, '2xl' => 'fi-in-icon-item-size-2xl h-8 w-8',
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
                                alias: 'infolists::components.icon-entry.item',
                            ) => $color !== 'gray',
                        ])
                    />
                @endif
            @endforeach
        @elseif (($placeholder = $getPlaceholder()) !== null)
            <x-filament-infolists::entries.placeholder>
                {{ $placeholder }}
            </x-filament-infolists::entries.placeholder>
        @endif
    </div>
</x-dynamic-component>
