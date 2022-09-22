@props([
    'actions',
    'alignment' => null,
    'record' => null,
])

<div {{ $attributes->class([
    'filament-tables-actions-container flex flex-wrap items-center gap-4',
    match ($alignment ?? config('tables.layout.action_alignment') ?? config('tables.layout.actions.cell.alignment')) {
        'center' => 'justify-center',
        'left' => 'justify-start',
        'left md:right' => 'justify-start md:justify-end',
        default => 'justify-end',
    },
]) }}>
    @foreach ($actions as $action)
        @if (! $action->record($record)->isHidden())
            {{ $action }}
        @endif
    @endforeach
</div>
