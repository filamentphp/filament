<?php

namespace Livewire\Testing {

    use Illuminate\Support\Collection;

    class TestableLivewire {
        public function mountTableAction(string $name, $record = null): static {}

        public function setTableActionData(array $data): static {}

        public function assertTableActionDataSet(array $data): static {}

        public function callTableAction(string $name, $record = null, array $data = [], array $arguments = []): static {}

        public function callMountedTableAction(array $arguments = []): static {}

        public function assertTableActionExists(string $name): static {}

        public function assertTableActionDoesNotExist(string $name): static {}

        public function assertTableActionVisible(string $name, $record = null): static {}

        public function assertTableActionHidden(string $name, $record = null): static {}

        public function assertTableActionEnabled(string $name, $record = null): static {}

        public function assertTableActionDisabled(string $name, $record = null): static {}

        public function assertTableActionHeld(string $name): static {}

        public function assertHasTableActionErrors(array $keys = []): static {}

        public function assertHasNoTableActionErrors(array $keys = []): static {}

        public function mountTableBulkAction(string $name, array | Collection $records): static {}

        public function setTableBulkActionData(array $data): static {}

        public function assertTableBulkActionDataSet(array $data): static {}

        public function callTableBulkAction(string $name, array | Collection $records, array $data = [], array $arguments = []): static {}

        public function callMountedTableBulkAction(array $arguments = []): static {}

        public function assertTableBulkActionExists(string $name): static {}

        public function assertTableBulkActionDoesNotExist(string $name): static {}

        public function assertTableBulkActionVisible(string $name): static {}

        public function assertTableBulkActionHidden(string $name): static {}

        public function assertTableBulkActionEnabled(string $name): static {}

        public function assertTableBulkActionDisabled(string $name): static {}

        public function assertTableActionHasIcon(string $name, string $icon): static {}

        public function assertTableActionDoesNotHaveIcon(string $name, string $icon): static {}

        public function assertTableActionHasLabel(string $name, string $label): static {}

        public function assertTableActionHasColor(string $name, string $color): static {}

        public function assertTableActionDoesNotHaveColor(string $name, string $color): static {}

        public function assertTableActionDoesNotHaveLabel(string $name, string $label): static {}

        public function assertTableBulkActionHasIcon(string $name, string $icon): static {}

        public function assertTableBulkActionDoesNotHaveIcon(string $name, string $icon): static {}

        public function assertTableBulkActionHasLabel(string $name, string $label): static {}

        public function assertTableBulkActionDoesNotHaveLabel(string $name, string $label): static {}

        public function assertTableBulkActionHasColor(string $name, string $color): static {}

        public function assertTableBulkActionDoesNotHaveColor(string $name, string $color): static {}

        public function assertTableBulkActionHeld(string $name): static {}

        public function assertHasTableBulkActionErrors(array $keys = []): static {}

        public function assertHasNoTableBulkActionErrors(array $keys = []): static {}

        public function assertCanRenderTableColumn(string $name): static {}

        public function assertTableColumnExists(string $name): static {}

        public function assertTableColumnVisible(string $name): static {}

        public function assertTableColumnHidden(string $name): static {}

        public function sortTable(?string $name = null, ?string $direction = null): static {}

        public function searchTable(?string $search = null): static {}

        public function filterTable(string $name, $data = null): static {}

        public function resetTableFilters(): static {}

        public function removeTableFilter(string $filter, ?string $field = null): static {}

        public function removeTableFilters(): static {}

        public function assertCanSeeTableRecords(array | Collection $records, bool $inOrder = false): static {}

        public function assertCanNotSeeTableRecords(array | Collection $records): static {}

        public function assertCountTableRecords(int $count): static {}
    }

}
