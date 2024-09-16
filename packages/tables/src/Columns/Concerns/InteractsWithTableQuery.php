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

        $relationshipName = $this->getRelationshipName();

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

        foreach ($this->getSearchColumns() as $searchColumn) {
            $whereClause = $isFirst ? 'where' : 'orWhere';

            $query->when(
                $translatableContentDriver?->isAttributeTranslatable($model::class, attribute: $searchColumn),
                fn (EloquentBuilder $query): EloquentBuilder => $translatableContentDriver->applySearchConstraintToQuery($query, $searchColumn, $search, $whereClause, $isSearchForcedCaseInsensitive),
                fn (EloquentBuilder $query) => $query->when(
                    $this->hasRelationship($query->getModel()),
                    fn (EloquentBuilder $query): EloquentBuilder => $query->{"{$whereClause}Relation"}(
                        $this->getRelationshipName(),
                        generate_search_column_expression($searchColumn, $isSearchForcedCaseInsensitive, $databaseConnection),
                        'like',
                        "%{$nonTranslatableSearch}%",
                    ),
                    function (EloquentBuilder $query) use ($databaseConnection, $isSearchForcedCaseInsensitive, $nonTranslatableSearch, $searchColumn, $whereClause): EloquentBuilder {
                        // Treat the missing "relationship" as a JSON column if dot notation is used in the column name.
                        if (filled($relationshipName = $this->getRelationshipName())) {
                            $searchColumn = (string) str($relationshipName)
                                ->append('.')
                                ->append($searchColumn)
                                ->replace('.', '->');
                        }

                        return $query->{$whereClause}(
                            generate_search_column_expression($searchColumn, $isSearchForcedCaseInsensitive, $databaseConnection),
                            'like',
                            "%{$nonTranslatableSearch}%",
                        );
                    },
                ),
            );

            $isFirst = false;
        }

        return $query;
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

        foreach (array_reverse($this->getSortColumns()) as $sortColumn) {
            $query->orderBy($this->getSortColumnForQuery($query, $sortColumn), $direction);
        }

        return $query;
    }

    /**
     * @param  array<string> | null  $relationships
     */
    protected function getSortColumnForQuery(EloquentBuilder $query, string $sortColumn, ?array $relationships = null): string | Builder
    {
        $relationships ??= ($relationshipName = $this->getRelationshipName()) ?
            explode('.', $relationshipName) :
            [];

        if (! count($relationships)) {
            return $sortColumn;
        }

        $currentRelationshipName = array_shift($relationships);

        $relationship = $this->getRelationship($query->getModel(), $currentRelationshipName);

        if (! $relationship) {
            // Treat the missing "relationship" as a JSON column if dot notation is used in the column name.
            return (string) str($relationshipName ?? $this->getRelationshipName())
                ->append('.')
                ->append($sortColumn)
                ->replace('.', '->');
        }

        $relatedQuery = $relationship->getRelated()::query();

        return $relationship
            ->getRelationExistenceQuery(
                $relatedQuery,
                $query,
                [$currentRelationshipName => $this->getSortColumnForQuery(
                    $relatedQuery,
                    $sortColumn,
                    $relationships,
                )],
            )
            ->applyScopes()
            ->getQuery();
    }
}
