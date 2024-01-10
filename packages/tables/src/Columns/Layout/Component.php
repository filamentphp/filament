<?php

namespace Filament\Tables\Columns\Layout;

use Closure;
use Exception;
use Filament\Support\Components\ViewComponent;
use Filament\Support\Concerns\CanGrow;
use Filament\Support\Concerns\HasExtraAttributes;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\Concerns\BelongsToLayout;
use Filament\Tables\Columns\Concerns\BelongsToTable;
use Filament\Tables\Columns\Concerns\CanBeHidden;
use Filament\Tables\Columns\Concerns\CanSpanColumns;
use Filament\Tables\Columns\Concerns\HasRecord;
use Filament\Tables\Columns\Concerns\HasRowLoopObject;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Traits\Conditionable;

class Component extends ViewComponent
{
    use BelongsToLayout;
    use BelongsToTable;
    use CanBeHidden;
    use CanGrow;
    use CanSpanColumns;
    use Conditionable;
    use HasExtraAttributes;
    use HasRecord;
    use HasRowLoopObject;

    protected string $evaluationIdentifier = 'layout';

    protected string $viewIdentifier = 'layout';

    /**
     * @var array<Column | Component> | Closure
     */
    protected array | Closure $components = [];

    protected bool $isCollapsible = false;

    protected bool | Closure $isCollapsed = true;

    /**
     * @param  array<Column | Component> | Closure  $schema
     */
    public function schema(array | Closure $schema): static
    {
        $this->components($schema);

        return $this;
    }

    /**
     * @param  array<Column | Component> | Closure  $components
     */
    public function components(array | Closure $components): static
    {
        $this->components = $components;

        return $this;
    }

    public function collapsible(bool $condition = true): static
    {
        $this->isCollapsible = $condition;

        return $this;
    }

    public function collapsed(bool | Closure $condition = true): static
    {
        $this->collapsible();
        $this->isCollapsed = $condition;

        return $this;
    }

    public function isCollapsed(): bool
    {
        return (bool) $this->evaluate($this->isCollapsed);
    }

    /**
     * @return array<string, Column>
     */
    public function getColumns(): array
    {
        $columns = [];

        foreach ($this->getComponents() as $component) {
            if ($component instanceof Column) {
                $columns[$component->getName()] = $component;

                continue;
            }

            $columns = [
                ...$columns,
                ...$component->getColumns(),
            ];
        }

        return $columns;
    }

    /**
     * @return array<Column | Component>
     */
    public function getComponents(): array
    {
        return array_map(function (Component | Column $component): Component | Column {
            return $component->layout($this);
        }, $this->evaluate($this->components));
    }

    public function getTable(): Table
    {
        return $this->table ?? $this->getLayout()?->getTable() ?? throw new Exception('The column layout component is not mounted to a table.');
    }

    public function isCollapsible(): bool
    {
        return $this->isCollapsible;
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'livewire' => [$this->getLivewire()],
            'record' => [$this->getRecord()],
            'rowLoop' => [$this->getRowLoop()],
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
}
