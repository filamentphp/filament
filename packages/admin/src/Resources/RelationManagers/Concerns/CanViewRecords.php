<?php

namespace Filament\Resources\RelationManagers\Concerns;

use Filament\Tables;
use Illuminate\Database\Eloquent\Model;

trait CanViewRecords
{
    protected static bool $hasViewAction = false;

    protected function hasViewAction(): bool
    {
        return static::$hasViewAction;
    }

    protected function canView(Model $record): bool
    {
        return $this->hasViewAction() && $this->can('view', $record);
    }

    protected function getViewFormSchema(): array
    {
        return $this->getResourceForm(columns: 2, isDisabled: true)->getSchema();
    }

    /**
     * @deprecated Use `->mountUsing()` on the action instead.
     */
    protected function fillViewForm(): void
    {
        $this->callHook('beforeFill');
        $this->callHook('beforeViewFill');

        $data = $this->getMountedTableActionRecord()->toArray();

        $this->getMountedTableActionForm()->fill($data);

        $this->callHook('afterFill');
        $this->callHook('afterViewFill');
    }

    protected function getViewAction(): Tables\Actions\Action
    {
        return Tables\Actions\ViewAction::make()
            ->form($this->getViewFormSchema())
            ->mountUsing(fn () => $this->fillViewForm())
            ->authorize(fn (Model $record): bool => static::canView($record));
    }
}
