@props([
    'actions' => false,
    'actionsPosition' => null,
    'columns',
    'extraHeadingColumn' => false,
    'groupColumn' => null,
    'groupsOnly' => false,
    'heading',
    'placeholderColumns' => true,
    'query',
    'selectionEnabled' => false,
    'selectedState',
    'recordCheckboxPosition' => null,
])

@php
    use Filament\Support\Enums\Alignment;
    use Filament\Tables\Columns\Column;
    use Filament\Tables\Enums\ActionsPosition;
    use Filament\Tables\Enums\RecordCheckboxPosition;

    if ($groupsOnly && $groupColumn) {
        $columns = collect($columns)
            ->reject(fn (Column $column): bool => $column->getName() === $groupColumn)
            ->all();
    }
@endphp

<x-filament-tables::row
    :attributes="
        \Filament\Support\prepare_inherited_attributes($attributes)
            ->class(['fi-ta-summary-row'])
    "
>
    @if ($placeholderColumns && $actions && in_array($actionsPosition, [ActionsPosition::BeforeCells, ActionsPosition::BeforeColumns]))
        <td></td>
    @endif

    @if ($placeholderColumns && $selectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::BeforeCells)
        <td></td>
    @endif

    @if ($extraHeadingColumn || $groupsOnly)
        <x-filament-tables::cell
            class="text-sm font-medium text-gray-950 dark:text-white"
        >
            <span class="px-3 py-4">
                {{ $heading }}
            </span>
        </x-filament-tables::cell>
    @else
        @php
            $headingColumnSpan = 1;

            foreach ($columns as $index => $column) {
                if ($index === array_key_first($columns)) {
                    continue;
                }

                if ($column->hasSummary()) {
                    break;
                }

                $headingColumnSpan++;
            }
        @endphp
    @endif

    @foreach ($columns as $column)
        @if (($loop->first || $extraHeadingColumn || $groupsOnly || ($loop->iteration > $headingColumnSpan)) && ($placeholderColumns || $column->hasSummary()))
            @php
                $alignment = $column->getAlignment() ?? Alignment::Start;

                if (! $alignment instanceof Alignment) {
                    $alignment = filled($alignment) ? (Alignment::tryFrom($alignment) ?? $alignment) : null;
                }
            @endphp

            <x-filament-tables::cell
                :colspan="($loop->first && (! $extraHeadingColumn) && (! $groupsOnly) && ($headingColumnSpan > 1)) ? $headingColumnSpan : null"
                @class([
                    match ($alignment) {
                        Alignment::Start => 'text-start',
                        Alignment::Center => 'text-center',
                        Alignment::End => 'text-end',
                        Alignment::Left => 'text-left',
                        Alignment::Right => 'text-right',
                        Alignment::Justify, Alignment::Between => 'text-justify',
                        default => $alignment,
                    },
                ])
            >
                @if ($loop->first && (! $extraHeadingColumn) && (! $groupsOnly))
                    <span
                        class="flex px-3 py-4 text-sm font-medium text-gray-950 dark:text-white"
                    >
                        {{ $heading }}
                    </span>
                @elseif ((! $placeholderColumns) || $column->hasSummary())
                    @foreach ($column->getSummarizers() as $summarizer)
                        {{ $summarizer->query($query)->selectedState($selectedState) }}
                    @endforeach
                @endif
            </x-filament-tables::cell>
        @endif
    @endforeach

    @if ($placeholderColumns && $actions && in_array($actionsPosition, [ActionsPosition::AfterColumns, ActionsPosition::AfterCells]))
        <td></td>
    @endif

    @if ($placeholderColumns && $selectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::AfterCells)
        <td></td>
    @endif
</x-filament-tables::row>
