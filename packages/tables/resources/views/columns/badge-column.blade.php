@php
    $state = $getFormattedState();

    $stateColor = match ($getStateColor()) {
        'danger' => \Illuminate\Support\Arr::toCssClasses(['text-danger-700 bg-danger-500/10', 'dark:text-danger-500' => config('tables.dark_mode')]),
        'primary' => \Illuminate\Support\Arr::toCssClasses(['text-primary-700 bg-primary-500/10', 'dark:text-primary-500' => config('tables.dark_mode')]),
        'success' => \Illuminate\Support\Arr::toCssClasses(['text-success-700 bg-success-500/10', 'dark:text-success-500' => config('tables.dark_mode')]),
        'warning' => \Illuminate\Support\Arr::toCssClasses(['text-warning-700 bg-warning-500/10', 'dark:text-warning-500' => config('tables.dark_mode')]),
        null, 'secondary' => \Illuminate\Support\Arr::toCssClasses(['text-gray-700 bg-gray-500/10', 'dark:text-gray-300 dark:bg-gray-500/20' => config('tables.dark_mode')]),
        default => $getStateColor(),
    };

    $stateIcon = $getStateIcon();
    $iconPosition = $getIconPosition();
    $iconClasses = 'h-4 w-4';

    $isCopyable = $isCopyable();
@endphp

<div
    {{
        $attributes
            ->merge($getExtraAttributes())
            ->class([
                'filament-tables-badge-column flex',
                'px-4 py-3' => ! $isInline(),
                match ($getAlignment()) {
                    'start' => 'justify-start',
                    'center' => 'justify-center',
                    'end' => 'justify-end',
                    'left' => 'justify-start rtl:flex-row-reverse',
                    'right' => 'justify-end rtl:flex-row-reverse',
                    default => null,
                },
            ])
    }}
>
    @if (filled($state))
        <div
            @class([
                'min-h-6 inline-flex items-center justify-center space-x-1 whitespace-nowrap rounded-xl px-2 py-0.5 text-sm font-medium tracking-tight rtl:space-x-reverse',
                $stateColor => $stateColor,
            ])
        >
            @if ($stateIcon && $iconPosition === 'before')
                <x-dynamic-component
                    :component="$stateIcon"
                    :class="$iconClasses"
                />
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

            @if ($stateIcon && $iconPosition === 'after')
                <x-dynamic-component
                    :component="$stateIcon"
                    :class="$iconClasses"
                />
            @endif
        </div>
    @endif
</div>
