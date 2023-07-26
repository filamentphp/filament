@props([
    'actions',
    'alignment' => null,
    'record' => null,
    'wrap' => false,
])

@php
    $actions = array_filter(
        $actions,
        fn ($action): bool => $action->isVisible(),
    );
@endphp

<div
    {{
        $attributes->class([
            'fi-ta-actions flex shrink-0 items-center gap-3',
            'flex-wrap' => $wrap,
            'sm:flex-nowrap' => $wrap === '-sm',
            match ($alignment) {
                'center' => 'justify-center',
                'start', 'left' => 'justify-start',
                'start sm:end' => 'justify-start sm:justify-end',
                default => 'justify-end',
            },
        ])
    }}
>
    @foreach ($actions as $action)
        @php
            if (! $action instanceof \Filament\Tables\Actions\BulkAction) {
                $action->record($record);
            }
        @endphp

        {{ $action }}
    @endforeach
</div>
