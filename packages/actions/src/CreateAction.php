<?php

namespace Filament\Actions;

use Closure;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Model;

class CreateAction extends Action
{
    use CanCustomizeProcess;

    protected bool | Closure $isCreateAnotherDisabled = false;

    public static function getDefaultName(): ?string
    {
        return 'create';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(fn (): string => __('filament-actions::create.single.label', ['label' => $this->getModelLabel()]));

        $this->modalHeading(fn (): string => __('filament-actions::create.single.modal.heading', ['label' => $this->getModelLabel()]));

        $this->modalButton(__('filament-actions::create.single.modal.actions.create.label'));

        $this->extraModalActions(function (): array {
            return $this->isCreateAnotherDisabled() ? [] : [
                $this->makeExtraModalAction('createAnother', ['another' => true])
                    ->label(__('filament-actions::create.single.modal.actions.create_another.label')),
            ];
        });

        $this->successNotificationTitle(__('filament-actions::create.single.messages.created'));

        $this->button();

        $this->groupedIcon('heroicon-m-plus');

        $this->action(function (array $arguments, Form $form): void {
            $model = $this->getModel();

            $record = $this->process(fn (array $data): Model => $model::create($data));

            $this->record($record);
            $form->model($record)->saveRelationships();

            if ($arguments['another'] ?? false) {
                $this->callAfter();
                $this->sendSuccessNotification();

                $this->record(null);

                // Ensure that the form record is anonymized so that relationships aren't loaded.
                $form->model($model);

                $form->fill();

                $this->halt();

                return;
            }

            $this->success();

            $this->record(null);
        });
    }

    public function disableCreateAnother(bool | Closure $condition = true): static
    {
        $this->isCreateAnotherDisabled = $condition;

        return $this;
    }

    public function isCreateAnotherDisabled(): bool
    {
        return (bool) $this->evaluate($this->isCreateAnotherDisabled);
    }
}
