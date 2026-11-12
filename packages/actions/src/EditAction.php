<?php

namespace Filament\Actions;

use Closure;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\View\ActionsIconAlias;
use Filament\Forms\EditConflictHandler;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;
use LogicException;
use Throwable;

class EditAction extends Action
{
    use CanCustomizeProcess;

    protected ?Closure $mutateRecordDataUsing = null;

    protected EditConflictHandler | Closure | bool | null $editConflictHandler = null;

    protected ?int $editTransactionLevel = null;

    protected ?Connection $editTransactionConnection = null;

    protected ?EditConflictHandler $activeEditConflictHandler = null;

    protected bool $isUsingEditConflictTransaction = false;

    public static function getDefaultName(): ?string
    {
        return 'edit';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-actions::edit.single.label'));

        $this->modalHeading(fn (): string => __('filament-actions::edit.single.modal.heading', ['label' => $this->getRecordTitle()]));

        $this->modalSubmitActionLabel(__('filament-actions::edit.single.modal.actions.save.label'));

        $this->successNotificationTitle(__('filament-actions::edit.single.notifications.saved.title'));

        $this->defaultColor('primary');

        $this->tableIcon(FilamentIcon::resolve(ActionsIconAlias::EDIT_ACTION) ?? Heroicon::PencilSquare);
        $this->groupedIcon(FilamentIcon::resolve(ActionsIconAlias::EDIT_ACTION_GROUPED) ?? Heroicon::PencilSquare);

        $this->fillForm(function (HasActions & HasSchemas $livewire, Model $record, ?Table $table): array {
            $translatableContentDriver = $livewire->makeFilamentTranslatableContentDriver();

            if ($translatableContentDriver) {
                $data = $translatableContentDriver->recordAttributesToArray($record);
            } else {
                $data = $record->attributesToArray();
            }

            $relationship = $table?->getRelationship();

            if ($relationship instanceof BelongsToMany) {
                $pivot = $record->getRelationValue($relationship->getPivotAccessor());

                $pivotColumns = $relationship->getPivotColumns();

                if ($translatableContentDriver) {
                    $data = [
                        ...$data,
                        ...Arr::only($translatableContentDriver->recordAttributesToArray($pivot), $pivotColumns),
                    ];
                } else {
                    $data = [
                        ...$data,
                        ...Arr::only($pivot->attributesToArray(), $pivotColumns),
                    ];
                }
            }

            if ($this->mutateRecordDataUsing) {
                $data = $this->evaluate($this->mutateRecordDataUsing, ['data' => $data]);
            }

            return $data;
        });

        $this->action(function (): void {
            $this->process(function (array $data, HasActions & HasSchemas $livewire, Model $record, ?Table $table): void {
                $relationship = $table?->getRelationship();

                $translatableContentDriver = $livewire->makeFilamentTranslatableContentDriver();

                if ($relationship instanceof BelongsToMany) {
                    $pivot = $record->getRelationValue($relationship->getPivotAccessor());

                    $pivotColumns = $relationship->getPivotColumns();
                    $pivotData = Arr::only($data, $pivotColumns);

                    if (count($pivotColumns)) {
                        if ($translatableContentDriver) {
                            $translatableContentDriver->updateRecord($pivot, $pivotData);
                        } else {
                            $pivot->update($pivotData);
                        }
                    }

                    $data = Arr::except($data, $pivotColumns);
                }

                if ($translatableContentDriver) {
                    $translatableContentDriver->updateRecord($record, $data);
                } else {
                    $record->update($data);
                }
            });

            $this->success();
        });
    }

    public function mutateRecordDataUsing(?Closure $callback): static
    {
        $this->mutateRecordDataUsing = $callback;

        return $this;
    }

    public function editConflictHandler(EditConflictHandler | Closure | bool | null $handler = true): static
    {
        $this->editConflictHandler = $handler;

        return $this;
    }

    public function getEditConflictHandler(): ?EditConflictHandler
    {
        if ($this->editTransactionConnection !== null) {
            return $this->activeEditConflictHandler;
        }

        $handler = $this->evaluate($this->editConflictHandler);

        return $handler === true ? EditConflictHandler::make() : ($handler ?: null);
    }

    public function getModalAction(string $name): ?Action
    {
        if (($name === 'resolveFormConflicts') && $this->getEditConflictHandler()) {
            return $this->prepareModalAction($this->getLivewire()->getActionEditConflictHandler($this)->getAction());
        }

        return parent::getModalAction($name);
    }

    public function boot(): void
    {
        if (isset($this->getLivewire()->actionFormConflictStates[$this->getNestingIndex()])) {
            abort_unless($this->getRecord() instanceof Model, 404);
        }

        parent::boot();
    }

    public function beginDatabaseTransaction(): void
    {
        $this->isUsingEditConflictTransaction = false;

        if (! $this->getEditConflictHandler()) {
            parent::beginDatabaseTransaction();

            return;
        }

        $this->beginEditConflictTransaction();

        if (! $this->getLivewire()->checkActionFormConflicts($this)) {
            throw (new Halt)->rollBackDatabaseTransaction();
        }
    }

    /** @internal Recovery uses the same lock without attempting a save. */
    public function beginEditConflictTransaction(): void
    {
        $this->activeEditConflictHandler = $this->getEditConflictHandler();
        $this->isUsingEditConflictTransaction = true;

        if ($this->fillFormUsing === null) {
            throw new LogicException('Edit conflict protection requires a repeatable fillForm() mapping. Custom mountUsing() callbacks cannot be replayed safely.');
        }

        $record = $this->getRecord();
        abort_unless($record instanceof Model, 404);

        $connection = $record->getConnection();
        $this->editTransactionConnection = $connection;
        $this->editTransactionLevel = $connection->transactionLevel();
        $connection->beginTransaction();
        $lockedRecord = $this->activeEditConflictHandler->lockRecord($record);

        $this->getLivewire()->authorizeActionFormConflictRecord($this, $lockedRecord);
    }

    public function commitDatabaseTransaction(): void
    {
        if (! $this->isUsingEditConflictTransaction) {
            parent::commitDatabaseTransaction();

            return;
        }

        if ($this->editTransactionConnection === null) {
            return;
        }

        try {
            $this->editTransactionConnection->commit();
        } catch (Throwable $exception) {
            $this->rollBackDatabaseTransaction();

            throw $exception;
        }

        $this->editTransactionLevel = null;
        $this->editTransactionConnection = null;
        $this->activeEditConflictHandler = null;
    }

    public function rollBackDatabaseTransaction(): void
    {
        if (! $this->isUsingEditConflictTransaction) {
            parent::rollBackDatabaseTransaction();

            return;
        }

        if ($this->editTransactionConnection === null) {
            return;
        }

        $this->editTransactionConnection->rollBack($this->editTransactionLevel);
        $this->editTransactionLevel = null;
        $this->editTransactionConnection = null;
        $this->activeEditConflictHandler = null;
    }
}
