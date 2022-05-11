<?php

namespace Filament\Resources\Pages\ListRecords\Concerns;

use Filament\Resources\Pages\Concerns\UsesResourceForm;
use Filament\Tables\Actions\Modal\Actions\Action as ModalAction;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
            ->modalHeading(fn (Model $record) => __('filament::resources/pages/view-record.title', ['label' => $resource::hasRecordTitle() ? $resource::getRecordTitle($record) : Str::title($resource::getLabel())]))
            ->modalActions([
                ModalAction::make('cancel')
                    ->label(__('forms::components.actions.modal.buttons.cancel.label'))
                    ->cancel()
                    ->color('secondary'),
            ])
            ->action(function () {})
            ->hidden(fn (Model $record) => ! $resource::canView($record));
    }

    protected function getViewFormSchema(): array
    {
        return $this->getResourceForm(columns: 2)->getSchema();
    }

    protected function fillViewForm(): void
    {
        $this->callHook('beforeFill');

        $data = $this->getMountedTableActionRecord()->toArray();

        $this->getMountedTableActionForm()->fill($data)->disabled();

        $this->callHook('afterFill');
    }

}
