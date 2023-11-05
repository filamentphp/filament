<?php

namespace Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint\Operators\Concerns;

use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Str;

trait CanAggregateRelationships
{
    public const AGGREGATE_SELECT_NAME = 'aggregate';

    public const AGGREGATE_AVERAGE = 'avg';

    public const AGGREGATE_MAXIMUM = 'max';

    public const AGGREGATE_MINIMUM = 'min';

    public const AGGREGATE_SUM = 'sum';

    public function queriesRelationshipsUsingSubSelect(): bool
    {
        return parent::queriesRelationshipsUsingSubSelect() && blank($this->getSettings()[static::AGGREGATE_SELECT_NAME]);
    }

    public function applyToBaseFilterQuery(Builder $query): Builder
    {
        if (filled($this->getAggregate())) {
            $aggregateAlias = $this->generateAggregateAlias();

            if ($this->getConstraint()->isExistingAggregateAlias($aggregateAlias)) {
                return $query;
            }

            $relationship = $query->getModel()->{$this->getConstraint()->getRelationshipName()}();
            $relatedModel = $relationship->getModel();
            $relatedQuery = $relatedModel->newQuery();

            $qualifiedColumn = $relatedQuery->qualifyColumn($this->getConstraint()->getAttributeForQuery());

            $query->leftJoinSub(
                $relatedQuery
                    ->groupBy($relatedModel->getQualifiedKeyName())
                    ->selectRaw("{$relatedModel->getQualifiedKeyName()}, {$this->getAggregate()}({$qualifiedColumn}) as {$aggregateAlias}"),
                $aggregateAlias,
                fn (JoinClause $join) => $join->on("{$aggregateAlias}.{$relatedModel->getKeyName()}", '=', $query->getModel()->getQualifiedKeyName())
            );

            $this->getConstraint()->reportAggregateAlias($aggregateAlias);
        }

        return $query;
    }

    protected function getAggregateSelect(): Select
    {
        return Select::make(static::AGGREGATE_SELECT_NAME)
            ->options([
                static::AGGREGATE_AVERAGE => 'Average',
                static::AGGREGATE_MAXIMUM => 'Maximum',
                static::AGGREGATE_MINIMUM => 'Minimum',
                static::AGGREGATE_SUM => 'Sum',
            ])
            ->visible($this->getConstraint()->queriesRelationships());
    }

    protected function getAggregate(): ?string
    {
        return $this->getSettings()[static::AGGREGATE_SELECT_NAME] ?? null;
    }

    protected function getAttributeLabel(): string
    {
        $attributeLabel = $this->getConstraint()->getAttributeLabel();

        return match ($this->getAggregate()) {
            static::AGGREGATE_AVERAGE => "Average {$attributeLabel}",
            static::AGGREGATE_MAXIMUM => "Maximum {$attributeLabel}",
            static::AGGREGATE_MINIMUM => "Minimum {$attributeLabel}",
            static::AGGREGATE_SUM => "Sum of {$attributeLabel}",
            default => $attributeLabel,
        };
    }

    protected function generateAggregateAlias(): string
    {
        $relationshipName = Str::snake($this->getConstraint()->getRelationshipName());

        return "{$relationshipName}_{$this->getAggregate()}_{$this->getConstraint()->getAttributeForQuery()}";
    }

    protected function replaceQualifiedColumnWithQualifiedAggregateColumn(string $qualifiedColumn): string
    {
        if (blank($this->getAggregate())) {
            return $qualifiedColumn;
        }

        $aggregateAlias = $this->generateAggregateAlias();

        return "{$aggregateAlias}.{$aggregateAlias}";
    }
}
