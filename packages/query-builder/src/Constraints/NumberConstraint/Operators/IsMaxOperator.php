<?php

namespace Filament\QueryBuilder\Constraints\NumberConstraint\Operators;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\TextInput;
use Filament\QueryBuilder\Constraints\Operators\Operator;
use Filament\Schemas\Components\Component;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Number;

class IsMaxOperator extends Operator
{
    use Concerns\CanAggregateRelationships;

    public function getName(): string
    {
        return 'isMax';
    }

    public function getLabel(): string
    {
        return __(
            $this->isInverse() ?
                'filament-query-builder::query-builder.operators.number.is_max.label.inverse' :
                'filament-query-builder::query-builder.operators.number.is_max.label.direct',
        );
    }

    public function getSummary(): string
    {
        return __(
            $this->isInverse() ?
                'filament-query-builder::query-builder.operators.number.is_max.summary.inverse' :
                'filament-query-builder::query-builder.operators.number.is_max.summary.direct',
            [
                'attribute' => $this->getAttributeLabel(),
                'number' => Number::format($this->getSettings()['number']),
            ],
        );
    }

    /**
     * @return array<Component | Action | ActionGroup>
     */
    public function getFormSchema(): array
    {
        return [
            TextInput::make('number')
                ->label(__('filament-query-builder::query-builder.operators.number.form.number.label'))
                ->numeric()
                ->integer($this->getConstraint()->isInteger())
                ->required(),
            $this->getAggregateSelect(),
        ];
    }

    public function apply(Builder $query, string $qualifiedColumn): Builder
    {
        if (filled($this->getAggregate())) {
            $relationshipName = $this->getConstraint()->getRelationshipName();
            $attributeForQuery = $this->getConstraint()->getAttributeForQuery();
            $operator = $this->isInverse() ? '>' : '<=';
            $value = floatval($this->getSettings()['number']);
            $aggregate = $this->getAggregate();

            // Build a scalar subquery for the aggregate
            $relationship = $query->getModel()->{$relationshipName}();
            $relatedModel = $relationship->getModel();
            $relatedTable = $relatedModel->getTable();
            $foreignKeyName = $relationship->getForeignKeyName();
            $ownerKeyName = $relationship->getLocalKeyName();
            $mainTable = $query->getModel()->getTable();

            // Create the subquery using the relationship to get proper scopes
            // Cast to REAL to ensure numeric comparison (important for SQLite)
            $subQuery = $relatedModel->query()
                ->selectRaw("CAST({$aggregate}({$relatedTable}.{$attributeForQuery}) AS REAL)")
                ->whereColumn("{$relatedTable}.{$foreignKeyName}", "{$mainTable}.{$ownerKeyName}");

            // Add the comparison
            $query->whereRaw("({$subQuery->toSql()}) {$operator} ?", array_merge($subQuery->getBindings(), [$value]));

            return $query;
        }

        return $query->where($this->replaceQualifiedColumnWithQualifiedAggregateColumn($qualifiedColumn), $this->isInverse() ? '>' : '<=', floatval($this->getSettings()['number']));
    }
}
