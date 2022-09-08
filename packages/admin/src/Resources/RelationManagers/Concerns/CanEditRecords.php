<?php

namespace Filament\Resources\RelationManagers\Concerns;

use Filament\Notifications\Notification;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;

trait CanEditRecords
{
    /**
     * @deprecated Use `->mountUsing()` on the action instead.
     */
    protected function fillEditForm(): void
    {
        $this->callHook('beforeFill');
        $this->callHook('beforeEditFill');

        $data = $this->getMountedTableActionRecord()->attributesToArray();

        $data = $this->mutateFormDataBeforeFill($data);

        $this->getMountedTableActionForm()->fill($data);

        $this->callHook('afterFill');
        $this->callHook('afterEditFill');
    }

    /**
     * @deprecated Use `->mutateRecordDataUsing()` on the action instead.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data;
    }

    /**
     * @deprecated Use `->action()` on the action instead.
     */
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
            Notification::make()
                ->title($this->getSavedNotificationMessage())
                ->success()
                ->send();
        }
    }

    /**
     * @deprecated Use `->successNotificationMessage()` on the action instead.
     */
    protected function getSavedNotificationMessage(): ?string
    {
        return __('filament-support::actions/edit.single.messages.saved');
    }

    /**
     * @deprecated Use `->using()` on the action instead.
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        return $record;
    }

    /**
     * @deprecated Use `->mutateFormDataUsing()` on the action instead.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $data;
    }

    /**
     * @deprecated Actions are no longer pre-defined.
     */
    protected function getEditAction(): Tables\Actions\Action
    {
        return Tables\Actions\EditAction::make()
            ->mountUsing(fn () => $this->fillEditForm())
            ->action(fn () => $this->save());
    }
}
