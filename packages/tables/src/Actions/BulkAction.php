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

    protected function setUp(): void
    {
        parent::setUp();

        $this->grouped();

        $this->extraAttributes([
            'x-bind:disabled' => '! selectedRecords.length',
        ]);
    }

    /**
     * @param  array<string, mixed>  $parameters
     * @return mixed
     */
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

    public function getAlpineMountAction(): ?string
    {
        return "mountBulkAction('{$this->getName()}')";
    }

    /**
     * @return array<string, mixed>
     */
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

    /**
     * @param array<mixed> $arguments
     * @return array<mixed>
     */
    protected function parseAuthorizationArguments(array $arguments): array
    {
        array_unshift($arguments, $this->getTable()->getModel());

        return $arguments;
    }
}
