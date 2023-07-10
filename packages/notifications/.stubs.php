<?php

namespace Livewire\Features\SupportTesting\Tests {

    use Filament\Notifications\Notification;

    class Testable {
        public function assertNotified(Notification | string $notification = null): static {}
    }

}
