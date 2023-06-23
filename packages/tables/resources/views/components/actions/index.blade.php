@props([
    'actions',
    'alignment' => null,
    'record' => null,
    'wrap' => false,
])

<div
    {{
        $attributes->class([
            'filament-tables-actions-container flex items-center gap-3',
            'flex-wrap' => $wrap,
            'md:flex-nowrap' => $wrap === '-md',
            match ($alignment) {
                'center' => 'justify-center',
                'start', 'left' => 'justify-start',
                'start md:end', 'left md:right' => 'justify-start md:justify-end',
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

        @if ($action->isVisible())
            {{ $action }}
        @endif
    @endforeach
</div>
