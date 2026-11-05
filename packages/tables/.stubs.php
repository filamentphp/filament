<?php

namespace Livewire\Features\SupportTesting {

    use Closure;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Collection;

    class Testable {
        public function selectTableRecords(array | Collection $records): static {}

        public function assertCanRenderTableColumn(string $name): static {}

        public function assertCanNotRenderTableColumn(string $name): static {}

        public function assertTableColumnExists(string $name, ?Closure $checkColumnUsing = null, $record = null): static {}

        public function assertTableColumnDoesNotExist(string $name, ?Closure $checkColumnUsing = null, $record = null): static {}

        public function assertTableColumnVisible(string $name): static {}

        public function assertTableColumnHidden(string $name): static {}

        public function assertTableColumnStateSet(string $name, $state, $record): static {}

        public function assertTableColumnStateNotSet(string $name, $state, $record): static {}

        public function assertTableColumnSummarizerExists(string $columnName, string $summarizerId): static {}

        public function assertTableColumnSummarySet(string $columnName, string $summarizerId, $state, bool $isCurrentPaginationPageOnly = false): static {}

        public function assertTableColumnSummaryNotSet(string $columnName, string $summarizerId, $state, bool $isCurrentPaginationPageOnly = false): static {}

        public function assertTableColumnFormattedStateSet(string $name, $state, $record): static {}

        public function assertTableColumnFormattedStateNotSet(string $name, $state, $record): static {}

        public function assertTableColumnHasExtraAttributes(string $name, array $attributes, $record): static {}

        public function assertTableColumnDoesNotHaveExtraAttributes(string $name, array $attributes, $record): static {}

        public function assertTableColumnHasDescription(string $name, $description, $record, string $position = 'below'): static {}

        public function assertTableColumnDoesNotHaveDescription(string $name, $description, $record, string $position = 'below'): static {}

        public function assertTableSelectColumnHasOptions(string $name, array $options, $record): static {}

        public function assertTableSelectColumnDoesNotHaveOptions(string $name, array $options, $record): static {}

        public function sortTable(?string $name = null, ?string $direction = null): static {}

        public function searchTable(?string $search = null): static {}

        public function searchTableColumns(array $searches): static {}

        public function filterTable(string $name, $data = null): static {}

        public function resetTableFilters(): static {}

        public function removeTableFilter(string $filter, ?string $field = null): static {}

        public function removeTableFilters(): static {}

        public function assertTableFilterVisible(string $name): static {}

        public function assertTableFilterHidden(string $name): static {}

        public function assertTableFilterExists(string $name, ?Closure $checkFilterUsing = null): static {}

        public function assertCanSeeTableRecords(array | Collection $records, bool $inOrder = false): static {}

        public function assertCanNotSeeTableRecords(array | Collection $records): static {}

        public function assertCountTableRecords(int $count): static {}

        public function toggleAllTableColumns(bool $condition = true): static {}

        public function loadTable(): static {}

        /**
         * @deprecated Use `mountAction()` instead.
         */
        public function mountTableAction(string | array $actions, $record = null): static {}

        /**
         * @deprecated Use `unmountAction()` instead.
         */
        public function unmountTableAction(): static {}

        /**
         * @deprecated Use `callAction()` instead.
         */
        public function callTableAction(string | array $actions, $record = null, array $data = [], array $arguments = []): static {}

        public function callTableColumnAction(string $name, $record = null): static {}

        /**
         * @deprecated Use `callMountedAction()` instead.
         */
        public function callMountedTableAction(array $arguments = []): static {}

        public function assertTableRecordActionsExistInOrder(array $names): static {}

        public function assertTableHeaderActionsExistInOrder(array $names): static {}

        public function assertTableEmptyStateActionsExistInOrder(array $names): static {}

        /**
         * @deprecated Use `selectTableRecords()` and `mountAction()` instead.
         */
        public function mountTableBulkAction(string | array $actions, array | Collection $records): static {}

        /**
         * @deprecated Use `callAction()` instead.
         */
        public function callTableBulkAction(string | array $actions, array | Collection $records, array $data = [], array $arguments = []): static {}

        /**
         * @deprecated Use `callMountedAction()` instead.
         */
        public function callMountedTableBulkAction(array $arguments = []): static {}
    }

}
