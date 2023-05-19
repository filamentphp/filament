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

    $isCopyable = $isCopyable();
    $copyMessage = $getCopyMessage();
    $copyMessageDuration = $getCopyMessageDuration();
@endphp

<div {{ $attributes
    ->merge($getExtraAttributes(), escape: false)
    ->class([
        'filament-tables-text-column',
        'px-4 py-3' => ! $isInline(),
        'text-primary-600 transition hover:underline hover:text-primary-500 focus:underline focus:text-primary-500' => $isClickable && (! $isBadge),
    ])
}}>
    @if (filled($descriptionAbove))
        <div class="text-sm text-gray-500 dark:text-gray-400">
            {{ $descriptionAbove instanceof \Illuminate\Support\HtmlString ? $descriptionAbove : str($descriptionAbove)->markdown()->sanitizeHtml()->toHtmlString() }}
        </div>
    @endif

    <{{ $isListWithLineBreaks ? 'ul' : 'div' }} @class([
        'list-disc list-inside' => $isBulleted(),
        'flex flex-wrap gap-1' => $isBadge,
    ])>
        @foreach ($arrayState as $state)
            @php
                $formattedState = $formatState($state);
                $icon = $getIcon($state);
            @endphp

            @if (filled($formattedState))
                <{{ $isListWithLineBreaks ? 'li' : 'div' }}>
                    <div @class([
                        'inline-flex items-center space-x-1 rtl:space-x-reverse',
                        'filament-tables-text-column-badge justify-center min-h-6 px-2 py-0.5 rounded-xl whitespace-nowrap' => $isBadge,
                        'whitespace-normal' => $canWrap,
                        ($isBadge ? match ($color = $getColor($state)) {
                            'danger' => 'filament-tables-text-column-badge-color-danger text-danger-700 bg-danger-500/10 dark:text-danger-500',
                            'gray', null => 'filament-tables-text-column-badge-color-gray text-gray-700 bg-gray-500/10 dark:text-gray-300 dark:bg-gray-500/20',
                            'info' => 'filament-tables-text-column-badge-color-info text-info-700 bg-info-500/10 dark:text-info-500',
                            'primary' => 'filament-tables-text-column-badge-color-primary text-primary-700 bg-primary-500/10 dark:text-primary-500',
                            'secondary' => 'filament-tables-text-column-badge-color-secondary text-secondary-700 bg-secondary-500/10 dark:text-secondary-500',
                            'success' => 'filament-tables-text-column-badge-color-success text-success-700 bg-success-500/10 dark:text-success-500',
                            'warning' => 'filament-tables-text-column-badge-color-warning text-warning-700 bg-warning-500/10 dark:text-warning-500',
                            default => $color,
                        } : null),
                        ((! ($isBadge || $isClickable)) ? match ($color = $getColor($state)) {
                            'danger' => 'text-danger-600',
                            'gray' => 'text-gray-600 dark:text-gray-400',
                            'info' => 'text-info-600',
                            'primary' => 'text-primary-600',
                            'secondary' => 'text-secondary-600',
                            'success' => 'text-success-600',
                            'warning' => 'text-warning-600',
                            default => $color,
                        } : null),
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
                    ])>
                        @if ($icon && $iconPosition === 'before')
                            <x-filament::icon
                                :name="$icon"
                                alias="filament-tables::columns.text.prefix"
                                :size="$iconSize"
                            />
                        @endif

                        <span
                            @if ($isCopyable)
                                x-on:click="
                                    window.navigator.clipboard.writeText(@js($state))
                                    $tooltip(@js($copyMessage), { timeout: @js($copyMessageDuration) })
                                "
                            @endif
                            @class([
                                'cursor-pointer' => $isCopyable,
                            ])
                        >
                            {{ $formattedState }}
                        </span>

                        @if ($icon && $iconPosition === 'after')
                            <x-filament::icon
                                :name="$icon"
                                alias="filament-tables::columns.text.suffix"
                                :size="$iconSize"
                            />
                        @endif
                    </div>
                </{{ $isListWithLineBreaks ? 'li' : 'div' }}>
            @endif
        @endforeach

        @if ($limitedArrayStateCount = count($limitedArrayState ?? []))
            <{{ $isListWithLineBreaks ? 'li' : 'div' }} @class([
                'text-sm' => ! $isBadge,
                'text-xs' => $isBadge,
            ])>
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
