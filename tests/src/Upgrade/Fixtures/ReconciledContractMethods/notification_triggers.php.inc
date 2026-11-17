<?php

namespace Filament\Tests\Upgrade\Fixtures\NotificationTriggers;

use Filament\Livewire\DatabaseNotifications as PanelDatabaseNotifications;
use Filament\Notifications\Livewire\DatabaseNotifications;
use Illuminate\Contracts\View\View;

class CustomNotifications extends DatabaseNotifications
{
    public function getTrigger(): ?View
    {
        return parent::getTrigger();
    }

    public function triggerCallback(): callable
    {
        return $this->getTrigger(...);
    }
}

trait RendersTrigger
{
    public function getTrigger(): ?View
    {
        return parent::getTrigger();
    }
}

class CustomPanelNotifications extends PanelDatabaseNotifications
{
    use RendersTrigger {
        getTrigger as triggerView;
    }
}

class ChildNotifications extends CustomNotifications
{
    public function getTrigger(): ?View
    {
        return parent::getTrigger();
    }
}

function triggerViews(DatabaseNotifications $standalone, ?PanelDatabaseNotifications $panel): array
{
    return [$standalone->getTrigger(), $panel?->getTrigger(), $standalone->getTrigger(...)];
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\NotificationTriggers;

use Filament\Livewire\DatabaseNotifications as PanelDatabaseNotifications;
use Filament\Notifications\Livewire\DatabaseNotifications;
use Illuminate\Contracts\View\View;

class CustomNotifications extends DatabaseNotifications
{
    public function renderTrigger(): ?View
    {
        return parent::renderTrigger();
    }

    public function triggerCallback(): callable
    {
        return $this->renderTrigger(...);
    }
}

trait RendersTrigger
{
    public function renderTrigger(): ?View
    {
        return parent::renderTrigger();
    }
}

class CustomPanelNotifications extends PanelDatabaseNotifications
{
    use RendersTrigger {
        renderTrigger as triggerView;
    }
}

class ChildNotifications extends CustomNotifications
{
    public function renderTrigger(): ?View
    {
        return parent::renderTrigger();
    }
}

function triggerViews(DatabaseNotifications $standalone, ?PanelDatabaseNotifications $panel): array
{
    return [$standalone->renderTrigger(), $panel?->renderTrigger(), $standalone->renderTrigger(...)];
}
