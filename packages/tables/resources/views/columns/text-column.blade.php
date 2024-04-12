@php
    use Filament\Support\Enums\Alignment;
    use Filament\Support\Enums\FontFamily;
    use Filament\Support\Enums\FontWeight;
    use Filament\Support\Enums\IconPosition;
    use Filament\Tables\Columns\TextColumn\TextColumnSize;

    $alignment = $getAlignment();
    $canWrap = $canWrap();
    $descriptionAbove = $getDescriptionAbove();
    $descriptionBelow = $getDescriptionBelow();
    $iconPosition = $getIconPosition();
    $isBadge = $isBadge();
    $isBulleted = $isBulleted();
    $isListWithLineBreaks = $isListWithLineBreaks();
    $isLimitedListExpandable = $isLimitedListExpandable();
    $url = $getUrl();

    if (! $alignment instanceof Alignment) {
        $alignment = filled($alignment) ? (Alignment::tryFrom($alignment) ?? $alignment) : null;
    }

    $arrayState = $getState();

    if ($arrayState instanceof \Illuminate\Support\Collection) {
        $arrayState = $arrayState->all();
    }

    $listLimit = 1;

    if (is_array($arrayState)) {
        if ($listLimit = $getListLimit()) {
            $limitedArrayStateCount = (count($arrayState) > $listLimit) ? (count($arrayState) - $listLimit) : 0;

            if (! $isListWithLineBreaks) {
                $arrayState = array_slice($arrayState, 0, $listLimit);
            }
        }

        $listLimit ??= count($arrayState);

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
                'fi-ta-text grid w-full gap-y-1',
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
                'flex' => ! $isBulleted,
                'flex-col' => (! $isBulleted) && $isListWithLineBreaks,
                'list-inside list-disc' => $isBulleted,
                'gap-1.5' => $isBadge,
                'flex-wrap' => $isBadge && (! $isListWithLineBreaks),
                'whitespace-normal' => $canWrap,
                match ($alignment) {
                    Alignment::Start => 'text-start',
                    Alignment::Center => 'text-center',
                    Alignment::End => 'text-end',
                    Alignment::Left => 'text-left',
                    Alignment::Right => 'text-right',
                    Alignment::Justify, Alignment::Between => 'text-justify',
                    default => $alignment,
                },
                match ($alignment) {
                    Alignment::Start, Alignment::Left => 'justify-start',
                    Alignment::Center => 'justify-center',
                    Alignment::End, Alignment::Right => 'justify-end',
                    Alignment::Between, Alignment::Justify => 'justify-between',
                    default => null,
                } => $isBulleted || (! $isListWithLineBreaks),
                match ($alignment) {
                    Alignment::Start, Alignment::Left => 'items-start',
                    Alignment::Center => 'items-center',
                    Alignment::End, Alignment::Right => 'items-end',
                    Alignment::Between, Alignment::Justify => 'items-stretch',
                    default => null,
                } => $isListWithLineBreaks && (! $isBulleted),
            ])
            @if ($isListWithLineBreaks && $isLimitedListExpandable)
                x-data="{ isLimited: true }"
            @endif
        >
            @foreach ($arrayState as $state)
                @if (filled($formattedState = $formatState($state)) &&
                     (! ($isListWithLineBreaks && (! $isLimitedListExpandable) && ($loop->iteration > $listLimit))))
                    @php
                        $color = $getColor($state);
                        $copyableState = $getCopyableState($state) ?? $state;
                        $copyMessage = $getCopyMessage($state);
                        $copyMessageDuration = $getCopyMessageDuration($state);
                        $fontFamily = $getFontFamily($state);
                        $icon = $getIcon($state);
                        $iconColor = $getIconColor($state) ?? $color;
                        $itemIsCopyable = $isCopyable($state);
                        $lineClamp = $getLineClamp($state);
                        $size = $getSize($state);
                        $weight = $getWeight($state);

                        $iconClasses = \Illuminate\Support\Arr::toCssClasses([
                            'fi-ta-text-item-icon h-5 w-5',
                            match ($iconColor) {
                                'gray', null => 'text-gray-400 dark:text-gray-500',
                                default => 'text-custom-500',
                            },
                        ]);

                        $iconStyles = \Illuminate\Support\Arr::toCssStyles([
                            \Filament\Support\get_color_css_variables(
                                $iconColor,
                                shades: [500],
                                alias: 'tables::columns.text-column.item.icon',
                            ) => $iconColor !== 'gray',
                        ]);
                    @endphp

                    <{{ $isListWithLineBreaks ? 'li' : 'div' }}
                        @if ($itemIsCopyable)
                            x-on:click="
                                window.navigator.clipboard.writeText(@js($copyableState))
                                $tooltip(@js($copyMessage), {
                                    theme: $store.theme,
                                    timeout: @js($copyMessageDuration),
                                })
                            "
                        @endif
                        @if ($isListWithLineBreaks && ($loop->iteration > $listLimit))
                            x-cloak
                            x-show="! isLimited"
                            x-transition
                        @endif
                        @class([
                            'flex' => ! $isBulleted,
                            'max-w-max' => ! ($isBulleted || $isBadge),
                            'w-max' => $isBadge,
                            'cursor-pointer' => $itemIsCopyable,
                            match ($color) {
                                null => 'text-gray-950 dark:text-white',
                                'gray' => 'text-gray-500 dark:text-gray-400',
                                default => 'text-custom-600 dark:text-custom-400',
                            } => $isBulleted,
                        ])
                        @style([
                            \Filament\Support\get_color_css_variables(
                                $color,
                                shades: [400, 600],
                                alias: 'tables::columns.text-column.item.container',
                            ) => $isBulleted && (! in_array($color, [null, 'gray'])),
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
                                    'group/item' => $url,
                                    match ($color) {
                                        null, 'gray' => null,
                                        default => 'fi-color-custom',
                                    },
                                    is_string($color) ? "fi-color-{$color}" : null,
                                ])
                            >
                                @if ($icon && in_array($iconPosition, [IconPosition::Before, 'before']))
                                    <x-filament::icon
                                        :icon="$icon"
                                        :class="$iconClasses"
                                        :style="$iconStyles"
                                    />
                                @endif

                                <span
                                    @class([
                                        'fi-ta-text-item-label',
                                        'group-hover/item:underline group-focus-visible/item:underline' => $url,
                                        'line-clamp-[--line-clamp]' => $lineClamp,
                                        match ($size) {
                                            TextColumnSize::ExtraSmall, 'xs' => 'text-xs',
                                            TextColumnSize::Small, 'sm', null => 'text-sm leading-6',
                                            TextColumnSize::Medium, 'base', 'md' => 'text-base',
                                            TextColumnSize::Large, 'lg' => 'text-lg',
                                            default => $size,
                                        },
                                        match ($color) {
                                            null => 'text-gray-950 dark:text-white',
                                            'gray' => 'text-gray-500 dark:text-gray-400',
                                            default => 'text-custom-600 dark:text-custom-400',
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
                                            alias: 'tables::columns.text-column.item.label',
                                        ) => ! in_array($color, [null, 'gray']),
                                        "--line-clamp: {$lineClamp}" => $lineClamp,
                                    ])
                                >
                                    {{ $formattedState }}
                                </span>

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

            @if ($limitedArrayStateCount ?? 0)
                <{{ $isListWithLineBreaks ? 'li' : 'div' }}>
                    @if ($isLimitedListExpandable)
                        <x-filament::link
                            color="gray"
                            tag="button"
                            x-on:click.prevent="isLimited = false"
                            x-show="isLimited"
                        >
                            {{ trans_choice('filament-tables::table.columns.text.actions.expand_list', $limitedArrayStateCount) }}
                        </x-filament::link>

                        <x-filament::link
                            color="gray"
                            tag="button"
                            x-cloak
                            x-on:click.prevent="isLimited = true"
                            x-show="! isLimited"
                        >
                            {{ trans_choice('filament-tables::table.columns.text.actions.collapse_list', $limitedArrayStateCount) }}
                        </x-filament::link>
                    @else
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ trans_choice('filament-tables::table.columns.text.more_list_items', $limitedArrayStateCount) }}
                        </span>
                    @endif
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
