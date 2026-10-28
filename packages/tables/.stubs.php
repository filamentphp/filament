<?php

namespace Livewire\Features\SupportTesting {

    use BackedEnum;
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
         * @deprecated Use `fillForm()` instead.
         */
        public function setTableActionData(array $data): static {}

        /**
         * @deprecated Use `assertSchemaStateSet()` instead.
         */
        public function assertTableActionDataSet(array | Closure $data): static {}

        /**
         * @deprecated Use `callAction()` instead.
         */
        public function callTableAction(string | array $actions, $record = null, array $data = [], array $arguments = []): static {}

        public function callTableColumnAction(string $name, $record = null): static {}

        /**
         * @deprecated Use `callMountedAction()` instead.
         */
        public function callMountedTableAction(array $arguments = []): static {}

        /**
         * @deprecated Use `assertActionExists()` instead.
         */
        public function assertTableActionExists(string | array $actions, ?Closure $checkActionUsing = null, $record = null): static {}

        /**
         * @deprecated Use `assertActionDoesNotExist()` instead.
         */
        public function assertTableActionDoesNotExist(string | array $actions, ?Closure $checkActionUsing = null, $record = null): static {}

        /**
         * @deprecated Use `assertTableRecordActionsExistInOrder()` instead.
         */
        public function assertTableActionsExistInOrder(array $names): static {}

        public function assertTableRecordActionsExistInOrder(array $names): static {}

        public function assertTableHeaderActionsExistInOrder(array $names): static {}

        public function assertTableEmptyStateActionsExistInOrder(array $names): static {}

        /**
         * @deprecated Use `assertActionVisible()` instead.
         */
        public function assertTableActionVisible(string | array $actions, $record = null): static {}

        /**
         * @deprecated Use `assertActionHidden()` instead.
         */
        public function assertTableActionHidden(string | array $actions, $record = null): static {}

        /**
         * @deprecated Use `assertActionEnabled()` instead.
         */
        public function assertTableActionEnabled(string | array $actions, $record = null): static {}

        /**
         * @deprecated Use `assertActionDisabled()` instead.
         */
        public function assertTableActionDisabled(string | array $actions, $record = null): static {}

        /**
         * @deprecated Use `assertActionMounted()` instead.
         */
        public function assertTableActionMounted(string | array $actions): static {}

        /**
         * @deprecated Use `assertActionNotMounted()` instead.
         */
        public function assertTableActionNotMounted(string | array $actions): static {}

        /**
         * @deprecated Use `assertActionHalted()` instead.
         */
        public function assertTableActionHalted(string | array $actions): static {}

        /**
         * @deprecated Use `assertActionHalted()` with table context instead.
         */
        public function assertTableActionHeld(string | array $actions): static {}

        /**
         * @deprecated Use `assertHasFormErrors()` instead.
         */
        public function assertHasTableActionErrors(array $keys = []): static {}

        /**
         * @deprecated Use `assertHasNoFormErrors()` instead.
         */
        public function assertHasNoTableActionErrors(array $keys = []): static {}

        /**
         * @deprecated Use `selectTableRecords()` and `mountAction()` instead.
         */
        public function mountTableBulkAction(string | array $actions, array | Collection $records): static {}

        /**
         * @deprecated Use `fillForm()` instead.
         */
        public function setTableBulkActionData(array $data): static {}

        /**
         * @deprecated Use `assertSchemaStateSet()` instead.
         */
        public function assertTableBulkActionDataSet(array | Closure $data): static {}

        /**
         * @deprecated Use `callAction()` instead.
         */
        public function callTableBulkAction(string | array $actions, array | Collection $records, array $data = [], array $arguments = []): static {}

        /**
         * @deprecated Use `callMountedAction()` instead.
         */
        public function callMountedTableBulkAction(array $arguments = []): static {}

        /**
         * @deprecated Use `assertActionExists()` instead.
         */
        public function assertTableBulkActionExists(string | array $actions): static {}

        /**
         * @deprecated Use `assertActionDoesNotExist()` instead.
         */
        public function assertTableBulkActionDoesNotExist(string | array $actions): static {}

        /**
         * @deprecated Use `assertActionListInOrder()` instead.
         */
        public function assertTableBulkActionsExistInOrder(array $names): static {}

        /**
         * @deprecated Use `assertActionVisible()` instead.
         */
        public function assertTableBulkActionVisible(string | array $actions): static {}

