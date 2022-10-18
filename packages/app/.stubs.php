<?php

namespace Livewire\Testing {

    class TestableLivewire {
        public function fillForm(array $state = []): static {}

        public function assertFormSet(array $state): static {}

        public function assertHasFormErrors(array $keys = []): static {}

        public function assertHasNoFormErrors(array $keys = []): static {}
    }

}
