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

<div {{ $attributes->class(['filament-forms-affix-container flex rtl:space-x-reverse group']) }}>
    @if (count($prefixActions))
        <div class="self-stretch flex gap-1 items-center pe-2">
            @foreach ($prefixActions as $prefixAction)
                {{ $prefixAction }}
            @endforeach
        </div>
    @endif

    @if ($prefixIcon)
        <span
            @class([
                $baseAffixClasses,
                'rounded-s-lg -me-px',
            ])
            @if (filled($statePath))
                x-bind:class="{
                    'text-gray-400': ! (@js($statePath) in $wire.__instance.serverMemo.errors),
                    'text-danger-400': (@js($statePath) in $wire.__instance.serverMemo.errors),
                }"
            @endif
        >
            <x-filament::icon
                alias="forms::components.affixes.prefix"
                :name="$prefixIcon"
                size="h-5 w-5"
                class="filament-input-affix-icon"
            />
        </span>
    @endif

    @if (filled($prefix))
        <span
            @class([
                'filament-input-affix-label -me-px',
                $baseAffixClasses,
                'rounded-s-lg' => ! $prefixIcon
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

    @if (filled($suffix))
        <span
            @class([
                'filament-input-affix-label -ms-px',
                $baseAffixClasses,
                'rounded-e-lg' => ! $suffixIcon
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
            @class([
                $baseAffixClasses,
                'rounded-e-lg -ms-px',
            ])
            @if (filled($statePath))
                x-bind:class="{
                    'text-gray-400': ! (@js($statePath) in $wire.__instance.serverMemo.errors),
                    'text-danger-400': (@js($statePath) in $wire.__instance.serverMemo.errors),
                }"
            @endif
        >
            <x-filament::icon
                alias="forms::components.affixes.suffix"
                :name="$suffixIcon"
                size="h-5 w-5"
                class="filament-input-affix-icon"
            />
        </span>
    @endif

    @if (count($suffixActions))
        <div class="self-stretch flex gap-1 items-center ps-2">
            @foreach ($suffixActions as $suffixAction)
                {{ $suffixAction }}
            @endforeach
        </div>
    @endif
</div>
