<?php

namespace Filament\Tables\Actions;

use Closure;
use Filament\Forms\ComponentContainer;
use Filament\Support\Actions\Concerns\CanCustomizeProcess;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;

class CreateAction extends Action
{
    use CanCustomizeProcess;
    use Concerns\InteractsWithRelationship;

    protected bool | Closure $isCreateAnotherDisabled = false;

    public static function getDefaultName(): ?string
    {
        return 'create';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(fn (): string => __('filament-support::actions/create.single.label', ['label' => $this->getModelLabel()]));

        $this->modalHeading(fn (): string => __('filament-support::actions/create.single.modal.heading', ['label' => $this->getModelLabel()]));

        $this->modalButton(__('filament-support::actions/create.single.modal.actions.create.label'));

        $this->extraModalActions(function (): array {
            return $this->isCreateAnotherDisabled() ? [] : [
                $this->makeExtraModalAction('createAnother', ['another' => true])
                    ->label(__('filament-support::actions/create.single.modal.actions.create_another.label')),
            ];
        });

        $this->successNotificationTitle(__('filament-support::actions/create.single.messages.created'));

        $this->button();

        $this->action(function (array $arguments, ComponentContainer $form, HasTable $livewire): void {
            $model = $this->getModel();

            $record = $this->process(function (array $data) use ($model): Model {
                $relationship = $this->getRelationship();

                if (! $relationship) {
                    return $model::create($data);
                }

                if ($relationship instanceof BelongsToMany) {
                    $pivotColumns = $relationship->getPivotColumns();

                    return $relationship->create(
                        Arr::except($data, $pivotColumns),
                        Arr::only($data, $pivotColumns),
                    );
                }

                return $relationship->create($data);
            });

            $this->record($record);
            $form->model($record)->saveRelationships();

            $livewire->mountedTableActionRecord($record->getKey());

            if ($arguments['another'] ?? false) {
                $this->callAfter();
                $this->sendSuccessNotification();

                $this->record(null);

                // Ensure that the form record is anonymized so that relationships aren't loaded.
                $form->model($model);
                $livewire->mountedTableActionRecord(null);

                $form->fill();

                $this->halt();

                return;
            }

            $this->success();
        });
    }

    public function disableCreateAnother(bool | Closure $condition = true): static
    {
        $this->isCreateAnotherDisabled = $condition;

        return $this;
    }

    public function isCreateAnotherDisabled(): bool
    {
        return $this->evaluate($this->isCreateAnotherDisabled);
    }
}
