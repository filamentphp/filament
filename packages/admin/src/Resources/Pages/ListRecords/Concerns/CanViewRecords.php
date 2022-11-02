<?php

namespace Filament\Resources\Pages\ListRecords\Concerns;

use Filament\Tables;

trait CanViewRecords
{
    /**
     * @deprecated Actions are no longer pre-defined.
     */
    protected function hasViewAction(): bool
    {
        return true;
    }

    /**
     * @deprecated Actions are no longer pre-defined.
     */
    protected function getViewAction(): Tables\Actions\Action
    {
        return parent::getViewAction()
            ->mountUsing(fn () => $this->fillViewForm());
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
}
