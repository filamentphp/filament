@php
    $state = $getFormattedState();

    $stateColor = match ($getStateColor()) {
        'primary' => \Illuminate\Support\Arr::toCssClasses([
            'bg-primary-600 text-white' => $getInverseColor(),
            'text-primary-600 bg-primary-500/10' => !$getInverseColor(),
            'dark:text-primary-500 dark:bg-primary-500/10' => !$getInverseColor() && config('tables.dark_mode'),
        ]),
        'success' => \Illuminate\Support\Arr::toCssClasses([
            'bg-success-600 text-white' => $getInverseColor(),
            'text-success-600 bg-success-500/10' => !$getInverseColor(),
            'dark:text-success-500 dark:bg-success-500/10' => !$getInverseColor() && config('tables.dark_mode'),
        ]),
        'warning' => \Illuminate\Support\Arr::toCssClasses([
            'bg-warning-600 text-white' => $getInverseColor(),
            'text-warning-600 bg-warning-500/10' => !$getInverseColor(),
            'dark:text-warning-500 dark:bg-warning-500/10' => !$getInverseColor() && config('tables.dark_mode'),
        ]),
        'danger' => \Illuminate\Support\Arr::toCssClasses([
            'bg-danger-600 text-white' => $getInverseColor(),
            'text-danger-600 bg-danger-500/10' => !$getInverseColor(),
            'dark:text-danger-500 dark:bg-danger-500/10' => !$getInverseColor() && config('tables.dark_mode'),
        ]),
        null => \Illuminate\Support\Arr::toCssClasses([
            'text-gray-600 bg-gray-500/10',
            'dark:text-gray-300 dark:bg-gray-500/20' => config('tables.dark_mode')
        ]),
        default => $getStateColor()
    };

    $iconColor = match ($getStateColor()) {
        'primary' => \Illuminate\Support\Arr::toCssClasses([
            'text-white bg-primary-600 ' => !$getInverseColor(),
            'text-primary-600 bg-white' => $getInverseColor(),
            'dark:text-gray-100' => !$getInverseColor() && config('tables.dark_mode'),
        ]),
        'success' => \Illuminate\Support\Arr::toCssClasses([
            'text-white bg-success-600' => !$getInverseColor(),
            'text-success-600 bg-white' => $getInverseColor(),
            'dark:text-gray-100' => !$getInverseColor() && config('tables.dark_mode'),
        ]),
        'warning' => \Illuminate\Support\Arr::toCssClasses([
            'text-white bg-warning-600 ' => !$getInverseColor(),
            'text-warning-600 bg-white' => $getInverseColor(),
            'dark:text-gray-100' => !$getInverseColor() && config('tables.dark_mode'),
        ]),
        'danger' => \Illuminate\Support\Arr::toCssClasses([
            'text-white bg-danger-600' => !$getInverseColor(),
            'text-danger-600 bg-white' => $getInverseColor(),
            'dark:text-gray-100' => !$getInverseColor() && config('tables.dark_mode'),
        ]),
        null => \Illuminate\Support\Arr::toCssClasses([
            'text-white bg-gray-600',
            'dark:text-gray-100 dark:bg-gray-800' => config('tables.dark_mode')
        ]),
        default => $getStateColor()
    };

    $inset = $getInset();
    $stateIcon = $getStateIcon();
    $iconPosition = $getIconPosition();
    $iconClasses = 'h-4 w-4';
@endphp

<div {{ $attributes->merge($getExtraAttributes())->class([
    'px-4 py-3 flex filament-tables-badge-column',
    match ($getAlignment()) {
        'left' => 'justify-start',
        'center' => 'justify-center',
        'right' => 'justify-end',
        default => null,
    },
]) }}>
    @if (filled($state))
        <div @class([
            'inline-flex justify-center items-center px-[10px] text-sm rounded-full font-medium tracking-tight',
            $stateColor => $stateColor
        ])>
            <div @class([
                'flex justify-between items-center min-h-6 min-w-6',
                'flex-row-reverse' => $stateIcon && $iconPosition === 'before'
            ])>

                <span>{{ $state }}</span>

                @if ($stateIcon)
                    <div @class([
                        'flex justify-center items-center whitespace-nowrap text-center rounded-full h-5 w-5 my-0.5',
                        '-translate-x-2 rtl:translate-x-2' => $iconPosition === 'before',
                        'translate-x-2 rtl:-translate-x-2' => $iconPosition === 'after',
                        $iconColor => $inset,
                    ])>
                        <x-dynamic-component :component="$stateIcon" :class="$iconClasses" />
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
