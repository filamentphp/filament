<?php

namespace Livewire\Features\SupportTesting {

    use Closure;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Collection;

    class Testable {
        public function mountTableAction(string | array $name, $record = null): static {}

        public function unmountTableAction(): static {}

        public function setTableActionData(array $data): static {}

        public function assertTableActionDataSet(array $data): static {}

        public function callTableAction(string | array $name, $record = null, array $data = [], array $arguments = []): static {}

        public function callTableColumnAction(string $name, $record = null): static {}

        public function callMountedTableAction(array $arguments = []): static {}

        public function assertTableActionExists(string | array $name, ?Closure $checkActionUsing = null, $record = null): static {}

        public function assertTableActionDoesNotExist(string | array $name, ?Closure $checkActionUsing = null, $record = null): static {}

        public function assertTableActionsExistInOrder(array $names): static {}

        public function assertTableHeaderActionsExistInOrder(array $names): static {}

        public function assertTableEmptyStateActionsExistInOrder(array $names): static {}

        public function assertTableActionVisible(string | array $name, $record = null): static {}

        public function assertTableActionHidden(string | array $name, $record = null): static {}

        public function assertTableActionEnabled(string | array $name, $record = null): static {}

        public function assertTableActionDisabled(string | array $name, $record = null): static {}

        public function assertTableActionMounted(string | array $name): static {}

        public function assertTableActionNotMounted(string | array $name): static {}

        public function assertTableActionHalted(string | array $name): static {}

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

        public function assertTableActionHasIcon(string | array $name, string $icon): static {}

        public function assertTableActionDoesNotHaveIcon(string | array $name, string $icon): static {}

        public function assertTableActionHasLabel(string | array $name, string $label): static {}

        public function assertTableActionDoesNotHaveLabel(string | array $name, string $label): static {}

        public function assertTableActionHasColor(string | array $name, string | array $color): static {}

        public function assertTableActionDoesNotHaveColor(string | array $name, string | array $color): static {}

        public function assertTableBulkActionHasIcon(string $name, string $icon): static {}

        public function assertTableBulkActionDoesNotHaveIcon(string $name, string $icon): static {}

        public function assertTableBulkActionHasLabel(string $name, string $label): static {}

        public function assertTableBulkActionDoesNotHaveLabel(string $name, string $label): static {}

        public function assertTableBulkActionHasColor(string $name, string | array $color): static {}

        public function assertTableBulkActionDoesNotHaveColor(string $name, string | array $color): static {}

        public function assertTableActionHasUrl(string | array $name, string $url): static {}

        public function assertTableActionDoesNotHaveUrl(string | array $name, string $url): static {}

        public function assertTableActionShouldOpenUrlInNewTab(string | array $name): static {}

        public function assertTableActionShouldNotOpenUrlInNewTab(string | array $name): static {}

        public function assertTableBulkActionMounted(string $name): static {}

        public function assertTableBulkActionNotMounted(string $name): static {}

        public function assertTableBulkActionHalted(string $name): static {}

        public function assertHasTableBulkActionErrors(array $keys = []): static {}

        public function assertHasNoTableBulkActionErrors(array $keys = []): static {}

        public function assertCanRenderTableColumn(string $name): static {}

        public function assertCanNotRenderTableColumn(string $name): static {}

        public function assertTableColumnExists(string $name, ?Closure $checkColumnUsing = null, $record = null): static {}

        public function assertTableColumnDoesNotExist(string $name, ?Closure $checkColumnUsing = null, $record = null): static {}

        public function assertTableColumnVisible(string $name): static {}

        public function assertTableColumnHidden(string $name): static {}

        public function assertTableColumnStateSet(string $name, $value, $record): static {}

        public function assertTableColumnStateNotSet(string $name, $value, $record): static {}

        public function assertTableColumnSummarizerExists(string $columnName, string $summarizerId): static {}

        public function assertTableColumnSummarySet(string $columnName, string $summarizerId, $state, bool $isCurrentPaginationPageOnly = false): static {}

        public function assertTableColumnSummaryNotSet(string $columnName, string $summarizerId, $state, bool $isCurrentPaginationPageOnly = false): static {}

        public function assertTableColumnFormattedStateSet(string $name, $value, $record): static {}

        public function assertTableColumnFormattedStateNotSet(string $name, $value, $record): static {}

        public function assertTableColumnHasExtraAttributes(string $name, $value, $record): static {}

        public function assertTableColumnDoesNotHaveExtraAttributes(string $name, $value, $record): static {}

        public function assertTableColumnHasDescription(string $name, $description, $record, $position = 'below'): static {}

        public function assertTableColumnDoesNotHaveDescription(string $name, $description, $record, $position = 'below'): static {}

        public function assertTableSelectColumnHasOptions(string $name, array $options, $record): static {}

        public function assertTableSelectColumnDoesNotHaveOptions(string $name, array $options, $record): static {}

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
