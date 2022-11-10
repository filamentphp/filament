<?php

namespace Livewire\Testing {

    class TestableLivewire {
        public function fillForm(array $state = [], string $formName = 'form'): static {}

        public function assertFormSet(array $state, string $formName = 'form'): static {}

        public function assertHasFormErrors(array $keys = [], string $formName = 'form'): static {}

        public function assertHasNoFormErrors(array $keys = [], string $formName = 'form'): static {}

        public function assertFormExists(string $name = 'form'): static {}

        public function assertFormFieldExists(string $fieldName, string $formName = 'form'): static {}

        public function assertFormFieldIsDisabled(string $fieldName, string $formName = 'form'): static {}

        public function assertFormFieldIsEnabled(string $fieldName, string $formName = 'form'): static {}

        public function assertFormFieldIsHidden(string $fieldName, string $formName = 'form'): static {}

        public function assertFormFieldIsVisible(string $fieldName, string $formName = 'form'): static {}
    }

}
