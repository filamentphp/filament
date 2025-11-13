<?php

namespace Filament\QueryBuilder\Constraints\NumberConstraint\Operators\Concerns;

use Filament\Forms\Components\Select;
use Filament\QueryBuilder\Constraints\NumberConstraint;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use LogicException;

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

    protected function getNumericCastType(Builder $query): string
    {
        /** @var Connection $databaseConnection */
        $databaseConnection = $query->getConnection();

        $driver = $databaseConnection->getDriverName();

        return match ($driver) {
            'sqlite' => 'real',
            'pgsql' => 'numeric',
            default => 'decimal(10,2)',
        };
    }

    protected function applyAggregateComparison(Builder $query, string $operator, float $value): Builder
    {
        $relationshipName = $this->getConstraint()->getRelationshipName();
        $attributeForQuery = $this->getConstraint()->getAttributeForQuery();
        $aggregate = $this->getAggregate();

        /** @var Relation $relationship */
        $relationship = $query->getModel()->{$relationshipName}();

        $relatedModel = $relationship->getModel();
        $attributeForQuery = $relatedModel->qualifyColumn($attributeForQuery);
        $castType = $this->getNumericCastType($query);

        if ($relationship instanceof BelongsToMany) {
            $pivotTable = $relationship->getTable();
            $foreignPivotKey = $relationship->getQualifiedForeignPivotKeyName();
            $relatedPivotKey = $relationship->getQualifiedRelatedPivotKeyName();
            $parentKey = $relationship->getQualifiedParentKeyName();
            $relatedKey = $relationship->getQualifiedRelatedKeyName();

            $subQuery = $relatedModel->query()
                ->selectRaw("cast({$aggregate}({$attributeForQuery}) as {$castType})")
                ->join($pivotTable, $relatedKey, '=', $relatedPivotKey)
                ->whereColumn($foreignPivotKey, $parentKey);

            return $query->whereRaw("({$subQuery->toSql()}) {$operator} ?", [...$subQuery->getBindings(), $value]);
        }

        if ($relationship instanceof HasOneOrMany) {
            $foreignKeyName = $relationship->getQualifiedForeignKeyName();
            $parentKeyName = $relationship->getQualifiedParentKeyName();

            $subQuery = $relatedModel->query()
                ->selectRaw("cast({$aggregate}({$attributeForQuery}) as {$castType})")
                ->whereColumn($foreignKeyName, $parentKeyName);

            return $query->whereRaw("({$subQuery->toSql()}) {$operator} ?", [...$subQuery->getBindings(), $value]);
        }

        throw new LogicException('Relationship type [' . get_class($relationship) . '] is not supported for aggregate queries.');
    }

    protected function getAggregateSelect(): Select
    {
        return Select::make(static::getAggregateSelectName())
            ->label(__('filament-query-builder::query-builder.operators.number.form.aggregate.label'))
            ->options([
                static::getAggregateSumKey() => __('filament-query-builder::query-builder.operators.number.aggregates.sum.label'),
                static::getAggregateAverageKey() => __('filament-query-builder::query-builder.operators.number.aggregates.average.label'),
                static::getAggregateMinKey() => __('filament-query-builder::query-builder.operators.number.aggregates.max.label'),
                static::getAggregateMaxKey() => __('filament-query-builder::query-builder.operators.number.aggregates.min.label'),
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
            static::getAggregateAverageKey() => 'filament-query-builder::query-builder.operators.number.aggregates.average.summary',
            static::getAggregateMaxKey() => 'filament-query-builder::query-builder.operators.number.aggregates.max.summary',
            static::getAggregateMinKey() => 'filament-query-builder::query-builder.operators.number.aggregates.min.summary',
            static::getAggregateSumKey() => 'filament-query-builder::query-builder.operators.number.aggregates.sum.summary',
            default => $attributeLabel,
        }, ['attribute' => $attributeLabel]);
    }

    public function getConstraint(): ?NumberConstraint
    {
        $constraint = parent::getConstraint();

        if (! ($constraint instanceof NumberConstraint)) {
            throw new LogicException('Constraint must be an instance of [' . NumberConstraint::class . '].');
        }

        return $constraint;
    }
}
