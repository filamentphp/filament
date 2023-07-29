@props([
    'actions',
    'alignment' => null,
    'record' => null,
    'wrap' => false,
])

@php
    $actions = array_filter(
        $actions,
        function ($action) use ($record): bool {
            if (! $action instanceof \Filament\Tables\Actions\BulkAction) {
                $action->record($record);
            }

            return $action->isVisible();
        },
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
        <span
            @class([
                'inline-flex',
                '-mx-2' => $action->isIconButton(),
            ])
        >
            {{ $action }}
        </span>
    @endforeach
</div>
