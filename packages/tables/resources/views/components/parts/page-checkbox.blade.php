@php
    use Filament\Tables\Table;
@endphp

@if ($table->hasContentLayout() && $table->canSelectRecords() && ($table->getMaxSelectableRecords() !== 1) && (! $table->isReordering()) && (! $table->selectsGroupsOnly()))
    @include('filament-tables::components.parts.content.page-checkbox', [
        'isSelectionDisabled' => $table->isSelectionDisabled(),
        'isStacked' => false,
        'loadingTargetsWireTarget' => implode(',', Table::LOADING_TARGETS),
        'maxSelectableRecords' => $table->getMaxSelectableRecords(),
    ])
@endif
