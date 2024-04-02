<?php

namespace Livewire\Features\SupportTesting {

    use Closure;

    class Testable {
        public function mountAction(string | array $actions, array $arguments = []): static {}

        public function unmountAction(): static {}

        public function setActionData(array $data): static {}

        public function assertActionDataSet(array $data): static {}

        public function callAction(string | array $actions, array $data = [], array $arguments = []): static {}

        public function callMountedAction(array $arguments = []): static {}

        public function assertActionExists(string | array $actions, ?Closure $checkActionUsing = null, ?Closure $generateMessageUsing = null): static {}

        public function assertActionDoesNotExist(string | array $actions, ?Closure $checkActionUsing = null, ?Closure $generateMessageUsing = null): static {}

        public function assertActionVisible(string | array $actions): static {}

        public function assertActionHidden(string | array $actions): static {}

        public function assertActionEnabled(string | array $actions): static {}

        public function assertActionDisabled(string | array $actions): static {}

        public function assertActionHasIcon(string | array $actions, string $icon): static {}

        public function assertActionDoesNotHaveIcon(string | array $actions, string $icon): static {}

        public function assertActionHasLabel(string | array $actions, string $label): static {}

        public function assertActionHasColor(string | array $actions, string $color): static {}

        public function assertActionDoesNotHaveColor(string | array $actions, string $color): static {}

        public function assertActionHasUrl(string | array $actions, string $url): static {}

        public function assertActionDoesNotHaveUrl(string | array $actions, string $url): static {}

        public function assertActionShouldOpenUrlInNewTab(string | array $actions): static {}

        public function assertActionShouldNotOpenUrlInNewTab(string | array $actions): static {}

        public function assertActionDoesNotHaveLabel(string | array $actions, string $label): static {}

        public function assertActionMounted(string | array $actions): static {}

        public function assertActionNotMounted(string | array $actions): static {}

        public function assertActionHalted(string | array $actions): static {}

        public function assertHasActionErrors(array $keys = []): static {}

        public function assertHasNoActionErrors(array $keys = []): static {}

        public function parseActionName(string | array $actions): string {}

        public function parseNestedActionName(string | array $name): array {}

        public function parseNestedActions(string | array $actions, array $arguments = []): array {}
    }

}
