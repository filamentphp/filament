<?php

namespace Filament\Tables\Actions;

use Closure;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Forms\Form;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;

class CreateAction extends Action
{
    use CanCustomizeProcess;

    protected bool | Closure $canCreateAnother = true;

    public static function getDefaultName(): ?string
    {
        return 'create';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(fn (): string => __('filament-actions::create.single.label', ['label' => $this->getModelLabel()]));

        $this->modalHeading(fn (): string => __('filament-actions::create.single.modal.heading', ['label' => $this->getModelLabel()]));

        $this->modalSubmitActionLabel(__('filament-actions::create.single.modal.actions.create.label'));

        $this->extraModalActions(function (): array {
            return $this->canCreateAnother() ? [
                $this->makeExtraModalAction('createAnother', ['another' => true])
                    ->label(__('filament-actions::create.single.modal.actions.create_another.label')),
            ] : [];
        });

        $this->successNotificationTitle(__('filament-actions::create.single.messages.created'));

        $this->action(function (array $arguments, Form $form, HasTable $livewire): void {
            $model = $this->getModel();

            $record = $this->process(function (array $data, Table $table) use ($model): Model {
                $relationship = $table->getRelationship();

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

    public function createAnother(bool | Closure $condition = true): static
    {
        $this->canCreateAnother = $condition;

        return $this;
    }

    /**
     * @deprecated Use `createAnother()` instead.
     */
    public function disableCreateAnother(bool | Closure $condition = true): static
    {
        $this->createAnother(fn (CreateAction $action): bool => ! $action->evaluate($condition));

        return $this;
    }

    public function canCreateAnother(): bool
    {
        return (bool) $this->evaluate($this->canCreateAnother);
    }
}
