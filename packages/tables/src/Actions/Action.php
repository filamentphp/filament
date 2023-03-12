<?php

namespace Filament\Tables\Actions;

use Filament\Actions\Concerns\InteractsWithRecord;
use Filament\Actions\Contracts\Groupable;
use Filament\Actions\Contracts\HasRecord;
use Filament\Actions\MountableAction;
use Illuminate\Database\Eloquent\Model;

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
     * @return array<string, mixed>
     */
    protected function getDefaultEvaluationParameters(): array
    {
        return array_merge(parent::getDefaultEvaluationParameters(), [
            'record' => $this->resolveEvaluationParameter(
                'record',
                fn (): ?Model => $this->getRecord(),
            ),
            'table' => $this->getTable(),
        ]);
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
