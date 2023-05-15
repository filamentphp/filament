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
@endphp

<div {{ $attributes->class(['filament-infolists-affix-container flex rtl:space-x-reverse group']) }}>
    @if (count($prefixActions))
        <div class="self-stretch flex gap-1 items-center pe-2">
            @foreach ($prefixActions as $prefixAction)
                {{ $prefixAction }}
            @endforeach
        </div>
    @endif

    <div class="flex-1 min-w-0">
        {{ $slot }}
    </div>

    @if (count($suffixActions))
        <div class="self-stretch flex gap-1 items-center ps-2">
            @foreach ($suffixActions as $suffixAction)
                {{ $suffixAction }}
            @endforeach
        </div>
    @endif
</div>
