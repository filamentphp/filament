<?php

namespace Livewire\Features\SupportTesting {

    use Closure;

    class Testable {
        public function fillForm(array | Closure $state = [], ?string $form = null): static {}

        /**
         * @deprecated Use `assertSchemaStateSet()` instead.
         */
        public function assertFormSet(array | Closure $state, string $form = 'form'): static {}

        public function assertHasFormErrors(array $keys = [], ?string $form = null): static {}

        public function assertHasNoFormErrors(array $keys = [], ?string $form = null): static {}

        public function assertFormFieldExists(string $key, string | Closure | null $form = null, ?Closure $checkFieldUsing = null): static {}

        public function assertFormFieldDoesNotExist(string $key, ?string $form = null): static {}

        public function assertFormFieldDisabled(string $key, ?string $form = null): static {}

        public function assertFormFieldEnabled(string $key, ?string $form = null): static {}

        public function assertFormFieldReadOnly(string $key, ?string $form = null): static {}

        /**
         * @deprecated Use `assertSchemaExists()` instead.
         */
        public function assertFormExists(string $name = 'form'): static {}

        /**
         * @deprecated Use `assertSchemaComponentHidden()` instead.
         */
        public function assertFormFieldHidden(string $key, string $form = 'form'): static {}

        /**
         * @deprecated Use `assertSchemaComponentVisible()` instead.
         */
        public function assertFormFieldVisible(string $key, string $form = 'form'): static {}

        /**
         * @deprecated Use `assertSchemaComponentExists()` instead.
         */
        public function assertFormComponentExists(string $componentKey, string | Closure $form = 'form', ?Closure $checkComponentUsing = null): static {}

        /**
         * @deprecated Use `assertSchemaComponentDoesNotExist()` instead.
         */
        public function assertFormComponentDoesNotExist(string $componentKey, string $form = 'form'): static {}

        /**
         * @deprecated Use `mountAction()` instead.
         */
        public function mountFormComponentAction(string | array $component, string | array $name, array $arguments = [], string $formName = 'form'): static {}

        /**
         * @deprecated Use `unmountAction()` instead.
         */
        public function unmountFormComponentAction(): static {}

        /**
         * @deprecated Use `fillForm()` instead.
         */
        public function setFormComponentActionData(array $data): static {}

        /**
         * @deprecated Use `assertSchemaStateSet()` instead.
         */
        public function assertFormComponentActionDataSet(array $data): static {}

        /**
         * @deprecated Use `callAction()` instead.
         */
        public function callFormComponentAction(string | array $component, string | array $name, array $data = [], array $arguments = [], string $formName = 'form'): static {}

        /**
         * @deprecated Use `callMountedAction()` instead.
         */
        public function callMountedFormComponentAction(array $arguments = []): static {}

        /**
         * @deprecated Use `assertActionExists()` instead.
         */
        public function assertFormComponentActionExists(string | array $component, string | array $name, string $formName = 'form'): static {}

        /**
         * @deprecated Use `assertActionDoesNotExist()` instead.
         */
        public function assertFormComponentActionDoesNotExist(string | array $component, string | array $name, string $formName = 'form'): static {}

        /**
         * @deprecated Use `assertActionVisible()` instead.
         */
        public function assertFormComponentActionVisible(string | array $component, string | array $name, array $arguments = [], string $formName = 'form'): static {}

        /**
         * @deprecated Use `assertActionHidden()` instead.
         */
        public function assertFormComponentActionHidden(string | array $component, string | array $name, array $arguments = [], string $formName = 'form'): static {}

        /**
         * @deprecated Use `assertActionEnabled()` instead.
         */
        public function assertFormComponentActionEnabled(string | array $component, string | array $name, array $arguments = [], string $formName = 'form'): static {}

        /**
         * @deprecated Use `assertActionDisabled()` instead.
         */
        public function assertFormComponentActionDisabled(string | array $component, string | array $name, array $arguments = [], string $formName = 'form'): static {}

        /**
         * @deprecated Use `assertActionMounted()` instead.
         */
        public function assertFormComponentActionMounted(string | array $component, string | array $name, string $formName = 'form'): static {}

        /**
         * @deprecated Use `assertActionNotMounted()` instead.
         */
        public function assertFormComponentActionNotMounted(string | array $component, string | array $name, string $formName = 'form'): static {}

        /**
         * @deprecated Use `assertActionHalted()` instead.
         */
        public function assertFormComponentActionHalted(string | array $component, string | array $name, string $formName = 'form'): static {}

        /**
         * @deprecated Use `assertHasFormErrors()` instead.
         */
        public function assertHasFormComponentActionErrors(array $keys = []): static {}

        /**
         * @deprecated Use `assertHasNoFormErrors()` instead.
         */
        public function assertHasNoFormComponentActionErrors(array $keys = []): static {}

        /**
         * @deprecated Use `assertActionHasIcon()` instead.
         */
        public function assertFormComponentActionHasIcon(string | array $component, string | array $name, string $icon, array $arguments = [], string $formName = 'form'): static {}

        /**
         * @deprecated Use `assertActionDoesNotHaveIcon()` instead.
         */
        public function assertFormComponentActionDoesNotHaveIcon(string | array $component, string | array $name, string $icon, array $arguments = [], string $formName = 'form'): static {}

        /**
         * @deprecated Use `assertActionHasLabel()` instead.
         */
        public function assertFormComponentActionHasLabel(string | array $component, string | array $name, string $label, array $arguments = [], string $formName = 'form'): static {}

        /**
         * @deprecated Use `assertActionDoesNotHaveLabel()` instead.
         */
        public function assertFormComponentActionDoesNotHaveLabel(string | array $component, string | array $name, string $label, array $arguments = [], string $formName = 'form'): static {}

        /**
         * @deprecated Use `assertActionHasColor()` instead.
         */
        public function assertFormComponentActionHasColor(string | array $component, string | array $name, string | array $color, array $arguments = [], string $formName = 'form'): static {}

        /**
         * @deprecated Use `assertActionDoesNotHaveColor()` instead.
         */
        public function assertFormComponentActionDoesNotHaveColor(string | array $component, string | array $name, string | array $color, array $arguments = [], string $formName = 'form'): static {}

        /**
         * @deprecated Use `assertActionHasUrl()` instead.
         */
        public function assertFormComponentActionHasUrl(string | array $component, string | array $name, string $url, array $arguments = [], string $formName = 'form'): static {}

        /**
         * @deprecated Use `assertActionDoesNotHaveUrl()` instead.
         */
        public function assertFormComponentActionDoesNotHaveUrl(string | array $component, string | array $name, string $url, array $arguments = [], string $formName = 'form'): static {}

        /**
         * @deprecated Use `assertActionShouldOpenUrlInNewTab()` instead.
         */
        public function assertFormComponentActionShouldOpenUrlInNewTab(string | array $component, string | array $name, array $arguments = [], string $formName = 'form'): static {}

        /**
         * @deprecated Use `assertActionShouldNotOpenUrlInNewTab()` instead.
         */
        public function assertFormComponentActionShouldNotOpenUrlInNewTab(string | array $component, string | array $name, array $arguments = [], string $formName = 'form'): static {}
    }

}
