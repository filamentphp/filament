<?php

namespace Livewire\Features\SupportUnitTesting\Tests {

    use Filament\Notifications\Notification;

    class Testable {
        public function assertNotified(Notification | string $notification = null): static {}
    }

}
