<?php

namespace Livewire\Testing {

    class TestableLivewire {
        public function fillForm(array $state = []): static {}

        public function assertFormSet(array $state): static {}

        public function assertHasFormErrors(array $keys = []): static {}

        public function assertHasNoFormErrors(array $keys = []): static {}

        public function mountPageAction(string $name): static {}

        public function setPageActionData(array $data): static {}

        public function assertPageActionDataSet(array $data): static {}

        public function callPageAction(string $name, array $data = [], array $arguments = []): static {}

        public function callMountedPageAction(array $arguments = []): static {}

        public function assertPageActionExists(string $name): static {}

        public function assertPageActionVisible(string $name): static {}

        public function assertPageActionHidden(string $name): static {}

        public function assertPageActionEnabled(string $name): static {}

        public function assertPageActionDisabled(string $name): static {}

        public function assertPageActionHeld(string $name): static {}

        public function assertHasPageActionErrors(array $keys = []): static {}

        public function assertHasNoPageActionErrors(array $keys = []): static {}
    }

}
