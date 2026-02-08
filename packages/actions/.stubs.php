<?php

namespace Livewire\Features\SupportTesting {

    use BackedEnum;
    use Closure;
    use Filament\Actions\Testing\TestAction;

    class Testable {
        public function mountAction(string | TestAction | array $actions, array $arguments = []): static {}

        public function unmountAction(): static {}

        /**
         * @deprecated Use `fillForm()` instead.
         */
        public function setActionData(array $data): static {}

        /**
         * @deprecated Use `assertSchemaStateSet()` instead.
         */
        public function assertActionDataSet(array | Closure $data): static {}

        public function callAction(string | TestAction | array $actions, array $data = [], array $arguments = []): static {}

        public function callMountedAction(array $arguments = []): static {}

        public function assertActionExists(string | TestAction | array $actions, ?Closure $checkActionUsing = null, ?Closure $generateMessageUsing = null, array $arguments = []): static {}

        public function assertActionDoesNotExist(string | TestAction | array $actions, ?Closure $checkActionUsing = null, ?Closure $generateMessageUsing = null): static {}

        public function assertActionVisible(string | TestAction | array $actions, array $arguments = []): static {}

        public function assertActionHidden(string | TestAction | array $actions, array $arguments = []): static {}

        public function assertActionEnabled(string | TestAction | array $actions): static {}

        public function assertActionDisabled(string | TestAction | array $actions): static {}

        public function assertActionHasIcon(string | TestAction | array $actions, string | BackedEnum $icon): static {}

        public function assertActionDoesNotHaveIcon(string | TestAction | array $actions, string | BackedEnum $icon): static {}

        public function assertActionHasLabel(string | TestAction | array $actions, string $label): static {}

        public function assertActionHasColor(string | TestAction | array $actions, string $color): static {}

        public function assertActionDoesNotHaveColor(string | TestAction | array $actions, string $color): static {}

        public function assertActionHasUrl(string | TestAction | array $actions, string $url): static {}

        public function assertActionDoesNotHaveUrl(string | TestAction | array $actions, string $url): static {}

        public function assertActionShouldOpenUrlInNewTab(string | TestAction | array $actions): static {}

        public function assertActionShouldNotOpenUrlInNewTab(string | TestAction | array $actions): static {}

        public function assertActionDoesNotHaveLabel(string | TestAction | array $actions, string $label): static {}

        public function assertActionMounted(string | TestAction | array $actions): static {}

        public function assertActionNotMounted(string | TestAction | array $actions = []): static {}

        public function assertMountedActionModalSee(string | array $values, bool $escape = true): static {}

        public function assertMountedActionModalDontSee(string | array $values, bool $escape = true): static {}

        public function assertMountedActionModalSeeHtml(string | array $values): static {}

        public function assertMountedActionModalDontSeeHtml(string | array $values): static {}

        public function assertActionHalted(string | TestAction | array $actions = []): static {}

        /**
         * @deprecated Use `assertHasFormErrors()` instead.
         */
        public function assertHasActionErrors(array $keys = []): static {}

        /**
         * @deprecated Use `assertHasNoFormErrors()` instead.
         */
        public function assertHasNoActionErrors(array $keys = []): static {}

        public function parseActionName(string | TestAction | array $actions): string {}

        public function parseNestedActionName(string | array $name): array {}

        public function parseNestedActions(string | TestAction | array $actions, array $arguments = [], bool $areRelativeToMountedActions = true): array {}
    }

}