        /**
         * @deprecated Use `assertActionHidden()` instead.
         */
        public function assertTableBulkActionHidden(string | array $actions): static {}

        /**
         * @deprecated Use `assertActionEnabled()` instead.
         */
        public function assertTableBulkActionEnabled(string | array $actions): static {}

        /**
         * @deprecated Use `assertActionDisabled()` instead.
         */
        public function assertTableBulkActionDisabled(string | array $actions): static {}

        /**
         * @deprecated Use `assertActionHasIcon()` instead.
         */
        public function assertTableActionHasIcon(string | array $actions, string | BackedEnum $icon, $record = null): static {}

        /**
         * @deprecated Use `assertActionDoesNotHaveIcon()` instead.
         */
        public function assertTableActionDoesNotHaveIcon(string | array $actions, string | BackedEnum $icon, $record = null): static {}

        /**
         * @deprecated Use `assertActionHasLabel()` instead.
         */
        public function assertTableActionHasLabel(string | array $actions, string $label, $record = null): static {}

        /**
         * @deprecated Use `assertActionDoesNotHaveLabel()` instead.
         */
        public function assertTableActionDoesNotHaveLabel(string | array $actions, string $label, $record = null): static {}

        /**
         * @deprecated Use `assertActionHasColor()` instead.
         */
        public function assertTableActionHasColor(string | array $actions, string | array $color, $record = null): static {}

        /**
         * @deprecated Use `assertActionDoesNotHaveColor()` instead.
         */
        public function assertTableActionDoesNotHaveColor(string | array $actions, string | array $color, $record = null): static {}

        /**
         * @deprecated Use `assertActionHasIcon()` instead.
         */
        public function assertTableBulkActionHasIcon(string | array $actions, string | BackedEnum $icon): static {}

        /**
         * @deprecated Use `assertActionDoesNotHaveIcon()` instead.
         */
        public function assertTableBulkActionDoesNotHaveIcon(string | array $actions, string | BackedEnum $icon): static {}

        /**
         * @deprecated Use `assertActionHasLabel()` instead.
         */
        public function assertTableBulkActionHasLabel(string | array $actions, string $label): static {}

        /**
         * @deprecated Use `assertActionDoesNotHaveLabel()` instead.
         */
        public function assertTableBulkActionDoesNotHaveLabel(string | array $actions, string $label): static {}

        /**
         * @deprecated Use `assertActionHasColor()` instead.
         */
        public function assertTableBulkActionHasColor(string | array $actions, string | array $color): static {}

        /**
         * @deprecated Use `assertActionDoesNotHaveColor()` instead.
         */
        public function assertTableBulkActionDoesNotHaveColor(string | array $actions, string | array $color): static {}

        /**
         * @deprecated Use `assertActionHasUrl()` instead.
         */
        public function assertTableActionHasUrl(string | array $actions, string $url, $record = null): static {}

        /**
         * @deprecated Use `assertActionDoesNotHaveUrl()` instead.
         */
        public function assertTableActionDoesNotHaveUrl(string | array $actions, string $url, $record = null): static {}

        /**
         * @deprecated Use `assertActionShouldOpenUrlInNewTab()` instead.
         */
        public function assertTableActionShouldOpenUrlInNewTab(string | array $actions, $record = null): static {}

        /**
         * @deprecated Use `assertActionShouldNotOpenUrlInNewTab()` instead.
         */
        public function assertTableActionShouldNotOpenUrlInNewTab(string | array $actions, $record = null): static {}

        /**
         * @deprecated Use `assertActionMounted()` instead.
         */
        public function assertTableBulkActionMounted(string | array $actions): static {}

        /**
         * @deprecated Use `assertActionNotMounted()` instead.
         */
        public function assertTableBulkActionNotMounted(string | array $actions): static {}

        /**
         * @deprecated Use `assertActionHalted()` instead.
         */
        public function assertTableBulkActionHalted(string | array $actions): static {}

        /**
         * @deprecated Use `assertActionHalted()` with table and bulk context instead.
         */
        public function assertTableBulkActionHeld(string | array $actions): static {}

        /**
         * @deprecated Use `assertHasFormErrors()` instead.
         */
        public function assertHasTableBulkActionErrors(array $keys = []): static {}

        /**
         * @deprecated Use `assertHasNoFormErrors()` instead.
         */
        public function assertHasNoTableBulkActionErrors(array $keys = []): static {}
    }

}
