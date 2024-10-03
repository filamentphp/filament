<?php

namespace Filament\Tables\Columns;

use Exception;
use Filament\Support\Components\ViewComponent;
use Filament\Support\Concerns\CanAggregateRelatedModels;
use Filament\Support\Concerns\CanGrow;
use Filament\Support\Concerns\HasAlignment;
use Filament\Support\Concerns\HasCellState;
use Filament\Support\Concerns\HasExtraAttributes;
use Filament\Support\Concerns\HasPlaceholder;
use Filament\Support\Concerns\HasVerticalAlignment;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Traits\Conditionable;

class Column extends ViewComponent
{
    use CanAggregateRelatedModels;
    use CanGrow;
    use Concerns\BelongsToGroup;
    use Concerns\BelongsToLayout;
    use Concerns\BelongsToTable;
    use Concerns\CanBeDisabled;
    use Concerns\CanBeHidden;
    use Concerns\CanBeInline;
    use Concerns\CanBeSearchable;
    use Concerns\CanBeSortable;
    use Concerns\CanBeSummarized;
    use Concerns\CanBeToggled;
    use Concerns\CanCallAction;
    use Concerns\CanOpenUrl;
    use Concerns\CanSpanColumns;
    use Concerns\CanWrapHeader;
    use Concerns\HasExtraCellAttributes;
    use Concerns\HasExtraHeaderAttributes;
    use Concerns\HasLabel;
    use Concerns\HasName;
    use Concerns\HasRecord;
    use Concerns\HasRowLoopObject;
    use Concerns\HasTooltip;
    use Concerns\HasWidth;
    use Concerns\InteractsWithTableQuery;
    use Conditionable;
    use HasAlignment;
    use HasCellState;
    use HasExtraAttributes;
    use HasPlaceholder;
    use HasVerticalAlignment;

    protected string $evaluationIdentifier = 'column';

    protected string $viewIdentifier = 'column';

    final public function __construct(string $name)
    {
        $this->name($name);
    }

    public static function make(string $name): static
    {
        $static = app(static::class, ['name' => $name]);
        $static->configure();

        return $static;
    }

    public function getTable(): Table
    {
        return $this->table ?? $this->getGroup()?->getTable() ?? $this->getLayout()?->getTable() ?? throw new Exception("The column [{$this->getName()}] is not mounted to a table.");
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
            'state' => [$this->getState()],
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
