<?php

namespace Filament\Actions;

use Closure;
use Filament\Actions\Contracts\HasLivewire;
use Filament\Notifications\Notification;
use Filament\Schema\Components\Actions\ActionContainer;
use Filament\Schema\Components\Actions\ActionContainer as InfolistActionContainer;
use Filament\Support\Exceptions\Cancel;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Js;

class Action extends StaticAction implements Contracts\HasRecord, HasLivewire
{
    use Concerns\BelongsToLivewire;
    use Concerns\BelongsToSchemaComponent;
    use Concerns\BelongsToTable;
    use Concerns\CanAccessSelectedRecords;
    use Concerns\CanBeMounted;
    use Concerns\CanDeselectRecordsAfterCompletion;
    use Concerns\CanFetchSelectedRecords;
    use Concerns\CanNotify;
    use Concerns\CanOpenModal;
    use Concerns\CanRedirect;
    use Concerns\CanRequireConfirmation;
    use Concerns\CanSubmitForm;
    use Concerns\CanUseDatabaseTransactions;
    use Concerns\HasForm;
    use Concerns\HasInfolist;
    use Concerns\HasLifecycleHooks;
    use Concerns\HasMountableArguments;
    use Concerns\HasParentActions;
    use Concerns\HasSchema;
    use Concerns\HasWizard;
    use Concerns\InteractsWithRecord;

    protected bool | Closure $isBulk = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->defaultView(static::BUTTON_VIEW);

        $this->failureNotification(fn (Notification $notification): Notification => $notification);
        $this->successNotification(fn (Notification $notification): Notification => $notification);
    }

    public function getLivewireCallMountedActionName(): string
    {
        return 'callMountedAction';
    }

    public function getLivewireClickHandler(): ?string
    {
        if (! $this->isLivewireClickHandlerEnabled()) {
            return null;
        }

        if (is_string($this->action)) {
            return $this->action;
        }

        if ($event = $this->getLivewireEventClickHandler()) {
            return $event;
        }

        if ($this->canAccessSelectedRecords()) {
            return null;
        }

        return $this->getJavaScriptClickHandler();
    }

    protected function getJavaScriptClickHandler(): string
    {
        $argumentsParameter = '';

        if (count($arguments = $this->getArguments())) {
            $argumentsParameter .= ', ';
            $argumentsParameter .= Js::from($arguments);
        }

        $context = [];

        $table = $this->getTable();

        if ($record = $this->getRecord()) {
            $context['recordKey'] = $table?->getRecordKey($record) ?? $record->getKey();
        }

        if (filled($componentKey = $this->getSchemaComponent()?->getKey())) {
            $context['schemaComponent'] = $componentKey;
        }

        if ($table) {
            $context['table'] = true;
        }

        if ($table && $this->isBulk()) {
            $context['bulk'] = true;
        }

        $contextParameter = '';

        if (filled($context)) {
            $contextParameter .= ', ';
            $contextParameter .= Js::from($context);

            if ($argumentsParameter === '') {
                $argumentsParameter = ', {}';
            }
        }

        return "mountAction('{$this->getName()}'{$argumentsParameter}{$contextParameter})";
    }

    public function getAlpineClickHandler(): ?string
    {
        if (filled($handler = parent::getAlpineClickHandler())) {
            return $handler;
        }

        if (! $this->canAccessSelectedRecords()) {
            return null;
        }

        return $this->getJavaScriptClickHandler();
    }

    public function getLivewireTarget(): ?string
    {
        if (filled($target = parent::getLivewireTarget())) {
            return $target;
        }

        if (! $this->canAccessSelectedRecords()) {
            return null;
        }

        return $this->getJavaScriptClickHandler();
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'arguments' => [$this->getArguments()],
            'component' => [$this->getSchemaComponent()],
            'context', 'operation' => [$this->getSchemaComponent()->getContainer()->getOperation()],
            'data' => [$this->getFormData()],
            'get' => [$this->getSchemaComponent()->makeGetUtility()],
            'livewire' => [$this->getLivewire()],
            'model' => [$this->getModel() ?? $this->getSchemaComponent()?->getModel()],
            'record' => [$this->getRecord() ?? $this->getSchemaComponent()?->getRecord()],
            'records' => [$this->getRecords()],
            'schemaComponent' => [$this->getSchemaComponent()],
            'selectedRecords' => [$this->getSelectedRecords()],
            'set' => [$this->getSchemaComponent()->makeSetUtility()],
            'state' => [$this->getSchemaComponent()->getState()],
            'table' => [$this->getTable()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByType(string $parameterType): array
    {
        $record = $this->getRecord() ?? $this->getSchemaComponent()?->getRecord();

        return match ($parameterType) {
            EloquentCollection::class, Collection::class => [$this->getRecords()],
            Model::class, $record ? $record::class : null => [$record],
            default => parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType),
        };
    }

    public function shouldClearRecordAfter(): bool
    {
        return ! $this->getRecord()?->exists;
    }

    public function clearRecordAfter(): void
    {
        if (! $this->shouldClearRecordAfter()) {
            return;
        }

        $this->record(null);
    }

    public function toFormComponent(): ActionContainer
    {
        $component = ActionContainer::make($this);

        $this->schemaComponent($component);

        return $component;
    }

    public function toInfolistComponent(): InfolistActionContainer
    {
        $component = InfolistActionContainer::make($this);

        $this->schemaComponent($component);

        return $component;
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function call(array $parameters = []): mixed
    {
        try {
            return $this->evaluate($this->getActionFunction(), $parameters);
        } finally {
            if ($this->shouldDeselectRecordsAfterCompletion()) {
                $this->getLivewire()->deselectAllTableRecords();
            }
        }
    }

    public function cancel(bool $shouldRollBackDatabaseTransaction = false): void
    {
        throw (new Cancel())->rollBackDatabaseTransaction($shouldRollBackDatabaseTransaction);
    }

    public function halt(bool $shouldRollBackDatabaseTransaction = false): void
    {
        throw (new Halt())->rollBackDatabaseTransaction($shouldRollBackDatabaseTransaction);
    }

    /**
     * @deprecated Use `halt()` instead.
     */
    public function hold(): void
    {
        $this->halt();
    }

    public function success(): void
    {
        $this->sendSuccessNotification();
        $this->dispatchSuccessRedirect();
    }

    public function failure(): void
    {
        $this->sendFailureNotification();
        $this->dispatchFailureRedirect();
    }

    public function bulk(bool | Closure $condition = true): static
    {
        $this->isBulk = $condition;

        return $this;
    }

    public function isBulk(): bool
    {
        return (bool) $this->evaluate($this->isBulk);
    }
}
