<?php

namespace Livewire\Testing {

    use Illuminate\Support\Collection;

    class TestableLivewire {
        public function callTableAction(string $name, $record = null, array $data = [], array $arguments = []): static {}

        public function assertTableActionExists(string $name): static {}

        public function assertTableActionHeld(string $name): static {}

        public function assertHasTableActionErrors(array $keys = []): static {}

        public function assertHasNoTableActionErrors(array $keys = []): static {}

        public function callTableBulkAction(string $name, array | Collection $records, array $data = [], array $arguments = []): static {}

        public function assertTableBulkActionExists(string $name): static {}

        public function assertTableBulkActionHeld(string $name): static {}

        public function assertHasTableBulkActionErrors(array $keys = []): static {}

        public function assertHasNoTableBulkActionErrors(array $keys = []): static {}

        public function assertCanRenderTableColumn(string $name): static {}

        public function assertTableColumnExists(string $name): static {}

        public function sortTable(?string $column = null, ?string $direction = null): static {}

        public function searchTable(?string $search = null): static {}

        public function filterTable(string $filter, ?array $data = null): static {}

        public function resetTableFilters(): static {}

        public function assertCanSeeTableRecords(array | Collection $records, bool $inOrder = false): static {}

        public function assertCanNotSeeTableRecords(array | Collection $records): static {}

        public function assertCountTableRecords(int $count): static {}
    }

}
