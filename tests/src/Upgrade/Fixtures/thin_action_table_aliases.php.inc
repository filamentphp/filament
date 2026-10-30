<?php

namespace ThinAliases;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Tables\Concerns\CanGroupRecords;
use Filament\Tables\Concerns\HasColumnManager;

class Owner
{
    use InteractsWithActions;
    use CanGroupRecords;
    use HasColumnManager;

    public function fluent(): static { return $this; }

    public function inspect(?Action $action): void
    {
        $this->getMountedActionForm();
        $this->getMountedActionForm(mountedAction: $action, actionNestingIndex: 1);
        $this->getToggledTableColumnsSessionKey();
    }
}

function calls(Owner $owner, ?Owner $nullable, object $unrelated): void
{
    $owner->fluent()->mountedActionHasForm();
    $nullable?->mountedActionHasForm(mountedAction: null);
    $owner->updatedToggledTableColumns();
    $nullable?->updatedTableGroupColumn();
    $unrelated->mountedActionHasForm();
    $owner->updatedToggledTableColumns(['extra']);
    $owner->mountedActionHasForm(...$arguments);
    $callback = $owner->mountedActionHasForm(...);
}
-----
<?php

namespace ThinAliases;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Tables\Concerns\CanGroupRecords;
use Filament\Tables\Concerns\HasColumnManager;

class Owner
{
    use InteractsWithActions;
    use CanGroupRecords;
    use HasColumnManager;

    public function fluent(): static { return $this; }

    public function inspect(?Action $action): void
    {
        $this->getMountedActionSchema();
        $this->getMountedActionSchema(mountedAction: $action, actionNestingIndex: 1);
        $this->getTableColumnsSessionKey();
    }
}

function calls(Owner $owner, ?Owner $nullable, object $unrelated): void
{
    $owner->fluent()->mountedActionHasSchema();
    $nullable?->mountedActionHasSchema(mountedAction: null);
    $owner->applyTableColumnManager();
    $nullable?->updatedTableGrouping();
    $unrelated->mountedActionHasForm();
    $owner->updatedToggledTableColumns(['extra']);
    $owner->mountedActionHasForm(...$arguments);
    $callback = $owner->mountedActionHasForm(...);
}
