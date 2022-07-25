<?php

namespace Livewire\Testing {

    use Filament\Notifications\Notification;

    class TestableLivewire {
        public function assertNotified(Notification | string $notification = null): static {}
    }

}
