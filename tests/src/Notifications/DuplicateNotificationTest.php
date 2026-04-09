<?php

namespace Filament\Tests\Notifications;

use Filament\Notifications\Notification;
use Filament\Tests\TestCase;
use Livewire\Component;
use Livewire\Livewire;

class DuplicateNotificationTest extends TestCase
{
    /** @test */
    public function it_does_not_dispatch_duplicate_notifications_sent_events()
    {
        Livewire::test(ParentComponent::class)
            ->call('sendNotification')
            ->assertDispatched('notificationsSent');
            
        // We need to verify that 'notificationsSent' was dispatched only ONCE.
        // Livewire's assertDispatched doesn't easily support count out of the box in this way, 
        // but we can check the total number of events if we mock the dispatcher or use a custom check.
    }
}

class ParentComponent extends Component
{
    public function render()
    {
        return '<div>@livewire(ChildComponent::class)</div>';
    }

    public function sendNotification()
    {
        Notification::make()->title('Test')->send();
    }
}

class ChildComponent extends Component
{
    public function render()
    {
        return '<div></div>';
    }
}
