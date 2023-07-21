<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    @php
        $isBadge = $isBadge();
        $isListWithLineBreaks = $isListWithLineBreaks();
        $iconPosition = $getIconPosition();
        $isProse = $isProse();
        $url = $getUrl();

        $arrayState = $getState();

        if ($arrayState instanceof \Illuminate\Support\Collection) {
            $arrayState = $arrayState->all();
        }

        if (is_array($arrayState)) {
            if ($listLimit = $getListLimit()) {
                $limitedArrayState = array_slice($arrayState, $listLimit);
                $arrayState = array_slice($arrayState, 0, $listLimit);
            }

            if ((! $isListWithLineBreaks) && (! $isBadge)) {
                $arrayState = implode(
                    ', ',
                    array_map(
                        fn ($value) => $value instanceof \Filament\Support\Contracts\HasLabel ? $value->getLabel() : $value,
                        $arrayState,
                    ),
                );
            }
        }

        $arrayState = \Illuminate\Support\Arr::wrap($arrayState);
    @endphp

    <x-filament-infolists::affixes
        :prefix-actions="$getPrefixActions()"
        :suffix-actions="$getSuffixActions()"
        :attributes="
            \Filament\Support\prepare_inherited_attributes($getExtraAttributeBag())->class([
                'fi-in-text',
            ])
        "
    >
        <{{ $isListWithLineBreaks ? 'ul' : 'div' }}
            @class([
                'list-inside list-disc' => $isBulleted(),
                'flex flex-wrap items-center gap-1' => $isBadge,
            ])
        >
            @foreach ($arrayState as $state)
                @if (filled($formattedState = $formatState($state)))
                    @php
                        $color = $getColor($state) ?? 'gray';
                        $copyableState = $getCopyableState($state) ?? $state;
                        $copyMessage = $getCopyMessage($state);
                        $copyMessageDuration = $getCopyMessageDuration($state);
                        $fontFamily = $getFontFamily($state);
                        $icon = $getIcon($state);
                        $itemIsCopyable = $isCopyable($state);
                        $size = $getSize($state);
                        $weight = $getWeight($state);

                        $proseClasses = \Illuminate\Support\Arr::toCssClasses([
                            'prose max-w-none dark:prose-invert [&>*:first-child]:mt-0 [&>*:last-child]:mb-0',
                            'pt-2' => ! $isLabelHidden(),
                            match ($size) {
                                'xs' => 'prose-xs',
                                'sm', null => 'prose-sm',
                                'base', 'md' => 'prose-base',
                                'lg' => 'prose-lg',
                                default => $size,
                            },
                        ]);

                        $iconClasses = \Illuminate\Support\Arr::toCssClasses([
                            'fi-in-text-icon h-5 w-5',
                            match ($color) {
                                'gray' => 'text-gray-400 dark:text-gray-500',
                                default => 'text-custom-500',
                            },
                        ]);

                        $iconStyles = \Illuminate\Support\Arr::toCssStyles([
                            \Filament\Support\get_color_css_variables($color, shades: [500]) => $color !== 'gray',
                        ]);
                    @endphp

                    <{{ $isListWithLineBreaks ? 'li' : 'div' }}
                        @if ($itemIsCopyable)
                            x-data="{}"
                            x-on:click="
                                window.navigator.clipboard.writeText(@js($copyableState))
                                $tooltip(@js($copyMessage), { timeout: @js($copyMessageDuration) })
                            "
                            class="cursor-pointer max-w-max"
                        @endif
                    >
                        @if ($isBadge)
                            <x-filament::badge
                                :color="$color"
                                :icon="$icon"
                                :icon-position="$iconPosition"
                            >
                                {{ $formattedState }}
                            </x-filament::badge>
                        @else
                            <div
                                @class([
                                    'fi-in-text-item inline-flex items-center gap-1',
                                    'transition duration-75 hover:underline focus:underline' => $url,
                                    match ($size) {
                                        'xs' => 'text-xs',
                                        'sm', null => 'text-sm leading-6',
                                        'base', 'md' => 'text-base',
                                        'lg' => 'text-lg',
                                        default => $size,
                                    },
                                    match ($color) {
                                        'gray' => 'text-gray-950 dark:text-white',
                                        default => 'text-custom-600 dark:text-custom-400',
                                    },
                                    match ($weight) {
                                        'thin' => 'font-thin',
                                        'extralight' => 'font-extralight',
                                        'light' => 'font-light',
                                        'medium' => 'font-medium',
                                        'semibold' => 'font-semibold',
                                        'bold' => 'font-bold',
                                        'extrabold' => 'font-extrabold',
                                        'black' => 'font-black',
                                        default => $weight,
                                    },
                                    match ($fontFamily) {
                                        'sans' => 'font-sans',
                                        'serif' => 'font-serif',
                                        'mono' => 'font-mono',
                                        default => $fontFamily,
                                    },
                                ])
                                @style([
                                    \Filament\Support\get_color_css_variables($color, shades: [400, 600]) => $color !== 'gray',
                                ])
                            >
                                @if ($icon && $iconPosition === 'before')
                                    <x-filament::icon
                                        :icon="$icon"
                                        :class="$iconClasses"
                                        :style="$iconStyles"
                                    />
                                @endif

                                <div
                                    @class([
                                        $proseClasses => $isProse,
                                    ])
                                >
                                    {{ $formattedState }}
                                </div>

                                @if ($icon && $iconPosition === 'after')
                                    <x-filament::icon
                                        :icon="$icon"
                                        :class="$iconClasses"
                                        :style="$iconStyles"
                                    />
                                @endif
                            </div>
                        @endif
                    </{{ $isListWithLineBreaks ? 'li' : 'div' }}>
                @endif
            @endforeach

            @if ($limitedArrayStateCount = count($limitedArrayState ?? []))
                <{{ $isListWithLineBreaks ? 'li' : 'div' }}
                    class="text-sm text-gray-950 dark:text-white"
                >
                    {{ trans_choice('filament-infolists::components.text_entry.more_list_items', $limitedArrayStateCount) }}
                </{{ $isListWithLineBreaks ? 'li' : 'div' }}>
            @endif
        </{{ $isListWithLineBreaks ? 'ul' : 'div' }}>
    </x-filament-infolists::affixes>
</x-dynamic-component>
