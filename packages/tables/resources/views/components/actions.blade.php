@php
    use Filament\Support\Enums\Alignment;
@endphp

@props([
    'actions',
    'alignment' => Alignment::End,
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

    if (! $alignment instanceof Alignment) {
        $alignment = filled($alignment) ? (Alignment::tryFrom($alignment) ?? $alignment) : null;
    }
@endphp

<div
    {{
        $attributes->class([
            'fi-ta-actions flex shrink-0 items-center gap-3',
            'flex-wrap' => $wrap,
            'sm:flex-nowrap' => $wrap === '-sm',
            match ($alignment) {
                Alignment::Center => 'justify-center',
                Alignment::Start, Alignment::Left => 'justify-start',
                Alignment::End, Alignment::Right => 'justify-end',
                'start md:end' => 'justify-start md:justify-end',
                default => $alignment,
            },
        ])
    }}
>
    @foreach ($actions as $action)
        {{ $action }}
    @endforeach
</div>
