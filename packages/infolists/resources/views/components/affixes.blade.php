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

<div
    {{ $attributes->class(['filament-infolists-affix-container group flex rtl:space-x-reverse']) }}
>
    @if (count($prefixActions))
        <div class="flex items-center gap-1 self-stretch pe-2">
            @foreach ($prefixActions as $prefixAction)
                {{ $prefixAction }}
            @endforeach
        </div>
    @endif

    <div class="min-w-0 flex-1">
        {{ $slot }}
    </div>

    @if (count($suffixActions))
        <div class="flex items-center gap-1 self-stretch ps-2">
            @foreach ($suffixActions as $suffixAction)
                {{ $suffixAction }}
            @endforeach
        </div>
    @endif
</div>
