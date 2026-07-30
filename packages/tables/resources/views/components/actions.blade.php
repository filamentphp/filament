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
    /**
     * Reset every child action inside an ActionGroup's own bound record to the
     * current row's record, recursively, before the group's visibility is checked
     * below. Companion fix to resources/views/components/group.blade.php in the
     * filament/actions package - see that file for the full explanation of the
     * underlying bug.
     *
     * Short version: Table::getAction() sets a record directly on whichever specific
     * child action was just clicked, and that action keeps using that stale record
     * for the rest of the request. group.blade.php resets it before deciding what to
     * put INSIDE the dropdown, but that runs too late to affect whether the trigger
     * button is shown at all - that decision happens right here, via
     * $actionGroup->isVisible() (which checks each child's isHiddenInGroup(), which
     * reads each child's own possibly-stale record). Without this reset, a row can
     * end up with a visible trigger button whose dropdown is empty: the stale record
     * made the group look non-empty here, but the corrected record inside
     * group.blade.php correctly finds nothing to show.
     */
    $resetActionGroupChildRecords = function ($action) use (&$resetActionGroupChildRecords, $record): void {
        if (! ($action instanceof \Filament\Actions\ActionGroup)) {
            return;
        }

        foreach ($action->getActions() as $childAction) {
            if ($childAction instanceof \Filament\Actions\Contracts\HasRecord) {
                $childAction->record($record);
            }

            $resetActionGroupChildRecords($childAction);
        }
    };

    $actions = array_filter(
        $actions,
        function ($action) use ($record, $resetActionGroupChildRecords): bool {
            if (! $action instanceof \Filament\Tables\Actions\BulkAction) {
                $action->record($record);
                $resetActionGroupChildRecords($action);
            }

            return $action->isVisible();
        },
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
