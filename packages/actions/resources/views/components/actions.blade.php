@props([
    'actions',
    'alignment' => 'left',
    'fullWidth' => false,
])

@if ($actions instanceof \Illuminate\Contracts\View\View)
    {{ $actions }}
@elseif (is_array($actions))
    @php
        $actions = array_filter(
            $actions,
            fn ($action): bool => $action->isVisible(),
        );
    @endphp

    @if (count($actions))
        <div
            {{
                $attributes->class([
                    'filament-actions-actions',
                    'flex flex-wrap items-center gap-4' => ! $fullWidth,
                    match ($alignment) {
                        'center' => 'justify-center',
                        'right' => 'flex-row-reverse space-x-reverse',
                        default => 'justify-start',
                    } => ! $fullWidth,
                    'grid grid-cols-[repeat(auto-fit,minmax(0,1fr))] gap-2' => $fullWidth,
                ])
            }}
        >
            @foreach ($actions as $action)
                {{ $action }}
            @endforeach
        </div>
    @endif
@endif
