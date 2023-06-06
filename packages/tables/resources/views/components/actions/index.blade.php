@props([
    'actions',
    'alignment' => null,
    'record' => null,
    'wrap' => false,
])

<div
    {{
        $attributes->class([
            'filament-tables-actions-container flex items-center gap-4',
            'flex-wrap' => $wrap,
            'md:flex-nowrap' => $wrap === '-md',
            match ($alignment ?? config('tables.layout.action_alignment') ?? config('tables.layout.actions.cell.alignment')) {
                'center' => 'justify-center',
                'left' => 'justify-start',
                'left md:right' => 'justify-start md:justify-end',
                default => 'justify-end',
            },
        ])
    }}
>
    @foreach ($actions as $action)
        @if (! $action->record($record)->isHidden())
            {{ $action }}
        @endif
    @endforeach
</div>
