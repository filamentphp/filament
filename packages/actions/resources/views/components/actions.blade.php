@props([
    'actions',
    'alignment' => 'start',
    'fullWidth' => false,
])

@php
    use Filament\Support\Enums\Alignment;
@endphp

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
                    'fi-ac gap-3',
                    'flex flex-wrap items-center' => ! $fullWidth,
                    match ($alignment) {
                        Alignment::Center, 'center' => 'justify-center',
                        Alignment::End, Alignment::Right, 'end', 'right' => 'flex-row-reverse',
                        Alignment::Between, 'between' => 'justify-between',
                        default => 'justify-start',
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
