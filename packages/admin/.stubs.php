<?php

namespace Livewire\Testing {

    class TestableLivewire {
        public function mountPageAction(string $name): static {}

        public function setPageActionData(array $data): static {}

        public function assertPageActionDataSet(array $data): static {}

        public function callPageAction(string $name, array $data = [], array $arguments = []): static {}

        public function callMountedPageAction(array $arguments = []): static {}

        public function assertPageActionExists(string $name): static {}

        public function assertPageActionDoesNotExist(string $name): static {}

        public function assertPageActionsExistInOrder(array $names): static {}

        public function assertPageActionVisible(string $name): static {}

        public function assertPageActionHidden(string $name): static {}

        public function assertPageActionEnabled(string $name): static {}

        public function assertPageActionDisabled(string $name): static {}

        public function assertPageActionHasIcon(string $name, string $icon): static {}

        public function assertPageActionDoesNotHaveIcon(string $name, string $icon): static {}

        public function assertPageActionHasLabel(string $name, string $label): static {}

        public function assertPageActionDoesNotHaveLabel(string $name, string $label): static {}

        public function assertPageActionHasColor(string $name, string $color): static {}

        public function assertPageActionDoesNotHaveColor(string $name, string $color): static {}

        public function assertPageActionHasUrl(string $name, string $url): static {}

        public function assertPageActionDoesNotHaveUrl(string $name, string $url): static {}

        public function assertPageActionShouldOpenUrlInNewTab(string $name): static {}

        public function assertPageActionShouldNotOpenUrlInNewTab(string $name): static {}

        public function assertPageActionHalted(string $name): static {}

        public function assertHasPageActionErrors(array $keys = []): static {}

        public function assertHasNoPageActionErrors(array $keys = []): static {}
    }

}
