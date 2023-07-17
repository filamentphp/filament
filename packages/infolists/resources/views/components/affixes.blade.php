@props([
    'prefixActions' => [],
    'suffixActions' => [],
])

@php
    $prefixActions = array_filter(
        $prefixActions,
        fn (\Filament\Infolists\Components\Actions\Action $prefixAction): bool => $prefixAction->isVisible(),
    );

    $suffixActions = array_filter(
        $suffixActions,
        fn (\Filament\Infolists\Components\Actions\Action $suffixAction): bool => $suffixAction->isVisible(),
    );

    $affixActionsClasses = '-mx-1.5 flex items-center self-stretch';
@endphp

<div
    {{ $attributes->class(['fi-in-affixes flex']) }}
>
    @if (count($prefixActions))
        <div
            @class([
                $affixActionsClasses,
                'pe-2',
            ])
        >
            @foreach ($prefixActions as $prefixAction)
                {{ $prefixAction }}
            @endforeach
        </div>
    @endif

    <div class="min-w-0 flex-1">
        {{ $slot }}
    </div>

    @if (count($suffixActions))
        <div
            @class([
                $affixActionsClasses,
                'ps-2',
            ])
        >
            @foreach ($suffixActions as $suffixAction)
                {{ $suffixAction }}
            @endforeach
        </div>
    @endif
</div>
