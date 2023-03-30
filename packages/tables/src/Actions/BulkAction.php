<?php

namespace Filament\Tables\Actions;

use Closure;
use Filament\Actions\MountableAction;
use ReflectionParameter;

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
     */
    public function call(array $parameters = []): mixed
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

    protected function resolveClosureDependencyForEvaluation(ReflectionParameter $parameter): mixed
    {
        return match ($parameter->getName()) {
            'records' => $this->getRecords(),
            'table' => $this->getTable(),
            default => parent::resolveClosureDependencyForEvaluation($parameter),
        };
    }

    /**
     * @param  array<mixed>  $arguments
     * @return array<mixed>
     */
    protected function parseAuthorizationArguments(array $arguments): array
    {
        array_unshift($arguments, $this->getTable()->getModel());

        return $arguments;
    }
}
