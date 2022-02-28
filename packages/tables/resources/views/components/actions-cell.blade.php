@props([
    'actions',
    'record',
])

<td {{ $attributes->class(['px-4 py-3 whitespace-nowrap filament-tables-actions-cell']) }}>
    <div {{ $attributes->class([
        'flex items-center gap-4',
        match (config('tables.layout.action_alignment')) {
            'center' => 'justify-center',
            'left' => 'justify-start',
            default => 'justify-end',
        },
    ]) }}>
        @foreach ($actions as $action)
            @if (! $action->record($record)->isHidden())
                {{ $action }}
            @endif
        @endforeach
    </div>
</td>
