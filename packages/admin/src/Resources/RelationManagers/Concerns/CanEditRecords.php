<?php

namespace Filament\Resources\RelationManagers\Concerns;

use Filament\Tables;
use Illuminate\Database\Eloquent\Model;

trait CanEditRecords
{
    protected function canEdit(Model $record): bool
    {
        return $this->can('update', $record);
    }

    protected function getEditFormSchema(): array
    {
        return $this->getResourceForm()->getSchema();
    }

    protected function fillEditForm(): void
    {
        $this->callHook('beforeFill');
        $this->callHook('beforeEditFill');

        $data = $this->getMountedTableActionRecord()->toArray();

        $data = $this->mutateFormDataBeforeFill($data);

        $this->getMountedTableActionForm()->fill($data);

        $this->callHook('afterFill');
        $this->callHook('afterEditFill');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data;
    }

    public function save(): void
    {
        $this->callHook('beforeValidate');
        $this->callHook('beforeEditValidate');

        $data = $this->getMountedTableActionForm()->getState();

        $this->callHook('afterValidate');
        $this->callHook('afterEditValidate');

        $data = $this->mutateFormDataBeforeSave($data);

        $this->callHook('beforeSave');

        $this->getMountedTableActionRecord()->update($data);

        $this->callHook('afterSave');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $data;
    }

    protected function getEditTableAction(): Tables\Actions\Action
    {
        return config('filament.layout.tables.actions.type')::make('edit')
            ->label(__('filament::resources/relation-managers/edit.action.label'))
            ->form($this->getEditFormSchema())
            ->mountUsing(fn () => $this->fillEditForm())
            ->modalButton(__('filament::resources/relation-managers/edit.action.modal.actions.save.label'))
            ->modalHeading(__('filament::resources/relation-managers/edit.action.modal.heading', ['label' => static::getRecordLabel()]))
            ->action(fn () => $this->save())
            ->icon('heroicon-o-pencil')
            ->hidden(fn (Model $record): bool => ! static::canEdit($record));
    }
}
