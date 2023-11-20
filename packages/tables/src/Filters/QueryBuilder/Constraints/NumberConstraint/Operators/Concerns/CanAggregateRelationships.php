<?php

namespace Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint\Operators\Concerns;

use Exception;
use Filament\Forms\Components\Select;
use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Str;

trait CanAggregateRelationships
{
    public static function getAggregateSelectName(): string
    {
        return 'aggregate';
    }

    public static function getAggregateAverageKey(): string
    {
        return 'avg';
    }

    public static function getAggregateMaxKey(): string
    {
        return 'max';
    }

    public static function getAggregateMinKey(): string
    {
        return 'min';
    }

    public static function getAggregateSumKey(): string
    {
        return 'sum';
    }

    public function queriesRelationshipsUsingSubSelect(): bool
    {
        return parent::queriesRelationshipsUsingSubSelect() && blank($this->getSettings()[static::getAggregateSelectName()]);
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
        return Select::make(static::getAggregateSelectName())
            ->label(__('filament-tables::filters/query-builder.operators.number.form.aggregate.label'))
            ->options([
                static::getAggregateSumKey() => __('filament-tables::filters/query-builder.operators.number.aggregates.sum.label'),
                static::getAggregateAverageKey() => __('filament-tables::filters/query-builder.operators.number.aggregates.average.label'),
                static::getAggregateMinKey() => __('filament-tables::filters/query-builder.operators.number.aggregates.max.label'),
                static::getAggregateMaxKey() => __('filament-tables::filters/query-builder.operators.number.aggregates.min.label'),
            ])
            ->visible($this->getConstraint()->queriesRelationships());
    }

    protected function getAggregate(): ?string
    {
        return $this->getSettings()[static::getAggregateSelectName()] ?? null;
    }

    protected function getAttributeLabel(): string
    {
        $attributeLabel = $this->getConstraint()->getAttributeLabel();

        return __(match ($this->getAggregate()) {
            static::getAggregateAverageKey() => 'filament-tables::filters/query-builder.operators.number.aggregates.average.summary',
            static::getAggregateMaxKey() => 'filament-tables::filters/query-builder.operators.number.aggregates.max.summary',
            static::getAggregateMinKey() => 'filament-tables::filters/query-builder.operators.number.aggregates.min.summary',
            static::getAggregateSumKey() => 'filament-tables::filters/query-builder.operators.number.aggregates.sum.summary',
            default => $attributeLabel,
        }, ['attribute' => $attributeLabel]);
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

    public function getConstraint(): ?NumberConstraint
    {
        $constraint = parent::getConstraint();

        if (! ($constraint instanceof NumberConstraint)) {
            throw new Exception('Constraint must be an instance of [' . NumberConstraint::class . '].');
        }

        return $constraint;
    }
}
