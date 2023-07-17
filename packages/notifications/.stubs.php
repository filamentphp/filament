<?php

namespace Livewire\Features\SupportTesting {

    use Filament\Notifications\Notification;

    class Testable {
        public function assertNotified(Notification | string $notification = null): static {}
    }

}
