<?php

namespace Livewire\Testing {

    use Illuminate\Support\Collection;

    class TestableLivewire {
        public function mountTableAction(string $name, $record = null): static {}

        public function setTableActionData(array $data): static {}

        public function assertTableActionDataSet(array $data): static {}

        public function callTableAction(string $name, $record = null, array $data = [], array $arguments = []): static {}

        public function callTableColumnAction(string $name, $record = null): static {}

        public function callMountedTableAction(array $arguments = []): static {}

        public function assertTableActionExists(string $name): static {}

        public function assertTableActionDoesNotExist(string $name): static {}

        public function assertTableActionsExistInOrder(array $names): static {}

        public function assertTableHeaderActionsExistInOrder(array $names): static {}

        public function assertTableEmptyStateActionsExistInOrder(array $names): static {}

        public function assertTableActionVisible(string $name, $record = null): static {}

        public function assertTableActionHidden(string $name, $record = null): static {}

        public function assertTableActionEnabled(string $name, $record = null): static {}

        public function assertTableActionDisabled(string $name, $record = null): static {}

        public function assertTableActionHalted(string $name): static {}

        public function assertHasTableActionErrors(array $keys = []): static {}

        public function assertHasNoTableActionErrors(array $keys = []): static {}

        public function mountTableBulkAction(string $name, array | Collection $records): static {}

        public function setTableBulkActionData(array $data): static {}

        public function assertTableBulkActionDataSet(array $data): static {}

        public function callTableBulkAction(string $name, array | Collection $records, array $data = [], array $arguments = []): static {}

        public function callMountedTableBulkAction(array $arguments = []): static {}

        public function assertTableBulkActionExists(string $name): static {}

        public function assertTableBulkActionDoesNotExist(string $name): static {}

        public function assertTableBulkActionsExistInOrder(array $names): static {}

        public function assertTableBulkActionVisible(string $name): static {}

        public function assertTableBulkActionHidden(string $name): static {}

        public function assertTableBulkActionEnabled(string $name): static {}

        public function assertTableBulkActionDisabled(string $name): static {}

        public function assertTableActionHasIcon(string $name, string $icon): static {}

        public function assertTableActionDoesNotHaveIcon(string $name, string $icon): static {}

        public function assertTableActionHasLabel(string $name, string $label): static {}

        public function assertTableActionDoesNotHaveLabel(string $name, string $label): static {}

        public function assertTableActionHasColor(string $name, string $color): static {}

        public function assertTableActionDoesNotHaveColor(string $name, string $color): static {}

        public function assertTableBulkActionHasIcon(string $name, string $icon): static {}

        public function assertTableBulkActionDoesNotHaveIcon(string $name, string $icon): static {}

        public function assertTableBulkActionHasLabel(string $name, string $label): static {}

        public function assertTableBulkActionDoesNotHaveLabel(string $name, string $label): static {}

        public function assertTableBulkActionHasColor(string $name, string $color): static {}

        public function assertTableBulkActionDoesNotHaveColor(string $name, string $color): static {}

        public function assertTableActionHasUrl(string $name, string $url): static {}

        public function assertTableActionDoesNotHaveUrl(string $name, string $url): static {}

        public function assertTableActionShouldOpenUrlInNewTab(string $name): static {}

        public function assertTableActionShouldNotOpenUrlInNewTab(string $name): static {}

        public function assertTableBulkActionHalted(string $name): static {}

        public function assertHasTableBulkActionErrors(array $keys = []): static {}

        public function assertHasNoTableBulkActionErrors(array $keys = []): static {}

        public function assertCanRenderTableColumn(string $name): static {}

        public function assertCanNotRenderTableColumn(string $name): static {}

        public function assertTableColumnExists(string $name): static {}

        public function assertTableColumnVisible(string $name): static {}

        public function assertTableColumnHidden(string $name): static {}

        public function assertTableColumnStateSet(string $name, $value, $record): static {}

        public function assertTableColumnStateNotSet(string $name, $value, $record): static {}

        public function assertTableColumnFormattedStateSet(string $name, $value, $record): static {}

        public function assertTableColumnFormattedStateNotSet(string $name, $value, $record): static {}

        public function assertTableColumnHasExtraAttributes(string $name, $value, $record): static {}

        public function assertTableColumnDoesNotHaveExtraAttributes(string $name, $value, $record): static {}

        public function assertTableColumnHasDescription(string $name, $description, $record, $position = 'below'): static {}

        public function assertTableColumnDoesNotHaveDescription(string $name, $description, $record, $position = 'below'): static {}

        public function assertSelectColumnHasOptions(string $name, array $options, $record): static {}

        public function assertSelectColumnDoesNotHaveOptions(string $name, array $options, $record): static {}

        public function sortTable(?string $name = null, ?string $direction = null): static {}

        public function searchTable(?string $search = null): static {}

        public function searchTableColumns(array $searches): static {}

        public function filterTable(string $name, $data = null): static {}

        public function resetTableFilters(): static {}

        public function removeTableFilter(string $filter, ?string $field = null): static {}

        public function removeTableFilters(): static {}

        public function assertCanSeeTableRecords(array | Collection $records, bool $inOrder = false): static {}

        public function assertCanNotSeeTableRecords(array | Collection $records): static {}

        public function assertCountTableRecords(int $count): static {}

        public function loadTable(): static {}
    }

}
