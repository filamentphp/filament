<?php

namespace Livewire\Features\SupportTesting {

    use Closure;

    class Testable {
        /**
         * @deprecated Use `mountAction()` instead.
         */
        public function mountInfolistAction(string $component, string | array $name, string $infolistName = 'infolist'): static {}

        /**
         * @deprecated Use `unmountAction()` instead.
         */
        public function unmountInfolistAction(): static {}

        /**
         * @deprecated Use `fillForm()` instead.
         */
        public function setInfolistActionData(array $data): static {}

        /**
         * @deprecated Use `assertSchemaStateSet()` instead.
         */
        public function assertInfolistActionDataSet(array $data): static {}

        /**
         * @deprecated Use `callAction()` instead.
         */
        public function callInfolistAction(string $component, string | array $name, array $data = [], array $arguments = [], string $infolistName = 'infolist'): static {}

        /**
         * @deprecated Use `callMountedAction()` instead.
         */
        public function callMountedInfolistAction(array $arguments = []): static {}

        /**
         * @deprecated Use `assertActionExists()` instead.
         */
        public function assertInfolistActionExists(string $component, string | array $name, string $infolistName = 'infolist'): static {}

        /**
         * @deprecated Use `assertActionDoesNotExist()` instead.
         */
        public function assertInfolistActionDoesNotExist(string $component, string | array $name, string $infolistName = 'infolist'): static {}

        /**
         * @deprecated Use `assertActionVisible()` instead.
         */
        public function assertInfolistActionVisible(string $component, string | array $name, string $infolistName = 'infolist'): static {}

        /**
         * @deprecated Use `assertActionHidden()` instead.
         */
        public function assertInfolistActionHidden(string $component, string | array $name, string $infolistName = 'infolist'): static {}

        /**
         * @deprecated Use `assertActionEnabled()` instead.
         */
        public function assertInfolistActionEnabled(string $component, string | array $name, string $infolistName = 'infolist'): static {}

        /**
         * @deprecated Use `assertActionDisabled()` instead.
         */
        public function assertInfolistActionDisabled(string $component, string | array $name, string $infolistName = 'infolist'): static {}

        /**
         * @deprecated Use `assertActionMounted()` instead.
         */
        public function assertInfolistActionMounted(string $component, string | array $name, string $infolistName = 'infolist'): static {}

        /**
         * @deprecated Use `assertActionNotMounted()` instead.
         */
        public function assertInfolistActionNotMounted(string $component, string | array $name, string $infolistName = 'infolist'): static {}

        /**
         * @deprecated Use `assertActionHalted()` instead.
         */
        public function assertInfolistActionHalted(string $component, string | array $name, string $infolistName = 'infolist'): static {}

        /**
         * @deprecated Use `assertHasFormErrors()` instead.
         */
        public function assertHasInfolistActionErrors(array $keys = []): static {}

        /**
         * @deprecated Use `assertHasNoFormErrors()` instead.
         */
        public function assertHasNoInfolistActionErrors(array $keys = []): static {}

        /**
         * @deprecated Use `assertActionHasIcon()` instead.
         */
        public function assertInfolistActionHasIcon(string $component, string | array $name, string $icon, string $infolistName = 'infolist'): static {}

        /**
         * @deprecated Use `assertActionDoesNotHaveIcon()` instead.
         */
        public function assertInfolistActionDoesNotHaveIcon(string $component, string | array $name, string $icon, string $infolistName = 'infolist'): static {}

        /**
         * @deprecated Use `assertActionHasLabel()` instead.
         */
        public function assertInfolistActionHasLabel(string $component, string | array $name, string $label, string $infolistName = 'infolist'): static {}

        /**
         * @deprecated Use `assertActionDoesNotHaveLabel()` instead.
         */
        public function assertInfolistActionDoesNotHaveLabel(string $component, string | array $name, string $label, string $infolistName = 'infolist'): static {}

        /**
         * @deprecated Use `assertActionHasColor()` instead.
         */
        public function assertInfolistActionHasColor(string $component, string | array $name, string | array $color, string $infolistName = 'infolist'): static {}

        /**
         * @deprecated Use `assertActionDoesNotHaveColor()` instead.
         */
        public function assertInfolistActionDoesNotHaveColor(string $component, string | array $name, string | array $color, string $infolistName = 'infolist'): static {}

        /**
         * @deprecated Use `assertActionHasUrl()` instead.
         */
        public function assertInfolistActionHasUrl(string $component, string | array $name, string $url, string $infolistName = 'infolist'): static {}

        /**
         * @deprecated Use `assertActionDoesNotHaveUrl()` instead.
         */
        public function assertInfolistActionDoesNotHaveUrl(string $component, string | array $name, string $url, string $infolistName = 'infolist'): static {}

        /**
         * @deprecated Use `assertActionShouldOpenUrlInNewTab()` instead.
         */
        public function assertInfolistActionShouldOpenUrlInNewTab(string $component, string | array $name, string $infolistName = 'infolist'): static {}

        /**
         * @deprecated Use `assertActionShouldNotOpenUrlInNewTab()` instead.
         */
        public function assertInfolistActionShouldNotOpenUrlInNewTab(string $component, string | array $name, string $infolistName = 'infolist'): static {}
    }

}
