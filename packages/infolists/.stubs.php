<?php

namespace Livewire\Features\SupportTesting {

    class Testable {
        public function mountInfolistAction(string $component, string | array $name, string $infolistName = 'infolist'): static {}

        public function unmountInfolistAction(): static {}

        public function setInfolistActionData(array $data): static {}

        public function assertInfolistActionDataSet(array $data): static {}

        public function callInfolistAction(string $component, string | array $name, array $data = [], array $arguments = [], string $infolistName = 'infolist'): static {}

        public function callMountedInfolistAction(array $arguments = []): static {}

        public function assertInfolistActionExists(string $component, string | array $name, string $infolistName = 'infolist'): static {}

        public function assertInfolistActionDoesNotExist(string $component, string | array $name, string $infolistName = 'infolist'): static {}

        public function assertInfolistActionVisible(string $component, string | array $name, string $infolistName = 'infolist'): static {}

        public function assertInfolistActionHidden(string $component, string | array $name, string $infolistName = 'infolist'): static {}

        public function assertInfolistActionEnabled(string $component, string | array $name, string $infolistName = 'infolist'): static {}

        public function assertInfolistActionDisabled(string $component, string | array $name, string $infolistName = 'infolist'): static {}

        public function assertInfolistActionMounted(string $component, string | array $name, string $infolistName = 'infolist'): static {}

        public function assertInfolistActionNotMounted(string $component, string | array $name, string $infolistName = 'infolist'): static {}

        public function assertInfolistActionHalted(string $component, string | array $name, string $infolistName = 'infolist'): static {}

        public function assertHasInfolistActionErrors(array $keys = []): static {}

        public function assertHasNoInfolistActionErrors(array $keys = []): static {}

        public function assertInfolistActionHasIcon(string $component, string | array $name, string $icon, string $infolistName = 'infolist'): static {}

        public function assertInfolistActionDoesNotHaveIcon(string $component, string | array $name, string $icon, string $infolistName = 'infolist'): static {}

        public function assertInfolistActionHasLabel(string $component, string | array $name, string $label, string $infolistName = 'infolist'): static {}

        public function assertInfolistActionDoesNotHaveLabel(string $component, string | array $name, string $label, string $infolistName = 'infolist'): static {}

        public function assertInfolistActionHasColor(string $component, string | array $name, string | array $color, string $infolistName = 'infolist'): static {}

        public function assertInfolistActionDoesNotHaveColor(string $component, string | array $name, string | array $color, string $infolistName = 'infolist'): static {}

        public function assertInfolistActionHasUrl(string $component, string | array $name, string $url, string $infolistName = 'infolist'): static {}

        public function assertInfolistActionDoesNotHaveUrl(string $component, string | array $name, string $url, string $infolistName = 'infolist'): static {}

        public function assertInfolistActionShouldOpenUrlInNewTab(string $component, string | array $name, string $infolistName = 'infolist'): static {}

        public function assertInfolistActionShouldNotOpenUrlInNewTab(string $component, string | array $name, string $infolistName = 'infolist'): static {}

        public function getNestedInfolistActionComponentAndName(string $component, string | array $name, string $infolistName = 'infolist'): static {}

        public function parseNestedInfolistActionComponentAndName(string $component, string | array $name, string $infolistName = 'infolist'): static {}
    }

}
