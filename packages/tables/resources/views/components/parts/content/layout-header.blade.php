@php
    use Filament\Tables\Columns\Column;
@endphp

@if (! $isReordering)
    @php
        $sortableColumns = $hasSortingSettingsInContentHeader ? array_filter(
            $columns,
            fn (Column $column): bool => $column->isSortable(),
        ) : [];
    @endphp

    @if (($isSelectionEnabled && ($maxSelectableRecords !== 1) && (! $isReordering) && (! $selectsGroupsOnly)) || count($sortableColumns))
        <div class="fi-ta-content-header">
            @if ($isSelectionEnabled && ($maxSelectableRecords !== 1) && (! $isReordering) && (! $selectsGroupsOnly))
                @include('filament-tables::components.parts.content.page-checkbox', ['isStacked' => false])
            @endif

            <?php if ($hasSortingSettingsInContentHeader) { ?>

            @include('filament-tables::components.parts.sorting-settings', ['table' => $table, 'part' => null])

            <?php } ?>
        </div>
    @endif
@endif
