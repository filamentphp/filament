<?php

namespace Filament\Tables\Columns\Summarizers;

use Closure;
use Filament\Support\Components\ViewComponent;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class Summarizer extends ViewComponent
{
    use Concerns\BelongsToColumn;
    use Concerns\CanFormatState;
    use Concerns\HasLabel;
    use Concerns\InteractsWithTableQuery;

    protected string $evaluationIdentifier = 'summarizer';

    protected string $viewIdentifier = 'summarizer';

    protected string $view = 'filament-tables::columns.summaries.text';

    protected ?Closure $using = null;

    public static function make(): static
    {
        $static = app(static::class);
        $static->configure();

        return $static;
    }

    public function using(?Closure $using): static
    {
        $this->using = $using;

        return $this;
    }

    public function getState()
    {
        $column = $this->getColumn();
        $attribute = $column->getName();
        $query = $this->getQuery()->clone();

        if ($column->queriesRelationships($query->getModel())) {
            $relationship = $column->getRelationship($query->getModel());
            $attribute = $column->getRelationshipAttribute();

            $inverseRelationship = $column->getInverseRelationshipName ?? (string) str(class_basename($relationship->getParent()::class))
                ->plural()
                ->camel();

            $query = $relationship->getModel()->newQuery()
                ->whereHas(
                    $inverseRelationship,
                    fn (EloquentBuilder $relatedQuery): EloquentBuilder => $this->hasPaginatedQuery() ?
                        $relatedQuery->whereKey($this->getTable()->getRecords()->modelKeys()) :
                        $query,
                );
        }

        $query = DB::table($query);

        if ($this->hasQueryModificationCallback()) {
            $query = $this->evaluate($this->modifyQueryUsing, [
                'attribute' => $attribute,
                'query' => $query,
            ]) ?? $query;
        }

        if ($this->using !== null) {
            return $this->evaluate($this->using, [
                'attribute' => $attribute,
                'query' => $query,
            ]);
        }

        return $this->summarize($query, $attribute);
    }

    public function summarize(Builder $query, string $attribute)
    {
        return null;
    }

    protected function hasPaginatedQuery(): bool
    {
        return $this->getQuery()->getQuery()->limit !== null;
    }

    protected function getDefaultEvaluationParameters(): array
    {
        return array_merge(parent::getDefaultEvaluationParameters(), [
            'livewire' => $this->getLivewire(),
            'table' => $this->getTable(),
        ]);
    }
}
