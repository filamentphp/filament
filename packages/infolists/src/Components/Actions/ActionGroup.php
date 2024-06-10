<?php

namespace Filament\Infolists\Components\Actions;

use Filament\Actions\ActionGroup as BaseActionGroup;

class ActionGroup extends BaseActionGroup
{
    use Concerns\BelongsToInfolist;

    public function toInfolistComponent(): ActionContainer
    {
        return ActionContainer::make($this);
    }
}
