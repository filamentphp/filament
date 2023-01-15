@php
    $state = $formatState($getState());

    $descriptionAbove = $getDescriptionAbove();
    $descriptionBelow = $getDescriptionBelow();

    $icon = $getIcon();
    $iconPosition = $getIconPosition();
    $iconSize = 'h-4 w-4';

    $isClickable = $getAction() || $getUrl();

    $isCopyable = $isCopyable();
@endphp

<div {{ $attributes
    ->merge($getExtraAttributes(), escape: false)
    ->class([
        'filament-tables-text-column',
        'px-4 py-3' => ! $isInline(),
        'text-primary-600 transition hover:underline hover:text-primary-500 focus:underline focus:text-primary-500' => $isClickable,
        match ($color = $getColor()) {
            'danger' => 'text-danger-600',
            'gray' => 'text-gray-600 dark:text-gray-400',
            'primary' => 'text-primary-600',
            'secondary' => 'text-secondary-600',
            'success' => 'text-success-600',
            'warning' => 'text-warning-600',
            default => $color,
        } => ! $isClickable,
        match ($size = $getSize()) {
            'sm', null => 'text-sm',
            'base', 'md' => 'text-base',
            'lg' => 'text-lg',
            default => $size,
        },
        match ($weight = $getWeight()) {
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
        match ($getFontFamily()) {
            'sans' => 'font-sans',
            'serif' => 'font-serif',
            'mono' => 'font-mono',
            default => null,
        },
        'whitespace-normal' => $canWrap(),
    ])
}}>
    @if (filled($descriptionAbove))
        <div class="text-sm text-gray-500">
            {{ $descriptionAbove instanceof \Illuminate\Support\HtmlString ? $descriptionAbove : str($descriptionAbove)->markdown()->sanitizeHtml()->toHtmlString() }}
        </div>
    @endif

    <div class="inline-flex items-center space-x-1 rtl:space-x-reverse">
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
                    $tooltip(@js($getCopyMessage()), { timeout: @js($getCopyMessageDuration()) })
                "
            @endif
            @class([
                'cursor-pointer' => $isCopyable,
            ])
        >
            {{ $state }}
        </span>

        @if ($icon && $iconPosition === 'after')
            <x-filament::icon
                :name="$icon"
                alias="filament-tables::columns.text.suffix"
                :size="$iconSize"
            />
        @endif
    </div>

    @if (filled($descriptionBelow))
        <div class="text-sm text-gray-500">
            {{ $descriptionBelow instanceof \Illuminate\Support\HtmlString ? $descriptionBelow : str($descriptionBelow)->markdown()->sanitizeHtml()->toHtmlString() }}
        </div>
    @endif
</div>
