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
    $actions = array_reduce(
        $actions,
        function (array $carry, $action) use ($record): array {
            if (! $action instanceof \Filament\Actions\ActionGroup) {
                $action = clone $action;
            }

            if (! $action instanceof \Filament\Actions\BulkAction) {
                $action->record($record);
            }

            if ($action->isHidden()) {
                return $carry;
            }

            $carry[] = $action;

            return $carry;
        },
        initial: [],
    );

    if (! $alignment instanceof Alignment) {
        $alignment = filled($alignment) ? (Alignment::tryFrom($alignment) ?? $alignment) : null;
    }
@endphp

@if ($actions)
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
                    Alignment::Between, Alignment::Justify => 'justify-between',
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
@endif
