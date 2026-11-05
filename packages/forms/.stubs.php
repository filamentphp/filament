<?php

namespace Livewire\Features\SupportTesting {

    use Closure;

    class Testable {
        public function fillForm(array | Closure $state = [], ?string $form = null): static {}

        public function assertHasFormErrors(array $keys = [], ?string $form = null): static {}

        public function assertHasNoFormErrors(array $keys = [], ?string $form = null): static {}

        public function assertFormFieldExists(string $key, string | Closure | null $form = null, ?Closure $checkFieldUsing = null): static {}

        public function assertFormFieldDoesNotExist(string $key, ?string $form = null): static {}

        public function assertFormFieldDisabled(string $key, ?string $form = null): static {}

        public function assertFormFieldEnabled(string $key, ?string $form = null): static {}

        public function assertFormFieldReadOnly(string $key, ?string $form = null): static {}

        public function assertFormFieldHidden(string $key, ?string $form = null): static {}

        public function assertFormFieldVisible(string $key, ?string $form = null): static {}

        /**
         * @deprecated Use `mountAction()` instead.
         */
        public function mountFormComponentAction(string | array $components, string | array $actions, array $arguments = [], string $formName = 'form'): static {}

        /**
         * @deprecated Use `unmountAction()` instead.
         */
        public function unmountFormComponentAction(): static {}

        /**
         * @deprecated Use `callAction()` instead.
         */
        public function callFormComponentAction(string | array $components, string | array $actions, array $data = [], array $arguments = [], string $formName = 'form'): static {}

        /**
         * @deprecated Use `callMountedAction()` instead.
         */
        public function callMountedFormComponentAction(array $arguments = []): static {}
    }

}
