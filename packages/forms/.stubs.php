<?php

namespace Livewire\Testing {

    class TestableLivewire {
        public function assertFormExists(string $name = 'form'): static {}

        public function assertFormFieldExists(string $fieldName, string $formName = 'form'): static {}

        public function assertFormFieldIsDisabled(string $fieldName, string $formName = 'form'): static {}

        public function assertFormFieldIsEnabled(string $fieldName, string $formName = 'form'): static {}

        public function assertFormFieldIsHidden(string $fieldName, string $formName = 'form'): static {}

        public function assertFormFieldIsVisible(string $fieldName, string $formName = 'form'): static {}
    }

}
