@php
    $descriptionAbove = $getDescriptionAbove();
    $descriptionBelow = $getDescriptionBelow();

    $icon = $getIcon();
    $iconPosition = $getIconPosition();
    $iconSize = 'h-4 w-4';
@endphp

<div
    {{ $attributes->merge($getExtraAttributes())->class([
        'filament-tables-text-column',
        'px-4 py-3' => ! $isInline(),
        'text-primary-600 transition hover:underline hover:text-primary-500 focus:underline focus:text-primary-500' => $getAction() || $getUrl(),
        match ($color = $getColor()) {
            'danger' => 'text-danger-600',
            'primary' => 'text-primary-600',
            'secondary' => 'text-gray-500 dark:text-gray-400',
            'success' => 'text-success-600',
            'warning' => 'text-warning-600',
            default => $color,
        } => ! ($getAction() || $getUrl()),
        match ($size = $getSize()) {
            'sm' => 'text-sm',
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
        'whitespace-normal' => $canWrap(),
    ]) }}
>
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

        <span>
            {{ $getFormattedState() }}
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
