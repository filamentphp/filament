<?php

namespace Livewire\Features\SupportTesting {

    use Illuminate\Support\Collection;
    use Closure;

    class Testable {
        public function fillForm(array $state = [], string $formName = 'form'): static {}

        public function assertFormSet(array $state, string $formName = 'form'): static {}

        public function assertHasFormErrors(array $keys = [], string $formName = 'form'): static {}

        public function assertHasNoFormErrors(array $keys = [], string $formName = 'form'): static {}

        public function assertFormExists(string | array $name = 'form'): static {}

        public function assertFormFieldExists(string $fieldName, string | Closure $formName = 'form', ?Closure $checkFieldUsing = null): static {}

        public function assertFormFieldIsDisabled(string $fieldName, string $formName = 'form'): static {}

        public function assertFormFieldIsEnabled(string $fieldName, string $formName = 'form'): static {}

        public function assertFormFieldIsHidden(string $fieldName, string $formName = 'form'): static {}

        public function assertFormFieldIsVisible(string $fieldName, string $formName = 'form'): static {}

        public function mountFormComponentAction(string | array $component, string | array $name, array $arguments = [], string $formName = 'form'): static {}

        public function unmountFormComponentAction(): static {}

        public function setFormComponentActionData(array $data): static {}

        public function assertFormComponentActionDataSet(array $data): static {}

        public function callFormComponentAction(string | array $component, string | array $name, array $data = [], array $arguments = [], string $formName = 'form'): static {}

        public function callMountedFormComponentAction(array $arguments = []): static {}

        public function assertFormComponentActionExists(string | array $component, string | array $name, string $formName = 'form'): static {}

        public function assertFormComponentActionDoesNotExist(string | array $component, string | array $name, string $formName = 'form'): static {}

        public function assertFormComponentActionVisible(string | array $component, string | array $name, array $arguments = [], string $formName = 'form'): static {}

        public function assertFormComponentActionHidden(string | array $component, string | array $name, array $arguments = [], string $formName = 'form'): static {}

        public function assertFormComponentActionEnabled(string | array $component, string | array $name, array $arguments = [], string $formName = 'form'): static {}

        public function assertFormComponentActionDisabled(string | array $component, string | array $name, array $arguments = [], string $formName = 'form'): static {}

        public function assertFormComponentActionMounted(string | array $component, string | array $name, string $formName = 'form'): static {}

        public function assertFormComponentActionNotMounted(string | array $component, string | array $name, string $formName = 'form'): static {}

        public function assertFormComponentActionHalted(string | array $component, string | array $name, string $formName = 'form'): static {}

        public function assertHasFormComponentActionErrors(array $keys = []): static {}

        public function assertHasNoFormComponentActionErrors(array $keys = []): static {}

        public function assertFormComponentActionHasIcon(string | array $component, string | array $name, string $icon, array $arguments = [], string $formName = 'form'): static {}

        public function assertFormComponentActionDoesNotHaveIcon(string | array $component, string | array $name, string $icon, array $arguments = [], string $formName = 'form'): static {}

        public function assertFormComponentActionHasLabel(string | array $component, string | array $name, string $label, array $arguments = [], string $formName = 'form'): static {}

        public function assertFormComponentActionDoesNotHaveLabel(string | array $component, string | array $name, string $label, array $arguments = [], string $formName = 'form'): static {}

        public function assertFormComponentActionHasColor(string | array $component, string | array $name, string | array $color, array $arguments = [], string $formName = 'form'): static {}

        public function assertFormComponentActionDoesNotHaveColor(string | array $component, string | array $name, string | array $color, array $arguments = [], string $formName = 'form'): static {}

        public function assertFormComponentActionHasUrl(string | array $component, string | array $name, string $url, array $arguments = [], string $formName = 'form'): static {}

        public function assertFormComponentActionDoesNotHaveUrl(string | array $component, string | array $name, string $url, array $arguments = [], string $formName = 'form'): static {}

        public function assertFormComponentActionShouldOpenUrlInNewTab(string | array $component, string | array $name, array $arguments = [], string $formName = 'form'): static {}

        public function assertFormComponentActionShouldNotOpenUrlInNewTab(string | array $component, string | array $name, array $arguments = [], string $formName = 'form'): static {}

        public function getNestedFormComponentActionComponentAndName(string | array $component, string | array $name, string $formName = 'form'): static {}

        public function parseNestedFormComponentActionComponentAndName(string | array $component, string | array $name, string $formName = 'form'): static {}
    }

}
