<?php

namespace Filament\Tables\Actions;

use Closure;
use Filament\Actions\Contracts\Groupable;
use Filament\Actions\MountableAction;
use Filament\Tables\Actions\Contracts\HasTable;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

class BulkAction extends MountableAction implements Groupable, HasTable
{
    use Concerns\BelongsToTable;
    use Concerns\CanDeselectRecordsAfterCompletion;
    use Concerns\InteractsWithRecords;

    protected function setUp(): void
    {
        parent::setUp();

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
            return $this->evaluate($this->getActionFunction(), $parameters);
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

    public function getLivewireCallMountedActionName(): string
    {
        return 'callMountedTableBulkAction';
    }

    public function getAlpineClickHandler(): ?string
    {
        return "mountBulkAction('{$this->getName()}')";
    }

    public function getLivewireTarget(): ?string
    {
        return "mountTableBulkAction('{$this->getName()}')";
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'records' => [$this->getRecords()],
            'table' => [$this->getTable()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByType(string $parameterType): array
    {
        return match ($parameterType) {
            Collection::class => [$this->getRecords()],
            EloquentCollection::class => [$this->getRecords()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType),
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

    public function getInfolistName(): string
    {
        return 'mountedTableBulkActionInfolist';
    }
}
