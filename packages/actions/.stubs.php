<?php

namespace Livewire\Features\SupportTesting {

    class Testable {
        public function mountAction(string | array $name, array $arguments = []): static {}

        public function unmountAction(): static {}

        public function setActionData(array $data): static {}

        public function assertActionDataSet(array $data): static {}

        public function callAction(string | array $name, array $data = [], array $arguments = []): static {}

        public function callMountedAction(array $arguments = []): static {}

        public function assertActionExists(string | array $name): static {}

        public function assertActionDoesNotExist(string | array $name): static {}

        public function assertActionVisible(string | array $name): static {}

        public function assertActionHidden(string | array $name): static {}

        public function assertActionEnabled(string | array $name): static {}

        public function assertActionDisabled(string | array $name): static {}

        public function assertActionHasIcon(string | array $name, string $icon): static {}

        public function assertActionDoesNotHaveIcon(string | array $name, string $icon): static {}

        public function assertActionHasLabel(string | array $name, string $label): static {}

        public function assertActionHasColor(string | array $name, string $color): static {}

        public function assertActionDoesNotHaveColor(string | array $name, string $color): static {}

        public function assertActionHasUrl(string | array $name, string $url): static {}

        public function assertActionDoesNotHaveUrl(string | array $name, string $url): static {}

        public function assertActionShouldOpenUrlInNewTab(string | array $name): static {}

        public function assertActionShouldNotOpenUrlInNewTab(string | array $name): static {}

        public function assertActionDoesNotHaveLabel(string | array $name, string $label): static {}

        public function assertActionMounted(string | array $name): static {}

        public function assertActionNotMounted(string | array $name): static {}

        public function assertActionHalted(string | array $name): static {}

        public function assertHasActionErrors(array $keys = []): static {}

        public function assertHasNoActionErrors(array $keys = []): static {}

        public function parseActionName(string | array $name): string {}

        public function parseNestedActionName(string | array $name): array {}
    }

}
