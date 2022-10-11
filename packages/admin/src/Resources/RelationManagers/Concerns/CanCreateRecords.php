<?php

namespace Filament\Resources\RelationManagers\Concerns;

use Filament\Notifications\Notification;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;

trait CanCreateRecords
{
    /**
     * @deprecated Use `->disableCreateAnother()` on the action instead.
     */
    protected static bool $canCreateAnother = true;

    /**
     * @deprecated Use `->disableCreateAnother()` on the action instead.
     */
    protected static function canCreateAnother(): bool
    {
        return static::$canCreateAnother;
    }

    /**
     * @deprecated Use `->disableCreateAnother()` on the action instead.
     */
    public static function disableCreateAnother(): void
    {
        static::$canCreateAnother = false;
    }

    /**
     * @deprecated Use `->mountUsing()` on the action instead.
     */
    protected function fillCreateForm(): void
    {
        $this->callHook('beforeFill');
        $this->callHook('beforeCreateFill');

        $this->getMountedTableActionForm()->fill();

        $this->callHook('afterFill');
        $this->callHook('afterCreateFill');
    }

    /**
     * @deprecated Use `->action()` on the action instead.
     */
    public function create(bool $another = false): void
    {
        $form = $this->getMountedTableActionForm();

        $this->callHook('beforeValidate');
        $this->callHook('beforeCreateValidate');

        $data = $form->getState();

        $this->callHook('afterValidate');
        $this->callHook('afterCreateValidate');

        $data = $this->mutateFormDataBeforeCreate($data);

        $this->callHook('beforeCreate');

        $record = $this->handleRecordCreation($data);

        $form->model($record)->saveRelationships();

        $this->mountedTableActionRecord($record->getKey());

        $this->callHook('afterCreate');

        if ($another) {
            // Ensure that the form record is anonymized so that relationships aren't loaded.
            $form->model($record::class);
            $this->mountedTableActionRecord(null);

            $form->fill();
        }

        if (filled($this->getCreatedNotificationMessage())) {
            Notification::make()
                ->title($this->getCreatedNotificationMessage())
                ->success()
                ->send();
        }

        if ($another) {
            $this->getMountedTableAction()->halt();
        }
    }

    /**
     * @deprecated Use `->successNotificationTitle()` on the action instead.
     */
    protected function getCreatedNotificationMessage(): ?string
    {
        return __('filament-support::actions/create.single.messages.created');
    }

    /**
     * @deprecated Use `->action()` on the action instead.
     */
    public function createAnother(): void
    {
        $this->create(another: true);
    }

    /**
     * @deprecated Use `->using()` on the action instead.
     */
    protected function handleRecordCreation(array $data): Model
    {
        return $this->getRelationship()->create($data);
    }

    /**
     * @deprecated Use `->mutateFormDataUsing()` on the action instead.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $data;
    }

    /**
     * @deprecated Actions are no longer pre-defined.
     */
    protected function getCreateAction(): Tables\Actions\Action
    {
        return Tables\Actions\CreateAction::make()
            ->mountUsing(fn () => $this->fillCreateForm())
            ->action(fn (array $arguments) => $this->create($arguments['another'] ?? false));
    }
}
