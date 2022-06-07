<?php

namespace Filament\Resources\Pages\ListRecords\Concerns;

use Filament\Resources\Pages\Concerns\UsesResourceForm;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;

trait CanViewRecords
{
    use UsesResourceForm;

    protected function hasViewAction(): bool
    {
        return true;
    }

    protected function getViewAction(): Tables\Actions\Action
    {
        $resource = static::getResource();

        return parent::getViewAction()
            ->url(null)
            ->form($this->getViewFormSchema())
            ->mountUsing(fn () => $this->fillViewForm())
            ->authorize(fn (Model $record) => $resource::canView($record));
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
}
