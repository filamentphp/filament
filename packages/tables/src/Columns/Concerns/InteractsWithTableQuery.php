<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;
use Filament\Support\Services\RelationshipOrderer;
use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Znck\Eloquent\Relations\BelongsToThrough;

use function Filament\Support\apply_search_constraint;
use function Filament\Support\is_database_driver_supported;

trait InteractsWithTableQuery
{
    public function applyRelationshipAggregates(EloquentBuilder | Relation $query): EloquentBuilder | Relation
    {
        return $query->when(
            filled([$this->getRelationshipToAvg(), $this->getColumnToAvg()]),
            fn ($query) => $query->withAvg($this->getRelationshipToAvg(), $this->getColumnToAvg())
        )->when(
            filled($this->getRelationshipsToCount()),
            fn ($query) => $query->withCount(Arr::wrap($this->getRelationshipsToCount()))
        )->when(
            filled($this->getRelationshipsToExistenceCheck()),
            fn ($query) => $query->withExists(Arr::wrap($this->getRelationshipsToExistenceCheck()))
        )->when(
            filled([$this->getRelationshipToMax(), $this->getColumnToMax()]),
            fn ($query) => $query->withMax($this->getRelationshipToMax(), $this->getColumnToMax())
        )->when(
            filled([$this->getRelationshipToMin(), $this->getColumnToMin()]),
            fn ($query) => $query->withMin($this->getRelationshipToMin(), $this->getColumnToMin())
        )->when(
            filled([$this->getRelationshipToSum(), $this->getColumnToSum()]),
            fn ($query) => $query->withSum($this->getRelationshipToSum(), $this->getColumnToSum())
        );
    }

    public function applyEagerLoading(EloquentBuilder | Relation $query): EloquentBuilder | Relation
    {
        if (! $this->hasRelationship($query->getModel())) {
            return $query;
        }

        $relationshipName = $this->getRelationshipName($query->getModel());

        if (array_key_exists($relationshipName, $query->getEagerLoads())) {
            return $query;
        }

        return $query->with([$relationshipName]);
    }

    public function applySearchConstraint(EloquentBuilder $query, string $search, bool &$isFirst): EloquentBuilder
    {
        if ($this->searchQuery) {
            $whereClause = $isFirst ? 'where' : 'orWhere';

            $query->{$whereClause}(
                fn ($query) => $this->evaluate($this->searchQuery, [
                    'query' => $query,
                    'search' => $search,
                    'searchQuery' => $search,
                ]),
            );

            $isFirst = false;

            return $query;
        }

        $model = $query->getModel();

        $isSearchForcedCaseInsensitive = $this->isSearchForcedCaseInsensitive();

        $translatableContentDriver = $this->getLivewire()->makeFilamentTranslatableContentDriver();

        foreach ($this->getSearchColumns($query->getModel()) as $searchColumn) {
            $whereClause = $isFirst ? 'where' : 'orWhere';

            $query->when(
                $translatableContentDriver?->isAttributeTranslatable($model::class, attribute: $searchColumn),
                fn (EloquentBuilder $query): EloquentBuilder => $translatableContentDriver->applySearchConstraintToQuery($query, $searchColumn, $search, $whereClause, $isSearchForcedCaseInsensitive),
                fn (EloquentBuilder $query) => $query->when(
                    $this->hasRelationship($query->getModel()),
                    function (EloquentBuilder $query) use ($model, $whereClause, $searchColumn, $isSearchForcedCaseInsensitive, $search): EloquentBuilder {
                        $relationshipName = $this->getRelationshipName($query->getModel());
                        $relationship = $this->getRelationship($query->getModel(), $relationshipName);

                        $relatedTable = $model->getTable();

                        if (($relationship instanceof BelongsToThrough) || ($relationship instanceof HasManyDeep)) {
                            $relatedTable = $relationship->getRelated()->getTable();
                            $searchColumn = str($searchColumn)->startsWith("{$relatedTable}.")
                                ? $searchColumn
                                : $relationship->getRelated()->qualifyColumn($searchColumn);
                        }

                        return $query->{"{$whereClause}Has"}(
                            $relationshipName,
                            fn (EloquentBuilder $query): EloquentBuilder => apply_search_constraint(
                                $query,
                                $this->getJsonSafeColumnName($searchColumn, $relatedTable),
                                "%{$search}%",
                                $isSearchForcedCaseInsensitive,
                            ),
                        );
                    },
                    fn (EloquentBuilder $query) => apply_search_constraint(
                        $query,
                        $this->getJsonSafeColumnName($searchColumn, $model->getTable()),
                        "%{$search}%",
                        $isSearchForcedCaseInsensitive,
                        ($whereClause === 'where') ? 'and' : 'or',
                    ),
                ),
            );

            $isFirst = false;
        }

        return $query;
    }

    protected function getJsonSafeColumnName(string $column, string $tableName): string
    {
        if (str($column)->startsWith("{$tableName}.")) {
            return (string) str($column)->after('.')->replace('.', '->')->prepend("{$tableName}.");
        }

        return (string) str($column)->replace('.', '->');
    }

