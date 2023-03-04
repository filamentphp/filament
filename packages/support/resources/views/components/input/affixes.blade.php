@props([
    'prefix' => null,
    'prefixActions' => [],
    'prefixIcon' => null,
    'statePath' => null,
    'suffix' => null,
    'suffixActions' => [],
    'suffixIcon' => null,
])

@php
    $baseAffixClasses = 'whitespace-nowrap group-focus-within:text-primary-500 shadow-sm px-2 border border-gray-300 self-stretch flex items-center dark:border-gray-600 dark:bg-gray-700';

    $prefixActions = array_filter(
        $prefixActions,
        fn (\Filament\Forms\Components\Actions\Action $prefixAction): bool => $prefixAction->isVisible(),
    );

    $suffixActions = array_filter(
        $suffixActions,
        fn (\Filament\Forms\Components\Actions\Action $suffixAction): bool => $suffixAction->isVisible(),
    );
@endphp

<div {{ $attributes->class(['filament-input-affix-container flex rtl:space-x-reverse group']) }}>
    @if (count($prefixActions))
        <div class="self-stretch flex gap-1 items-center pr-2">
            @foreach ($prefixActions as $prefixAction)
                {{ $prefixAction }}
            @endforeach
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

    @if (count($suffixActions))
        <div class="self-stretch flex gap-1 items-center pl-2">
            @foreach ($suffixActions as $suffixAction)
                {{ $suffixAction }}
            @endforeach
        </div>
    @endif
</div>
