@php
    use Filament\Infolists\Components\TextEntry\TextEntrySize;
    use Filament\Support\Enums\FontFamily;
    use Filament\Support\Enums\FontWeight;
    use Filament\Support\Enums\IconPosition;
@endphp

<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    @php
        $isBadge = $isBadge();
        $iconPosition = $getIconPosition();
        $isListWithLineBreaks = $isListWithLineBreaks();
        $isProse = $isProse();
        $isMarkdown = $isMarkdown();
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

    <div
        {{
            $attributes
                ->merge($getExtraAttributes(), escape: false)
                ->class([
                    'fi-in-text',
                ])
        }}
    >
        @if ($arrayState)
            <x-filament-infolists::affixes
                :prefix-actions="$getPrefixActions()"
                :suffix-actions="$getSuffixActions()"
            >
                <{{ $isListWithLineBreaks ? 'ul' : 'div' }}
                    @class([
                        'list-inside list-disc' => $isBulleted(),
                        'flex flex-wrap items-center gap-1.5' => $isBadge,
                    ])
                >
                    @foreach ($arrayState as $state)
                        @if (filled($formattedState = $formatState($state)))
                            @php
                                $color = $getColor($state);
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
                                        TextEntrySize::ExtraSmall, 'xs' => 'prose-xs',
                                        TextEntrySize::Small, 'sm', null => 'prose-sm',
                                        TextEntrySize::Medium, 'base', 'md' => 'prose-base',
                                        TextEntrySize::Large, 'lg' => 'prose-lg',
                                        default => $size,
                                    },
                                ]);

                                $iconClasses = \Illuminate\Support\Arr::toCssClasses([
                                    'fi-in-text-item-icon h-5 w-5 shrink-0',
                                    match ($color) {
                                        'gray', null => 'text-gray-400 dark:text-gray-500',
                                        default => 'text-custom-500',
                                    },
                                ]);

                                $iconStyles = \Illuminate\Support\Arr::toCssStyles([
                                    \Filament\Support\get_color_css_variables(
                                        $color,
                                        shades: [500],
                                    ) => $color !== 'gray',
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
                                            'fi-in-text-item inline-flex items-center gap-1.5',
                                            'transition duration-75 hover:underline focus-visible:underline' => $url,
                                            match ($size) {
                                                TextEntrySize::ExtraSmall, 'xs' => 'text-xs',
                                                TextEntrySize::Small, 'sm', null => 'text-sm leading-6',
                                                TextEntrySize::Medium, 'base', 'md' => 'text-base',
                                                TextEntrySize::Large, 'lg' => 'text-lg',
                                                default => $size,
                                            },
                                            match ($color) {
                                                null => 'text-gray-950 dark:text-white',
                                                'gray' => 'fi-color-gray text-gray-500 dark:text-gray-400',
                                                default => 'fi-color-custom text-custom-600 dark:text-custom-400',
                                            },
                                            match ($weight) {
                                                FontWeight::Thin, 'thin' => 'font-thin',
                                                FontWeight::ExtraLight, 'extralight' => 'font-extralight',
                                                FontWeight::Light, 'light' => 'font-light',
                                                FontWeight::Medium, 'medium' => 'font-medium',
                                                FontWeight::SemiBold, 'semibold' => 'font-semibold',
                                                FontWeight::Bold, 'bold' => 'font-bold',
                                                FontWeight::ExtraBold, 'extrabold' => 'font-extrabold',
                                                FontWeight::Black, 'black' => 'font-black',
                                                default => $weight,
                                            },
                                            match ($fontFamily) {
                                                FontFamily::Sans, 'sans' => 'font-sans',
                                                FontFamily::Serif, 'serif' => 'font-serif',
                                                FontFamily::Mono, 'mono' => 'font-mono',
                                                default => $fontFamily,
                                            },
                                        ])
                                        @style([
                                            \Filament\Support\get_color_css_variables(
                                                $color,
                                                shades: [400, 600],
                                            ) => ! in_array($color, [null, 'gray']),
                                        ])
                                    >
                                        @if ($icon && in_array($iconPosition, [IconPosition::Before, 'before']))
                                            <x-filament::icon
                                                :icon="$icon"
                                                :class="$iconClasses"
                                                :style="$iconStyles"
                                            />
                                        @endif

                                        <div
                                            @class([
                                                $proseClasses => $isProse || $isMarkdown,
                                            ])
                                        >
                                            {{ $formattedState }}
                                        </div>

                                        @if ($icon && in_array($iconPosition, [IconPosition::After, 'after']))
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
                            class="text-sm text-gray-500 dark:text-gray-400"
                        >
                            {{ trans_choice('filament-infolists::components.text_entry.more_list_items', $limitedArrayStateCount) }}
                        </{{ $isListWithLineBreaks ? 'li' : 'div' }}>
                    @endif
                </{{ $isListWithLineBreaks ? 'ul' : 'div' }}>
            </x-filament-infolists::affixes>
        @elseif (($placeholder = $getPlaceholder()) !== null)
            <x-filament-infolists::entries.placeholder>
                {{ $placeholder }}
            </x-filament-infolists::entries.placeholder>
        @endif
    </div>
</x-dynamic-component>
