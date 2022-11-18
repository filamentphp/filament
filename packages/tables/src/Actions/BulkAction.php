<?php

namespace Filament\Tables\Actions;

use Closure;
use Filament\Actions\MountableAction;
use Illuminate\Database\Eloquent\Collection;

class BulkAction extends MountableAction
{
    use Concerns\BelongsToTable;
    use Concerns\CanDeselectRecordsAfterCompletion;
    use Concerns\InteractsWithRecords;

    protected string $view = 'filament-tables::actions.bulk-action';

    public function call(array $parameters = [])
    {
        try {
            return $this->evaluate($this->getAction(), $parameters);
        } finally {
            if ($this->shouldDeselectRecordsAfterCompletion()) {
                $this->getLivewire()->deselectAllTableRecords();
            }
        }
    }

    public function getAction(): ?Closure
    {
        $action = $this->action;

        if (is_string($action)) {
            $action = Closure::fromCallable([$this->getLivewire(), $action]);
        }

        return $action;
    }

    public function getLivewireCallActionName(): string
    {
        return 'callMountedTableBulkAction';
    }

    protected function getDefaultEvaluationParameters(): array
    {
        return array_merge(parent::getDefaultEvaluationParameters(), [
            'records' => $this->resolveEvaluationParameter(
                'records',
                fn (): ?Collection => $this->getRecords(),
            ),
            'table' => $this->getTable(),
        ]);
    }

    protected function parseAuthorizationArguments(array $arguments): array
    {
        array_unshift($arguments, $this->getTable()->getModel());

        return $arguments;
    }
}
