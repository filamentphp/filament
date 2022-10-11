<?php

namespace Filament\Resources\Pages\ListRecords\Concerns;

use Filament\Notifications\Notification;
use Filament\Pages\Actions\Action;
use Illuminate\Database\Eloquent\Model;

/**
 * @deprecated Deleting the Create page now opens the action in a modal.
 */
trait CanCreateRecords
{
    /**
     * @deprecated Use `->disableCreateAnother()` on the action instead.
     */
    protected static bool $canCreateAnother = true;

    /**
     * @deprecated Actions are no longer pre-defined.
     */
    protected function hasCreateAction(): bool
    {
        return static::getResource()::canCreate();
    }

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
     * @deprecated Actions are no longer pre-defined.
     */
    protected function getCreateAction(): Action
    {
        return parent::getCreateAction()
            ->mountUsing(fn () => $this->fillCreateForm())
            ->action(fn (array $arguments) => $this->create($arguments['another'] ?? false));
    }

    /**
     * @deprecated Use `->mountUsing()` on the action instead.
     */
    protected function fillCreateForm(): void
    {
        $this->callHook('beforeFill');
        $this->callHook('beforeCreateFill');

        $this->getMountedActionForm()->fill();

        $this->callHook('afterFill');
        $this->callHook('afterCreateFill');
    }

    /**
     * @deprecated Use `->action()` on the action instead.
     */
    public function create(bool $another = false): void
    {
        $form = $this->getMountedActionForm();

        $this->callHook('beforeValidate');
        $this->callHook('beforeCreateValidate');

        $data = $form->getState();

        $this->callHook('afterValidate');
        $this->callHook('afterCreateValidate');

        $data = $this->mutateFormDataBeforeCreate($data);

        $this->callHook('beforeCreate');

        $record = $this->handleRecordCreation($data);

        $form->model($record)->saveRelationships();

        $this->callHook('afterCreate');

        if ($another) {
            // Ensure that the form record is anonymized so that relationships aren't loaded.
            $form->model($record::class);

            $form->fill();
        }

        if (filled($this->getCreatedNotificationMessage())) {
            Notification::make()
                ->title($this->getCreatedNotificationMessage())
                ->success()
                ->send();
        }

        if ($another) {
            $this->getMountedAction()->halt();
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
        return $this->getModel()::create($data);
    }

    /**
     * @deprecated Use `->mutateFormDataUsing()` on the action instead.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $data;
    }
}
