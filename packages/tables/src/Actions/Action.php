<?php

namespace Filament\Tables\Actions;

use Filament\Actions\Concerns\HasMountableArguments;
use Filament\Actions\Concerns\InteractsWithRecord;
use Filament\Actions\Contracts\Groupable;
use Filament\Actions\Contracts\HasRecord;
use Filament\Actions\MountableAction;
use Filament\Actions\StaticAction;
use Filament\Tables\Actions\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;

class Action extends MountableAction implements Groupable, HasRecord, HasTable
{
    use Concerns\BelongsToTable;
    use Concerns\CanAccessSelectedRecords;
    use Concerns\CanDeselectRecordsAfterCompletion;
    use Concerns\CanFetchSelectedRecords;
    use HasMountableArguments;
    use InteractsWithRecord;

    public function getLivewireCallMountedActionName(): string
    {
        return 'callMountedTableAction';
    }

    public function getAlpineClickHandler(): ?string
    {
        if (filled($handler = parent::getAlpineClickHandler())) {
            return $handler;
        }

        if (! $this->canAccessSelectedRecords()) {
            return null;
        }

        return $this->generateJavaScriptClickHandler('mountAction');
    }

    public function getLivewireTarget(): ?string
    {
        if (filled($target = parent::getLivewireTarget())) {
            return $target;
        }

        if (! $this->canAccessSelectedRecords()) {
            return null;
        }

        return $this->generateJavaScriptClickHandler('mountTableAction');
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

        return $this->generateJavaScriptClickHandler('mountTableAction');
    }

    protected function generateJavaScriptClickHandler(string $method): string
    {
        if ($record = $this->getRecord()) {
            $recordKey = $this->getLivewire()->getTableRecordKey($record);

            return "{$method}('{$this->getName()}', '{$recordKey}')";
        }

        return "{$method}('{$this->getName()}')";
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'model' => [$this->getModel()],
            'record' => [$this->getRecord()],
            'selectedRecords' => [$this->getSelectedRecords()],
            'table' => [$this->getTable()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByType(string $parameterType): array
    {
        $record = $this->getRecord();

        if (! $record) {
            return parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType);
        }

        return match ($parameterType) {
            Model::class, $record::class => [$record],
            default => parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType),
        };
    }

    public function getRecordTitle(?Model $record = null): string
    {
        $record ??= $this->getRecord();

        return $this->getCustomRecordTitle($record) ?? $this->getTable()->getRecordTitle($record);
    }

    public function getRecordTitleAttribute(): ?string
    {
        return $this->getCustomRecordTitleAttribute() ?? $this->getTable()->getRecordTitleAttribute();
    }

    public function getModelLabel(): string
    {
        return $this->getCustomModelLabel() ?? $this->getTable()->getModelLabel();
    }

    public function getPluralModelLabel(): string
    {
        return $this->getCustomPluralModelLabel() ?? $this->getTable()->getPluralModelLabel();
    }

    public function getModel(): string
    {
        return $this->getCustomModel() ?? $this->getTable()->getModel();
    }

    public function prepareModalAction(StaticAction $action): StaticAction
    {
        $action = parent::prepareModalAction($action);

        if (! $action instanceof Action) {
            return $action;
        }

        return $action
            ->table($this->getTable())
            ->record($this->getRecord());
    }

    public function getInfolistName(): string
    {
        return 'mountedTableActionInfolist';
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function call(array $parameters = []): mixed
    {
        try {
            return parent::call($parameters);
        } finally {
            if ($this->shouldDeselectRecordsAfterCompletion()) {
                $this->getLivewire()->deselectAllTableRecords();
            }
        }
    }
}
