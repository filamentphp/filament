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
    public const AGGREGATE_SELECT_NAME = 'aggregate';

    public const AGGREGATE_AVERAGE = 'avg';

    public const AGGREGATE_MAX = 'max';

    public const AGGREGATE_MIN = 'min';

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
            ->label(__('filament-tables::filters/query-builder.operators.number.form.aggregate.label'))
            ->options([
                static::AGGREGATE_SUM => __('filament-tables::filters/query-builder.operators.number.aggregates.sum.label'),
                static::AGGREGATE_AVERAGE => __('filament-tables::filters/query-builder.operators.number.aggregates.average.label'),
                static::AGGREGATE_MIN => __('filament-tables::filters/query-builder.operators.number.aggregates.max.label'),
                static::AGGREGATE_MAX => __('filament-tables::filters/query-builder.operators.number.aggregates.min.label'),
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

        return __(match ($this->getAggregate()) {
            static::AGGREGATE_AVERAGE => 'filament-tables::filters/query-builder.operators.number.aggregates.average.summary',
            static::AGGREGATE_MAX => 'filament-tables::filters/query-builder.operators.number.aggregates.max.summary',
            static::AGGREGATE_MIN => 'filament-tables::filters/query-builder.operators.number.aggregates.min.summary',
            static::AGGREGATE_SUM => 'filament-tables::filters/query-builder.operators.number.aggregates.sum.summary',
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
