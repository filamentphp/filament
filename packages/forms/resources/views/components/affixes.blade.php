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
    $baseAffixClasses = 'flex items-center self-stretch whitespace-nowrap border border-gray-300 px-2 shadow-sm group-focus-within:text-primary-500 dark:border-gray-600 dark:bg-gray-700';

    $prefixActions = array_filter(
        $prefixActions,
        fn (\Filament\Forms\Components\Actions\Action $prefixAction): bool => $prefixAction->isVisible(),
    );

    $suffixActions = array_filter(
        $suffixActions,
        fn (\Filament\Forms\Components\Actions\Action $suffixAction): bool => $suffixAction->isVisible(),
    );
@endphp

<div
    {{ $attributes->class(['filament-forms-affix-container group flex rtl:space-x-reverse']) }}
>
    @if (count($prefixActions))
        <div class="flex items-center gap-1 self-stretch pe-2">
            @foreach ($prefixActions as $prefixAction)
                {{ $prefixAction }}
            @endforeach
        </div>
    @endif

    @if ($prefixIcon)
        <span
            @class([
                $baseAffixClasses,
                '-me-px rounded-s-lg',
            ])
            @if (filled($statePath))
                x-bind:class="{
                    'text-gray-400': ! (@js($statePath) in $wire.__instance.serverMemo.errors),
                    'text-danger-400': @js($statePath) in $wire.__instance.serverMemo.errors,
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
                'filament-input-affix-label -me-px text-sm',
                $baseAffixClasses,
                'rounded-s-lg' => ! $prefixIcon,
            ])
            @if (filled($statePath))
                x-bind:class="{
                    'text-gray-400': ! (@js($statePath) in $wire.__instance.serverMemo.errors),
                    'text-danger-400': @js($statePath) in $wire.__instance.serverMemo.errors,
                }"
            @endif
        >
            {{ $prefix }}
        </span>
    @endif

    <div class="min-w-0 flex-1">
        {{ $slot }}
    </div>

    @if (filled($suffix))
        <span
            @class([
                'filament-input-affix-label -ms-px text-sm',
                $baseAffixClasses,
                'rounded-e-lg' => ! $suffixIcon,
            ])
            @if (filled($statePath))
                x-bind:class="{
                    'text-gray-400': ! (@js($statePath) in $wire.__instance.serverMemo.errors),
                    'text-danger-400': @js($statePath) in $wire.__instance.serverMemo.errors,
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
                '-ms-px rounded-e-lg',
            ])
            @if (filled($statePath))
                x-bind:class="{
                    'text-gray-400': ! (@js($statePath) in $wire.__instance.serverMemo.errors),
                    'text-danger-400': @js($statePath) in $wire.__instance.serverMemo.errors,
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
        <div class="flex items-center gap-1 self-stretch ps-2">
            @foreach ($suffixActions as $suffixAction)
                {{ $suffixAction }}
            @endforeach
        </div>
    @endif
</div>
