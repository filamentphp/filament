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
    $baseAffixClasses = 'whitespace-nowrap group-focus-within:text-primary-500 shadow-sm px-2 border border-gray-300 self-stretch flex items-center dark:border-gray-600 dark:bg-gray-700';
@endphp

<div {{ $attributes->class(['filament-input-affix-container flex rtl:space-x-reverse group']) }}>
    @if ($prefixAction?->isVisible())
        <div class="self-stretch flex items-center pr-2">
            {{ $prefixAction }}
        </div>
    @endif

    @if ($prefixIcon)
        <span
            @class(array_merge(
                [$baseAffixClasses],
                ['rounded-l-lg -mr-px'],
            ))
            @if (filled($statePath))
                x-bind:class="{
                'text-gray-400': ! (@js($statePath) in $wire.__instance.serverMemo.errors),
                'text-danger-400': (@js($statePath) in $wire.__instance.serverMemo.errors),
            }"
            @endif
        >
            <x-filament::icon
                alias="support::input.affixes.prefix"
                :name="$prefixIcon"
                size="h-5 w-5"
                class="filament-input-affix-icon"
            />
        </span>
    @endif

    @if ($prefix)
        <span
            @class([
                    'filament-input-affix-label -mr-px',
                    $baseAffixClasses,
                    'rounded-l-lg' => ! $prefixIcon
            ])
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
            @class([
                    'filament-input-affix-label -ml-px',
                    $baseAffixClasses,
                    'rounded-r-lg' => ! $suffixIcon
            ])
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
        <span
            @class(array_merge(
                [$baseAffixClasses],
                ['rounded-r-lg -ml-px'],
            ))
            @if (filled($statePath))
                x-bind:class="{
                'text-gray-400': ! (@js($statePath) in $wire.__instance.serverMemo.errors),
                'text-danger-400': (@js($statePath) in $wire.__instance.serverMemo.errors),
            }"
            @endif
        >
            <x-filament::icon
                alias="support::input.affixes.suffix"
                :name="$suffixIcon"
                size="h-5 w-5"
                class="filament-input-affix-icon"
            />
        </span>
    @endif

    @if ($suffixAction?->isVisible())
        <div class="self-stretch flex items-center pl-2">
            {{ $suffixAction }}
        </div>
    @endif
</div>
