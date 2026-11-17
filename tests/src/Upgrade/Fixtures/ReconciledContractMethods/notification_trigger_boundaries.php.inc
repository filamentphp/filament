<?php

namespace Filament\Tests\Upgrade\Fixtures\NotificationTriggerBoundaries;

use Filament\Notifications\Livewire\DatabaseNotifications;
use Illuminate\Contracts\View\View;

class ParentNotifications extends DatabaseNotifications
{
    public function getTrigger(): ?View
    {
        return parent::getTrigger();
    }
}

class ConflictingNotifications extends ParentNotifications
{
    public function renderTrigger(): ?View
    {
        return null;
    }
}

trait SharedTrigger
{
    public function getTrigger(): ?View
    {
        return null;
    }

    public function triggerView(): ?View
    {
        return $this->getTrigger();
    }
}

class SharedNotifications extends DatabaseNotifications
{
    use SharedTrigger;
}

class UnrelatedTrigger
{
    use SharedTrigger;
}

function triggerViews(ParentNotifications $conflicting, SharedNotifications $shared, UnrelatedTrigger $unrelated): array
{
    return [$conflicting->getTrigger(), $shared->getTrigger(), $unrelated->getTrigger(), $unrelated->getTrigger(...)];
}
