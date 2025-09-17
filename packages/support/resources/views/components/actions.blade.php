@php
    use Filament\Support\Enums\Alignment;
@endphp

@props([
    'actions' => [],
    'alignment' => Alignment::Start,
    'fullWidth' => false,
])

@php
    if (is_array($actions)) {
        $actions = array_filter(
            $actions,
            fn ($action): bool => $action->isVisible(),
        );
    }

    if (! $alignment instanceof Alignment) {
        $alignment = filled($alignment) ? (Alignment::tryFrom($alignment) ?? $alignment) : null;
    }

    $hasActions = false;

    $hasSlot = ! \Filament\Support\is_slot_empty($slot);
    $actionsAreHtmlable = $actions instanceof \Illuminate\Contracts\Support\Htmlable;

    if ($hasSlot) {
        $hasActions = true;
    } elseif ($actionsAreHtmlable) {
        $hasActions = ! \Filament\Support\is_slot_empty($actions);
    } else {
        $hasActions = filled($actions);
    }
@endphp

@if ($hasActions)
    <div
        {{
            $attributes->class([
                'fi-ac',
                'fi-width-full' => $fullWidth,
                ($alignment instanceof Alignment) ? "fi-align-{$alignment->value}" : (is_string($alignment) ? $alignment : null) => ! $fullWidth,
            ])
        }}
    >
        @if ($hasSlot)
            {{ $slot }}
        @elseif ($actionsAreHtmlable)
            {{ $actions }}
        @else
            @foreach ($actions as $action)
                {{ $action }}
            @endforeach
        @endif
    </div>
@endif
