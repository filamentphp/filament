@php
    use Filament\Infolists\Components\IconEntry\Enums\IconEntrySize;
@endphp

<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    @php
        $arrayState = $getState();

        if ($arrayState instanceof \Illuminate\Support\Collection) {
            $arrayState = $arrayState->all();
        }

        $arrayState = \Illuminate\Support\Arr::wrap($arrayState);
    @endphp

    <div
        {{
            $attributes
                ->merge($getExtraAttributes(), escape: false)
                ->class([
                    'fi-in-icon flex flex-wrap gap-1.5',
                ])
        }}
    >
        @if (count($arrayState))
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
                                IconEntrySize::ExtraSmall, 'xs' => 'fi-in-icon-item-size-xs size-3',
                                IconEntrySize::Small, 'sm' => 'fi-in-icon-item-size-sm size-4',
                                IconEntrySize::Medium, 'md' => 'fi-in-icon-item-size-md size-5',
                                IconEntrySize::Large, 'lg' => 'fi-in-icon-item-size-lg size-6',
                                IconEntrySize::ExtraLarge, 'xl' => 'fi-in-icon-item-size-xl size-7',
                                IconEntrySize::TwoExtraLarge, IconEntrySize::ExtraExtraLarge, '2xl' => 'fi-in-icon-item-size-2xl size-8',
                                default => $size,
                            },
                            match ($color) {
                                'gray' => 'text-gray-400 dark:text-gray-500',
                                default => 'fi-color-custom text-custom-500 dark:text-custom-400',
                            },
                            is_string($color) ? 'fi-color-' . $color : null,
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
