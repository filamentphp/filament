<?php

namespace Livewire\Features\SupportTesting {

    use Closure;

    class Testable {
        public function fillForm(array | Closure $state = [], string $formName = 'form'): static {}

        public function assertFormSet(array | Closure $state, string $formName = 'form'): static {}

        public function assertHasFormErrors(array $keys = [], string $formName = 'form'): static {}

        public function assertHasNoFormErrors(array $keys = [], string $formName = 'form'): static {}

        public function assertFormExists(string | array $name = 'form'): static {}

        public function assertFormFieldExists(string $fieldName, string | Closure $formName = 'form', ?Closure $checkFieldUsing = null): static {}

        public function assertFormFieldDoesNotExist(string $fieldName, string $formName = 'form'): static {}

        public function assertFormFieldIsDisabled(string $fieldName, string $formName = 'form'): static {}

        public function assertFormFieldIsEnabled(string $fieldName, string $formName = 'form'): static {}

        public function assertFormFieldIsHidden(string $fieldName, string $formName = 'form'): static {}

        public function assertFormFieldIsVisible(string $fieldName, string $formName = 'form'): static {}

        public function assertFormComponentExists(string $componentKey, string | Closure $formName = 'form', ?Closure $checkComponentUsing = null): static {}

        public function assertFormComponentDoesNotExist(string $componentKey, string $formName = 'form'): static {}

        public function goToWizardStep(int $step, string $formName = 'form'): static {}

        public function goToNextWizardStep(string $formName = 'form'): static {}

        public function goToPreviousWizardStep(string $formName = 'form'): static {}

        public function assertWizardStepExists(int $step, string $formName = 'form'): static {}

        public function assertWizardCurrentStep(int $step, string $formName = 'form'): static {}
    }

}
