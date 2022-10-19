@props([
    'prefix' => null,
    'prefixAction' => null,
    'prefixIcon' => null,
    'statePath' => null,
    'suffix' => null,
    'suffixAction' => null,
    'suffixIcon' => null,
])

@php
    $affixLabelClasses = [
        'filament-input-affix-label whitespace-nowrap group-focus-within:text-primary-500',
        'text-gray-400' => blank($statePath),
    ];
@endphp

<div {{ $attributes->class(['filament-input-affix-container flex items-center space-x-2 rtl:space-x-reverse group']) }}>
    @if ($prefixAction && (! $prefixAction->isHidden()))
        {{ $prefixAction }}
    @endif

    @if ($prefixIcon)
        @svg($prefixIcon, 'filament-input-affix-icon h-5 w-5')
    @endif

    @if ($prefix)
        <span
            @class($affixLabelClasses)
            @if (filled($statePath))
                x-bind:class="{
                    'text-gray-400': ! (@js($statePath) in $wire.__instance.serverMemo.errors),
                    'text-danger-400': (@js($statePath) in $wire.__instance.serverMemo.errors),
                }"
            @endif
        >
            {{ $prefix }}
        </span>
    @endif

    <div class="flex-1 min-w-0">
        {{ $slot }}
    </div>

    @if ($suffix)
        <span
            @class($affixLabelClasses)
            @if (filled($statePath))
                x-bind:class="{
                    'text-gray-400': ! (@js($statePath) in $wire.__instance.serverMemo.errors),
                    'text-danger-400': (@js($statePath) in $wire.__instance.serverMemo.errors),
                }"
            @endif
        >
            {{ $suffix }}
        </span>
    @endif

    @if ($suffixIcon)
        @svg($suffixIcon, 'filament-input-affix-icon h-5 w-5')
    @endif

    @if ($suffixAction && (! $suffixAction->isHidden()))
        {{ $suffixAction }}
    @endif
</div>
