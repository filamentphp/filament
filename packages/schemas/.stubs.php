<?php

namespace Livewire\Features\SupportTesting {

    use Closure;

    class Testable {

        public function assertSchemaComponentExists(string $key, ?string $schema = null, ?Closure $checkComponentUsing = null): static {}

        public function assertSchemaComponentDoesNotExist(string $key, ?string $schema = null): static {}

        public function assertSchemaExists(string $name): static {}

        public function assertSchemaStateSet(array | Closure $state, ?string $schema = null): static {}

        public function assertSchemaComponentStateSet(string $key, mixed $state, ?string $schema = null): static {}

        public function assertSchemaComponentStateNotSet(string $key, mixed $state, ?string $schema = null): static {}

        public function goToWizardStep(int $step, ?string $schema = null): static {}

        public function goToNextWizardStep(?string $schema = null): static {}

        public function goToPreviousWizardStep(?string $schema = null): static {}

        public function assertWizardStepExists(int $step, ?string $schema = null): static {}

        public function assertWizardCurrentStep(int $step, ?string $schema = null): static {}
    }

}
