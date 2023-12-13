<?php

namespace Filament\Tables\Actions;

use Closure;
use Filament\Actions\ActionGroup as BaseActionGroup;
use Filament\Actions\Concerns\InteractsWithRecord;
use Filament\Actions\Contracts\HasRecord;
use Filament\Tables\Actions\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

/**
 * @property array<Action | BulkAction> $actions
 */
class ActionGroup extends BaseActionGroup implements HasRecord, HasTable
{
    use InteractsWithRecord;

    public function record(Model | Closure | null $record): static
    {
        $this->record = $record;

        foreach ($this->actions as $action) {
            if (! $action instanceof HasRecord) {
                continue;
            }

            $action->record($record);
        }

        return $this;
    }

    public function table(Table $table): static
    {
        foreach ($this->actions as $action) {
            if (! $action instanceof HasTable) {
                continue;
            }

            $action->table($table);
        }

        return $this;
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'record' => [$this->getRecord()],
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
}
