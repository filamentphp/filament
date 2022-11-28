<?php

namespace Filament\Tables\Actions;

use Filament\Actions\ActionGroup as BaseActionGroup;
use Filament\Actions\Concerns\InteractsWithRecord;

/**
 * @property array<Action | BulkAction> $actions
 */
class ActionGroup extends BaseActionGroup
{
    use InteractsWithRecord;

    /**
     * @return array<Action | BulkAction>
     */
    public function getActions(): array
    {
        $actions = [];

        foreach ($this->actions as $action) {
            $action->grouped();

            if (! $action instanceof BulkAction) {
                $action->record($this->getRecord());
            }

            $actions[$action->getName()] = $action;
        }

        return $actions;
    }
}
