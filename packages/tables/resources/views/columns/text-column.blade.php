@php
    $isListWithLineBreaks = $isListWithLineBreaks();

    $isBadge = $isBadge();

    $arrayState = $getState();
    if (is_array($arrayState)) {
        if ($listLimit = $getListLimit()) {
            $limitedArrayState = array_slice($arrayState, $listLimit);
            $arrayState = array_slice($arrayState, 0, $listLimit);
        }

        if ((! $isListWithLineBreaks) && (! $isBadge)) {
            $arrayState = implode(', ', $arrayState);
        }
    }
    $arrayState = \Illuminate\Support\Arr::wrap($arrayState);

    $canWrap = $canWrap();

    $descriptionAbove = $getDescriptionAbove();
    $descriptionBelow = $getDescriptionBelow();

    $iconPosition = $getIconPosition();
    $iconSize = $isBadge ? 'h-3 w-3' : 'h-4 w-4';

    $isClickable = $getAction() || $getUrl();
@endphp

<div
    {{
        $attributes
            ->merge($getExtraAttributes(), escape: false)
            ->class([
                'filament-tables-text-column',
                'px-4 py-3' => ! $isInline(),
                'text-primary-600 transition hover:text-primary-500 hover:underline focus:text-primary-500 focus:underline' => $isClickable && (! $isBadge),
            ])
    }}
>
    @if (filled($descriptionAbove))
        <div class="text-sm text-gray-500 dark:text-gray-400">
            {{ $descriptionAbove instanceof \Illuminate\Support\HtmlString ? $descriptionAbove : str($descriptionAbove)->markdown()->sanitizeHtml()->toHtmlString() }}
        </div>
    @endif

    <{{ $isListWithLineBreaks ? 'ul' : 'div' }}
        @class([
            'list-inside list-disc' => $isBulleted(),
            'flex flex-wrap gap-1' => $isBadge,
        ])
    >
        @foreach ($arrayState as $state)
            @php
                $formattedState = $formatState($state);

                $color = $getColor($state) ?? 'gray';
                $icon = $getIcon($state);

                $itemIsCopyable = $isCopyable($state);
                $copyableState = $getCopyableState($state) ?? $state;
                $copyMessage = $getCopyMessage($state);
                $copyMessageDuration = $getCopyMessageDuration($state);
            @endphp

            @if (filled($formattedState))
                <{{ $isListWithLineBreaks ? 'li' : 'div' }}>
                    <div
                        @class([
                            'inline-flex items-center space-x-1 rtl:space-x-reverse',
                            'filament-tables-text-column-badge min-h-6 justify-center whitespace-nowrap rounded-xl px-2 py-0.5' => $isBadge,
                            'whitespace-normal' => $canWrap,
                            "filament-tables-text-column-badge-color-{$color}" => $isBadge && is_string($color),
                            match ($color) {
                                'gray' => 'bg-gray-500/10 text-gray-700 dark:bg-gray-500/20 dark:text-gray-300',
                                default => 'bg-custom-500/10 text-custom-700 dark:text-custom-500',
                            } => $isBadge,
                            match ($color) {
                                'gray' => null,
                                default => 'text-custom-600 dark:text-custom-400',
                            } => ! ($isBadge || $isClickable),
                            match ($size = ($isBadge ? 'xs' : $getSize($state))) {
                                'xs' => 'text-xs',
                                'sm', null => 'text-sm',
                                'base', 'md' => 'text-base',
                                'lg' => 'text-lg',
                                default => $size,
                            },
                            match ($weight = ($isBadge ? 'medium' : $getWeight($state))) {
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
                            match ($getFontFamily($state)) {
                                'sans' => 'font-sans',
                                'serif' => 'font-serif',
                                'mono' => 'font-mono',
                                default => null,
                            },
                        ])
                        @style([
                            \Filament\Support\get_color_css_variables(
                                $color,
                                shades: match (true) {
                                    $isBadge => [500, 700],
                                    ! ($isBadge || $isClickable) => [400, 600],
                                    default => [],
                                },
                            ) => $color !== 'gray',
                        ])
                    >
                        @if ($icon && $iconPosition === 'before')
                            <x-filament::icon
                                :name="$icon"
                                alias="tables::columns.text.prefix"
                                :size="$iconSize"
                            />
                        @endif

                        <span
                            @if ($itemIsCopyable)
                                x-on:click="
                                    window.navigator.clipboard.writeText(@js($copyableState))
                                    $tooltip(@js($copyMessage), { timeout: @js($copyMessageDuration) })
                                "
                            @endif
                            @class([
                                'filament-tables-text-column-content',
                                'cursor-pointer' => $itemIsCopyable,
                            ])
                        >
                            {{ $formattedState }}
                        </span>

                        @if ($icon && $iconPosition === 'after')
                            <x-filament::icon
                                :name="$icon"
                                alias="tables::columns.text.suffix"
                                :size="$iconSize"
                            />
                        @endif
                    </div>
                </{{ $isListWithLineBreaks ? 'li' : 'div' }}>
            @endif
        @endforeach

        @if ($limitedArrayStateCount = count($limitedArrayState ?? []))
            <{{ $isListWithLineBreaks ? 'li' : 'div' }}
                @class([
                    'text-sm' => ! $isBadge,
                    'text-xs' => $isBadge,
                ])
            >
                {{ trans_choice('filament-tables::table.columns.text.more_list_items', $limitedArrayStateCount) }}
            </{{ $isListWithLineBreaks ? 'li' : 'div' }}>
        @endif
    </{{ $isListWithLineBreaks ? 'ul' : 'div' }}>

    @if (filled($descriptionBelow))
        <div class="text-sm text-gray-500 dark:text-gray-400">
            {{ $descriptionBelow instanceof \Illuminate\Support\HtmlString ? $descriptionBelow : str($descriptionBelow)->markdown()->sanitizeHtml()->toHtmlString() }}
        </div>
    @endif
</div>
