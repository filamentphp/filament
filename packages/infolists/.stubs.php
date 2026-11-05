<?php

namespace Livewire\Features\SupportTesting {

    class Testable {
        /**
         * @deprecated Use `mountAction()` instead.
         */
        public function mountInfolistAction(string $component, string | array $actions, string $schema = 'infolist'): static {}

        /**
         * @deprecated Use `unmountAction()` instead.
         */
        public function unmountInfolistAction(): static {}

        /**
         * @deprecated Use `callAction()` instead.
         */
        public function callInfolistAction(string $component, string | array $actions, array $data = [], array $arguments = [], string $schema = 'infolist'): static {}

        /**
         * @deprecated Use `callMountedAction()` instead.
         */
        public function callMountedInfolistAction(array $arguments = []): static {}
    }

}
