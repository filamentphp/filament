@props([
    'actions',
    'align' => 'left',
])

@if ($actions instanceof \Illuminate\Contracts\View\View)
    {{ $actions }}
@elseif (is_array($actions))
    @php
        $actions = array_filter(
            $actions,
            fn (\Filament\Pages\Actions\Action $action): bool => ! $action->isHidden(),
        );
    @endphp

    @if (count($actions))
        <div
            {{ $attributes->class([
                'flex flex-wrap items-center gap-4',
                match ($align) {
                    'center' => 'justify-center',
                    'right' => 'justify-end',
                    default => 'justify-start',
                },
                'filament-page-actions',
            ]) }}
        >
            @foreach ($actions as $action)
                {{ $action }}
            @endforeach
        </div>
    @endif
@endif
