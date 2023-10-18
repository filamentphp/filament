@php
    use Filament\Support\Enums\FontFamily;
    use Filament\Support\Enums\FontWeight;
    use Filament\Support\Enums\IconPosition;
    use Filament\Tables\Columns\TextColumn\TextColumnSize;

    $canWrap = $canWrap();
    $descriptionAbove = $getDescriptionAbove();
    $descriptionBelow = $getDescriptionBelow();
    $iconPosition = $getIconPosition();
    $isBadge = $isBadge();
    $isBulleted = $isBulleted();
    $isListWithLineBreaks = $isListWithLineBreaks();
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
                'fi-ta-text grid gap-y-1',
                'px-3 py-4' => ! $isInline(),
            ])
    }}
>
    @if (count($arrayState))
        @if (filled($descriptionAbove))
            <p
                @class([
                    'text-sm text-gray-500 dark:text-gray-400',
                    'whitespace-normal' => $canWrap,
                ])
            >
                {{ $descriptionAbove }}
            </p>
        @endif

        <{{ $isListWithLineBreaks ? 'ul' : 'div' }}
            @class([
                'list-inside list-disc' => $isBulleted,
                'flex flex-wrap items-center gap-1.5' => $isBadge,
                'whitespace-normal' => $canWrap,
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

                        $iconClasses = \Illuminate\Support\Arr::toCssClasses([
                            'fi-ta-text-item-icon h-5 w-5',
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
                            x-on:click="
                                window.navigator.clipboard.writeText(@js($copyableState))
                                $tooltip(@js($copyMessage), { timeout: @js($copyMessageDuration) })
                            "
                        @endif
                        @class([
                            'flex' => ! $isBulleted,
                            'max-w-max' => ! $isBadge,
                            'w-max' => $isBadge,
                            'cursor-pointer' => $itemIsCopyable,
                        ])
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
                                    'fi-ta-text-item inline-flex items-center gap-1.5',
                                    'transition duration-75 hover:underline focus-visible:underline' => $url,
                                    match ($size) {
                                        TextColumnSize::ExtraSmall, 'xs' => 'text-xs',
                                        TextColumnSize::Small, 'sm', null => 'text-sm',
                                        TextColumnSize::Medium, 'base', 'md' => 'text-base',
                                        TextColumnSize::Large, 'lg' => 'text-lg',
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

                                <div>
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
                    {{ trans_choice('filament-tables::table.columns.text.more_list_items', $limitedArrayStateCount) }}
                </{{ $isListWithLineBreaks ? 'li' : 'div' }}>
            @endif
        </{{ $isListWithLineBreaks ? 'ul' : 'div' }}>

        @if (filled($descriptionBelow))
            <p
                @class([
                    'text-sm text-gray-500 dark:text-gray-400',
                    'whitespace-normal' => $canWrap,
                ])
            >
                {{ $descriptionBelow }}
            </p>
        @endif
    @elseif (($placeholder = $getPlaceholder()) !== null)
        <x-filament-tables::columns.placeholder>
            {{ $placeholder }}
        </x-filament-tables::columns.placeholder>
    @endif
</div>
