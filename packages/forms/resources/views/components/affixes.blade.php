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
    $baseAffixClasses = 'flex items-center self-stretch whitespace-nowrap border border-gray-300 px-3 text-gray-500 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-400';

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
