<?php

use Closure;

namespace Livewire\Features\SupportTesting {

    class Testable {
        public function fillForm(array $state = [], string $formName = 'form'): static {}

        public function assertFormSet(array $state, string $formName = 'form'): static {}

        public function assertHasFormErrors(array $keys = [], string $formName = 'form'): static {}

        public function assertHasNoFormErrors(array $keys = [], string $formName = 'form'): static {}

        public function assertFormExists(string $name = 'form'): static {}

        public function assertFormFieldExists(string $fieldName, string | Closure $formName = 'form', ?Closure $checkFieldUsing = null): static {}

        public function assertFormFieldIsDisabled(string $fieldName, string $formName = 'form'): static {}

        public function assertFormFieldIsEnabled(string $fieldName, string $formName = 'form'): static {}

        public function assertFormFieldIsHidden(string $fieldName, string $formName = 'form'): static {}

        public function assertFormFieldIsVisible(string $fieldName, string $formName = 'form'): static {}
    }

}
