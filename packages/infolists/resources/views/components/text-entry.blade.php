<x-dynamic-component
    :component="$getEntryWrapperView()"
    :entry="$entry"
>
    @php
        $state = $formatState($getState());

        $icon = $getIcon();
        $iconPosition = $getIconPosition();
        $iconSize = 'h-4 w-4';

        $url = $getUrl();

        $isCopyable = $isCopyable();
    @endphp

    <div {{ $attributes
        ->merge($getExtraAttributes(), escape: false)
        ->class([
            'filament-infolists-text-entry',
            'text-primary-600 transition hover:underline hover:text-primary-500 focus:underline focus:text-primary-500' => $url,
            match ($color = $getColor()) {
                'danger' => 'text-danger-600',
                'gray' => 'text-gray-600 dark:text-gray-400',
                'primary' => 'text-primary-600',
                'secondary' => 'text-secondary-600',
                'success' => 'text-success-600',
                'warning' => 'text-warning-600',
                default => $color,
            } => ! $url,
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
        ])
    }}>
        <div class="inline-flex items-center space-x-1 rtl:space-x-reverse">
            @if ($icon && $iconPosition === 'before')
                <x-filament::icon
                    :name="$icon"
                    alias="filament-infolists::components.text-entry.prefix"
                    :size="$iconSize"
                />
            @endif

            <span
                @if ($isCopyable)
                    x-data="{}"
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
                    alias="filament-infolists::components.text-entry.suffix"
                    :size="$iconSize"
                />
            @endif
        </div>
    </div>
</x-dynamic-component>
