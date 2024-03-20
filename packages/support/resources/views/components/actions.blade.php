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
                'fi-ac gap-3',
                'flex flex-wrap items-center' => ! $fullWidth,
                match ($alignment) {
                    Alignment::Start, Alignment::Left => 'justify-start',
                    Alignment::Center => 'justify-center',
                    Alignment::End, Alignment::Right => 'flex-row-reverse',
                    Alignment::Between, Alignment::Justify => 'justify-between',
                    default => $alignment,
                } => ! $fullWidth,
                'grid grid-cols-[repeat(auto-fit,minmax(0,1fr))]' => $fullWidth,
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
