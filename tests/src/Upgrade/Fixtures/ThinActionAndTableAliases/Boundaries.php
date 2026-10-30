<?php

namespace Filament\Tests\Upgrade\Fixtures\ThinActionAndTableAliases;

use Filament\Tables\Concerns\HasColumnManager;

class GenuineCustomParent extends Owner
{
    public function updatedToggledTableColumns(): void {}
}

class GenuineCustomChild extends GenuineCustomParent
{
    public function updatedToggledTableColumns(): void
    {
        parent::updatedToggledTableColumns();
    }
}

class AdaptedCanonicalAlias
{
    use HasColumnManager {
        applyTableColumnManager as updatedToggledTableColumns;
    }

    public function update(): void
    {
        $this->updatedToggledTableColumns(['consumed by the application alias']);
    }
}

function normalizedUnion(Owner | GenuineCustomParent $owner): void
{
    $owner->updatedToggledTableColumns();
}

function dynamicCall(Owner $owner, string $method): void
{
    $owner->{$method}();
}

function protectedExternal(Owner $owner): void
{
    $owner->getMountedActionForm();
    $owner->getToggledTableColumnsSessionKey();
}
