<?php

namespace Filament\Resources\RelationManagers\Concerns;

use Filament\Tables;
use Illuminate\Database\Eloquent\Model;

trait CanViewRecords
{
    /**
     * @deprecated Actions are no longer pre-defined.
     */
    protected static bool $hasViewAction = false;

    /**
     * @deprecated Actions are no longer pre-defined.
     */
    protected function hasViewAction(): bool
    {
        return static::$hasViewAction;
    }

    protected function canView(Model $record): bool
    {
        return $this->hasViewAction() && $this->can('view', $record);
    }

    /**
     * @deprecated Use `->mountUsing()` on the action instead.
     */
    protected function fillViewForm(): void
    {
        $this->callHook('beforeFill');
        $this->callHook('beforeViewFill');

        $data = $this->getMountedTableActionRecord()->attributesToArray();

        $this->getMountedTableActionForm()->fill($data);

        $this->callHook('afterFill');
        $this->callHook('afterViewFill');
    }

    /**
     * @deprecated Actions are no longer pre-defined.
     */
    protected function getViewAction(): Tables\Actions\Action
    {
        return Tables\Actions\ViewAction::make()
            ->mountUsing(fn () => $this->fillViewForm());
    }
}