    public function applySort(EloquentBuilder $query, string $direction = 'asc'): EloquentBuilder
    {
        if ($this->sortQuery) {
            $this->evaluate($this->sortQuery, [
                'direction' => $direction,
                'query' => $query,
            ]);

            return $query;
        }

        $relationshipName = $this->getRelationshipName($query->getModel());
        $orderByRelationAggregateMethod = 'orderByRelationAggregate';
        $canOrderByRelationAggregate = (! is_database_driver_supported($query->getConnection())) && method_exists($query, $orderByRelationAggregateMethod);
        $relationshipAggregates = $canOrderByRelationAggregate ? $this->getRelationshipAggregates() : [];

        foreach (array_reverse($this->getSortColumns($query->getModel())) as $sortColumn) {
            $sortColumn = $this->getJsonSafeColumnName($sortColumn, $query->getModel()->getTable());
            $relationshipAggregateForSortColumn = $this->getRelationshipAggregateForSortColumn($query, $relationshipAggregates, $sortColumn);

            if (
                $relationshipAggregateForSortColumn &&
                $canOrderByRelationAggregate
            ) {
                $query->{$orderByRelationAggregateMethod}(
                    $relationshipAggregateForSortColumn['relation'],
                    $relationshipAggregateForSortColumn['column'],
                    $relationshipAggregateForSortColumn['function'],
                    $direction,
                    $relationshipAggregateForSortColumn['callback'],
                );

                continue;
            }

            if (filled($relationshipName)) {
                app(RelationshipOrderer::class)->orderQuery(
                    $query,
                    $relationshipName,
                    $sortColumn,
                    $direction,
                );

                continue;
            }

            $query->orderBy($sortColumn, $direction);
        }

        return $query;
    }

    /**
     * @return array<array{relations: string | array<int | string, string | Closure>, column: string | Expression, function: string}>
     */
    protected function getRelationshipAggregates(): array
    {
        $relationshipAggregates = [];

        if (filled($relationshipToAverage = $this->getRelationshipToAvg()) && filled($columnToAverage = $this->getColumnToAvg())) {
            $relationshipAggregates[] = ['relations' => $relationshipToAverage, 'column' => $columnToAverage, 'function' => 'avg'];
        }

        if (filled($relationshipsToCount = $this->getRelationshipsToCount())) {
            $relationshipAggregates[] = ['relations' => $relationshipsToCount, 'column' => '*', 'function' => 'count'];
        }

        if (filled($relationshipsToExistenceCheck = $this->getRelationshipsToExistenceCheck())) {
            $relationshipAggregates[] = ['relations' => $relationshipsToExistenceCheck, 'column' => '*', 'function' => 'exists'];
        }

        if (filled($relationshipToMaximum = $this->getRelationshipToMax()) && filled($columnToMaximum = $this->getColumnToMax())) {
            $relationshipAggregates[] = ['relations' => $relationshipToMaximum, 'column' => $columnToMaximum, 'function' => 'max'];
        }

        if (filled($relationshipToMinimum = $this->getRelationshipToMin()) && filled($columnToMinimum = $this->getColumnToMin())) {
            $relationshipAggregates[] = ['relations' => $relationshipToMinimum, 'column' => $columnToMinimum, 'function' => 'min'];
        }

        if (filled($relationshipToSum = $this->getRelationshipToSum()) && filled($columnToSum = $this->getColumnToSum())) {
            $relationshipAggregates[] = ['relations' => $relationshipToSum, 'column' => $columnToSum, 'function' => 'sum'];
        }

        return $relationshipAggregates;
    }

    /**
     * @param  array<array{relations: string | array<int | string, string | Closure>, column: string | Expression, function: string}>  $relationshipAggregates
     * @return array{relation: string, column: string | Expression, function: string, callback: Closure | null} | null
     */
    protected function getRelationshipAggregateForSortColumn(EloquentBuilder $query, array $relationshipAggregates, string $sortColumn): ?array
    {
        foreach ($relationshipAggregates as $relationshipAggregate) {
            foreach (Arr::wrap($relationshipAggregate['relations']) as $relationKey => $relationConfiguration) {
                $modifyRelationQueryUsing = null;

                if (is_int($relationKey) && is_string($relationConfiguration)) {
                    $relation = $relationConfiguration;
                } elseif (is_string($relationKey) && ($relationConfiguration instanceof Closure)) {
                    $relation = $relationKey;
                    $modifyRelationQueryUsing = $relationConfiguration;
                } else {
                    continue;
                }

                $relationNameSegments = explode(' ', $relation);
                $aggregateAlias = null;

                if ((count($relationNameSegments) === 3) && (Str::lower($relationNameSegments[1]) === 'as')) {
                    $relation = $relationNameSegments[0];
                    $aggregateAlias = $relationNameSegments[2];
                }

                $aggregateAlias ??= Str::snake((string) preg_replace(
                    '/[^[:alnum:][:space:]_]/u',
                    '',
                    sprintf(
                        '%s %s %s',
                        $relation,
                        $relationshipAggregate['function'],
                        Str::lower((string) $query->getQuery()->getGrammar()->getValue($relationshipAggregate['column'])),
                    ),
                ));

                if ($aggregateAlias !== $sortColumn) {
                    continue;
                }

                return [
                    'relation' => $relation,
                    'column' => $relationshipAggregate['column'],
                    'function' => $relationshipAggregate['function'],
                    'callback' => $modifyRelationQueryUsing,
                ];
            }
        }

        return null;
    }
}
