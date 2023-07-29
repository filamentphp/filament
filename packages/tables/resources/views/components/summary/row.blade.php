@props([
    'actions' => false,
    'actionsPosition' => null,
    'columns',
    'extraHeadingColumn' => false,
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
    use Filament\Tables\Enums\ActionsPosition;
    use Filament\Tables\Enums\RecordCheckboxPosition;
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
            {{ $heading }}
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
            <x-filament-tables::cell
                :colspan="($loop->first && (! $extraHeadingColumn) && (! $groupsOnly) && ($headingColumnSpan > 1)) ? $headingColumnSpan : null"
                @class([
                    match ($column->getAlignment()) {
                        Alignment::Start, 'start' => 'text-start',
                        Alignment::Center, 'center' => 'text-center',
                        Alignment::End, 'end' => 'text-end',
                        Alignment::Left, 'left' => 'text-left',
                        Alignment::Right, 'right' => 'text-right',
                        Alignment::Justify, 'justify' => 'text-justify',
                        default => null,
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
