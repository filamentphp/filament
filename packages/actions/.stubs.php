<?php

namespace Livewire\Features\SupportTesting {

    class Testable {
        public function mountAction(string $name, array $arguments = []): static {}

        public function unmountAction(): static {}

        public function setActionData(array $data): static {}

        public function assertActionDataSet(array $data): static {}

        public function callAction(string $name, array $data = [], array $arguments = []): static {}

        public function callMountedAction(array $arguments = []): static {}

        public function assertActionExists(string $name): static {}

        public function assertActionDoesNotExist(string $name): static {}

        public function assertActionVisible(string $name): static {}

        public function assertActionHidden(string $name): static {}

        public function assertActionEnabled(string $name): static {}

        public function assertActionDisabled(string $name): static {}

        public function assertActionHasIcon(string $name, string $icon): static {}

        public function assertActionDoesNotHaveIcon(string $name, string $icon): static {}

        public function assertActionHasLabel(string $name, string $label): static {}

        public function assertActionHasColor(string $name, string $color): static {}

        public function assertActionDoesNotHaveColor(string $name, string $color): static {}

        public function assertActionHasUrl(string $name, string $url): static {}

        public function assertActionDoesNotHaveUrl(string $name, string $url): static {}

        public function assertActionShouldOpenUrlInNewTab(string $name): static {}

        public function assertActionShouldNotOpenUrlInNewTab(string $name): static {}

        public function assertActionDoesNotHaveLabel(string $name, string $label): static {}

        public function assertActionMounted(string $name): static {}

        public function assertActionNotMounted(string $name): static {}

        public function assertActionHalted(string $name): static {}

        public function assertHasActionErrors(array $keys = []): static {}

        public function assertHasNoActionErrors(array $keys = []): static {}

        public function parseActionName(string $name): string {}

        public function parseNestedActionName(string | array $name): array {}
    }

}
