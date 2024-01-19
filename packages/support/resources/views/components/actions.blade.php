@php
    use Filament\Support\Enums\Alignment;
    use Illuminate\Contracts\Support\Htmlable;
    use Illuminate\Contracts\View\View;
@endphp

@props([
    'actions',
    'alignment' => Alignment::Start,
    'fullWidth' => false,
])

@if (($actions instanceof View) || ($actions instanceof Htmlable))
    {{ $actions }}
@elseif (is_array($actions))
    @php
        $actions = array_filter(
            $actions,
            fn ($action): bool => $action->isVisible(),
        );

        if (! $alignment instanceof Alignment) {
            $alignment = filled($alignment) ? (Alignment::tryFrom($alignment) ?? $alignment) : null;
        }
    @endphp

    @if (count($actions))
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
            @foreach ($actions as $action)
                {{ $action }}
            @endforeach
        </div>
    @endif
@endif
