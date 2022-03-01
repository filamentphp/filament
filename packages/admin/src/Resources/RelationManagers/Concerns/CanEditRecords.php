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

        $this->handleRecordUpdate($this->getMountedTableActionRecord(), $data);

        $this->callHook('afterSave');

        if (filled($this->getSavedNotificationMessage())) {
            $this->notify('success', $this->getSavedNotificationMessage());
        }
    }

    protected function getSavedNotificationMessage(): ?string
    {
        return __('filament::resources/relation-managers/edit.action.messages.saved');
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        return $record;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $data;
    }

    protected function getEditAction(): Tables\Actions\Action
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
