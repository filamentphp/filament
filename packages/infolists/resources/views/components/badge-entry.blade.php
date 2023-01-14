<x-dynamic-component
    :component="$getEntryWrapperView()"
    :entry="$entry"
>
    @php
        $state = $formatState($getState());

        $color = $getColor();
        $colorClasses = match ($color) {
            'danger' => 'text-danger-700 bg-danger-500/10 dark:text-danger-500',
            'gray', null => 'text-gray-700 bg-gray-500/10 dark:text-gray-300 dark:bg-gray-500/20',
            'primary' => 'text-primary-700 bg-primary-500/10 dark:text-primary-500',
            'secondary' => 'text-secondary-700 bg-secondary-500/10 dark:text-secondary-500',
            'success' => 'text-success-700 bg-success-500/10 dark:text-success-500',
            'warning' => 'text-warning-700 bg-warning-500/10 dark:text-warning-500',
            default => $color,
        };

        $icon = $getIcon();
        $iconPosition = $getIconPosition();
        $iconSize = 'h-4 w-4';
    @endphp

    <div {{ $attributes->merge($getExtraAttributes(), escape: false)->class([
        'filament-infolists-badge-entry flex',
        match ($getAlignment()) {
            'start' => 'justify-start',
            'center' => 'justify-center',
            'end' => 'justify-end',
            'left' => 'justify-start rtl:flex-row-reverse',
            'right' => 'justify-end rtl:flex-row-reverse',
            default => null,
        },
    ]) }}>
        @if (filled($state))
            <div @class([
                'inline-flex items-center justify-center space-x-1 rtl:space-x-reverse min-h-6 px-2 py-0.5 text-xs font-medium rounded-xl whitespace-nowrap',
                $colorClasses,
            ])>
                @if ($icon && $iconPosition === 'before')
                    <x-filament::icon
                        :name="$icon"
                        alias="filament-infolists::components.badge-entry.prefix"
                        :size="$iconSize"
                    />
                @endif

                <span>
                    {{ $state }}
                </span>

                @if ($icon && $iconPosition === 'after')
                    <x-filament::icon
                        :name="$icon"
                        alias="filament-infolists::components.badge-entry.suffix"
                        :size="$iconSize"
                    />
                @endif
            </div>
        @endif
    </div>
</x-dynamic-component>
