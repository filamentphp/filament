<?php

namespace Filament\Tables\Actions;

use Filament\Actions\Concerns\InteractsWithRecord;
use Filament\Actions\Contracts\Groupable;
use Filament\Actions\Contracts\HasRecord;
use Filament\Actions\MountableAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use ReflectionParameter;

class Action extends MountableAction implements Groupable, HasRecord
{
    use Concerns\BelongsToTable;
    use InteractsWithRecord;

    public function getLivewireCallActionName(): string
    {
        return 'callMountedTableAction';
    }

    public function getLivewireMountAction(): ?string
    {
        if (! $this->isMountedOnClick()) {
            return null;
        }

        if ($this->getUrl()) {
            return null;
        }

        if ($record = $this->getRecord()) {
            $recordKey = $this->getLivewire()->getTableRecordKey($record);

            return "mountTableAction('{$this->getName()}', '{$recordKey}')";
        }

        return "mountTableAction('{$this->getName()}')";
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'record' => [$this->getRecord()],
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
}
