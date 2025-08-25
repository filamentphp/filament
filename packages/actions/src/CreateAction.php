<?php

namespace Filament\Actions;

use Closure;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\View\ActionsIconAlias;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOneOrManyThrough;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;

class CreateAction extends Action
{
    use CanCustomizeProcess;

    protected bool | Closure $canCreateAnother = true;

    protected ?Closure $modifyCreateAnotherActionUsing = null;

    protected ?Closure $preserveFormDataWhenCreatingAnotherUsing = null;

    protected ?Closure $getRelationshipUsing = null;

    public static function getDefaultName(): ?string
    {
        return 'create';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(fn (): string => __('filament-actions::create.single.label', ['label' => $this->getModelLabel()]));

        $this->modalHeading(fn (): string => __('filament-actions::create.single.modal.heading', ['label' => $this->getTitleCaseModelLabel()]));

        $this->modalSubmitActionLabel(__('filament-actions::create.single.modal.actions.create.label'));

        $this->extraModalFooterActions(function (): array {
            return $this->canCreateAnother() ? [$this->getCreateAnotherAction()] : [];
        });

        $this->successNotificationTitle(__('filament-actions::create.single.notifications.created.title'));

        $this->groupedIcon(FilamentIcon::resolve(ActionsIconAlias::CREATE_ACTION_GROUPED) ?? Heroicon::Plus);

        $this->record(null);

        $this->action(function (array $arguments, Schema $schema): void {
            if ($arguments['another'] ?? false) {
                $preserveRawState = $this->evaluate($this->preserveFormDataWhenCreatingAnotherUsing, [
                    'data' => $schema->getRawState(),
                ]) ?? [];
            }

            $model = $this->getModel();

            $record = $this->process(function (array $data, HasActions & HasSchemas $livewire) use ($model): Model {
                $relationship = $this->getRelationship();

                $pivotData = [];

                if ($relationship instanceof BelongsToMany) {
                    $pivotColumns = $relationship->getPivotColumns();

                    $pivotData = Arr::only($data, $pivotColumns);
                    $data = Arr::except($data, $pivotColumns);
                }

                if ($translatableContentDriver = $livewire->makeFilamentTranslatableContentDriver()) {
                    $record = $translatableContentDriver->makeRecord($model, $data);
                } else {
                    $record = new $model;
                    $record->fill($data);
                }

                if (
                    (! $relationship) ||
                    ($relationship instanceof HasOneOrManyThrough)
                ) {
                    $record->save();

                    return $record;
                }

                if ($relationship instanceof BelongsToMany) {
                    $relationship->save($record, $pivotData);

                    return $record;
                }

                /** @phpstan-ignore-next-line */
                $relationship->save($record);

                return $record;
            });

            $this->record($record);
            $schema->model($record)->saveRelationships();

            if ($arguments['another'] ?? false) {
                $this->callAfter();
                $this->sendSuccessNotification();

                $this->record(null);

                // Ensure that the form record is anonymized so that relationships aren't loaded.
                $schema->model($model);

                $schema->fill();

                $schema->rawState([
                    ...$schema->getRawState(),
                    ...$preserveRawState ?? [],
                ]);

                $this->halt();

                return;
            }

            $this->success();
        });
    }

    /**
     * @param  array<string>  $fields
     */
    public function preserveFormDataWhenCreatingAnother(array | Closure | null $fields): static
    {
        $this->preserveFormDataWhenCreatingAnotherUsing = is_array($fields) ?
            fn (array $data): array => Arr::only($data, $fields) :
            $fields;

        return $this;
    }

    public function relationship(?Closure $relationship): static
    {
        $this->getRelationshipUsing = $relationship;

        return $this;
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

    public function createAnotherAction(Closure $callback): static
    {
        $this->modifyCreateAnotherActionUsing = $callback;

        return $this;
    }

    public function getCreateAnotherAction(): Action
    {
        $action = $this->makeModalSubmitAction('createAnother', arguments: ['another' => true])
            ->label(__('filament-actions::create.single.modal.actions.create_another.label'));

        return $this->evaluate($this->modifyCreateAnotherActionUsing, ['action' => $action]) ?? $action;
    }

    public function shouldClearRecordAfter(): bool
    {
        return true;
    }

    public function getRelationship(): Relation | Builder | null
    {
        return $this->evaluate($this->getRelationshipUsing) ?? $this->getTable()?->getRelationship() ?? $this->getHasActionsLivewire()?->getDefaultActionRelationship($this);
    }
}
