@php
    $state = $getFormattedState();

    $descriptionAbove = $getDescriptionAbove();
    $descriptionBelow = $getDescriptionBelow();

    $icon = $getIcon();
    $iconPosition = $getIconPosition();
    $iconClasses = 'h-4 w-4';

    $isCopyable = $isCopyable();
@endphp

<div
    {{
        $attributes
            ->merge($getExtraAttributes())
            ->class([
                'filament-tables-text-column',
                'px-4 py-3' => ! $isInline(),
                'text-primary-600 transition hover:text-primary-500 hover:underline focus:text-primary-500 focus:underline' => $getAction() || $getUrl(),
                match ($getColor()) {
                    'danger' => 'text-danger-600',
                    'primary' => 'text-primary-600',
                    'secondary' => 'text-gray-500',
                    'success' => 'text-success-600',
                    'warning' => 'text-warning-600',
                    default => null,
                } => ! ($getAction() || $getUrl()),
                match ($getColor()) {
                    'secondary' => 'dark:text-gray-400',
                    default => null,
                } => (! ($getAction() || $getUrl())) && config('tables.dark_mode'),
                match ($getSize()) {
                    'sm' => 'text-sm',
                    'lg' => 'text-lg',
                    default => null,
                },
                match ($getWeight()) {
                    'thin' => 'font-thin',
                    'extralight' => 'font-extralight',
                    'light' => 'font-light',
                    'medium' => 'font-medium',
                    'semibold' => 'font-semibold',
                    'bold' => 'font-bold',
                    'extrabold' => 'font-extrabold',
                    'black' => 'font-black',
                    default => null,
                },
                match ($getFontFamily()) {
                    'sans' => 'font-sans',
                    'serif' => 'font-serif',
                    'mono' => 'font-mono',
                    default => null,
                },
                'whitespace-normal' => $canWrap(),
            ])
    }}
>
    @if (filled($descriptionAbove))
        <div
            @class([
                'text-sm text-gray-500',
                'dark:text-gray-400' => config('tables.dark_mode'),
            ])
        >
            {{ $descriptionAbove instanceof \Illuminate\Support\HtmlString ? $descriptionAbove : \Illuminate\Support\Str::of($descriptionAbove)->markdown()->sanitizeHtml()->toHtmlString() }}
        </div>
    @endif

    <div class="inline-flex items-center space-x-1 rtl:space-x-reverse">
        @if ($icon && $iconPosition === 'before')
            <x-dynamic-component :component="$icon" :class="$iconClasses" />
        @endif

        <span
            @if ($isCopyable)
                x-on:click="
                    window.navigator.clipboard.writeText(@js($getCopyableState()))
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
            <x-dynamic-component :component="$icon" :class="$iconClasses" />
        @endif
    </div>

    @if (filled($descriptionBelow))
        <div
            @class([
                'text-sm text-gray-500',
                'dark:text-gray-400' => config('tables.dark_mode'),
            ])
        >
            {{ $descriptionBelow instanceof \Illuminate\Support\HtmlString ? $descriptionBelow : \Illuminate\Support\Str::of($descriptionBelow)->markdown()->sanitizeHtml()->toHtmlString() }}
        </div>
    @endif
</div>
