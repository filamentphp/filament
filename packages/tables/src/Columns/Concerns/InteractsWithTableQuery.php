<?php

namespace Filament\Tables\Columns\Concerns;

use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;

use function Filament\Support\generate_search_column_expression;
use function Filament\Support\generate_search_term_expression;

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

        /** @var Connection $databaseConnection */
        $databaseConnection = $query->getConnection();

        $model = $query->getModel();

        $isSearchForcedCaseInsensitive = $this->isSearchForcedCaseInsensitive();

        $nonTranslatableSearch = generate_search_term_expression($search, $isSearchForcedCaseInsensitive, $databaseConnection);

        $translatableContentDriver = $this->getLivewire()->makeFilamentTranslatableContentDriver();

        foreach ($this->getSearchColumns($query->getModel()) as $searchColumn) {
            $whereClause = $isFirst ? 'where' : 'orWhere';

            $jsonSafeSearchColumn = $this->getJsonSafeColumnName($searchColumn, $model->getTable());

            $query->when(
                $translatableContentDriver?->isAttributeTranslatable($model::class, attribute: $searchColumn),
                fn (EloquentBuilder $query): EloquentBuilder => $translatableContentDriver->applySearchConstraintToQuery($query, $searchColumn, $search, $whereClause, $isSearchForcedCaseInsensitive),
                fn (EloquentBuilder $query) => $query->when(
                    $this->hasRelationship($query->getModel()),
                    fn (EloquentBuilder $query): EloquentBuilder => $query->{"{$whereClause}Relation"}(
                        $this->getRelationshipName($query->getModel()),
                        generate_search_column_expression($jsonSafeSearchColumn, $isSearchForcedCaseInsensitive, $databaseConnection),
                        'like',
                        "%{$nonTranslatableSearch}%",
                    ),
                    fn (EloquentBuilder $query) => $query->{$whereClause}(
                        generate_search_column_expression($jsonSafeSearchColumn, $isSearchForcedCaseInsensitive, $databaseConnection),
                        'like',
                        "%{$nonTranslatableSearch}%",
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

        foreach (array_reverse($this->getSortColumns($query->getModel())) as $sortColumn) {
            $sortColumn = $this->getJsonSafeColumnName($sortColumn, $query->getModel()->getTable());

            if (blank($relationshipName)) {
                $query->orderBy($sortColumn, $direction);

                continue;
            }

            if (str($relationshipName)->contains('.')) {
                $query->orderByPowerJoins("{$relationshipName}.{$sortColumn}", $direction, joinType: 'leftJoin'); /** @phpstan-ignore method.notFound */

                continue;
            }

            $query->orderBy($this->getRelationshipSortColumnForQuery($query, $sortColumn), $direction);
        }

        return $query;
    }

    protected function getRelationshipSortColumnForQuery(EloquentBuilder $query, string $sortColumn): string | Builder
    {
        $relationship = $this->getRelationship($query->getModel());

        $relatedQuery = $relationship->getRelated()::query();

        return $relationship
            ->getRelationExistenceQuery(
                $relatedQuery,
                $query,
                $sortColumn,
            )
            ->applyScopes()
            ->getQuery();
    }
}
