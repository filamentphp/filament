<?php

namespace Filament\Tables\Columns\Concerns;

use Exception;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

trait InteractsWithTableQuery
{
    protected ?string $inverseRelationshipName = null;

    public function inverseRelationship(?string $name): static
    {
        $this->inverseRelationshipName = $name;

        return $this;
    }

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
        if (! $this->queriesRelationships($query->getModel())) {
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

        $model = $query->getModel();

        $translatableContentDriver = $this->getLivewire()->makeFilamentTranslatableContentDriver();

        foreach ($this->getSearchColumns() as $searchColumn) {
            $whereClause = $isFirst ? 'where' : 'orWhere';

            $query->when(
                $translatableContentDriver?->isAttributeTranslatable($model::class, attribute: $searchColumn),
                fn (EloquentBuilder $query): EloquentBuilder => $translatableContentDriver->applySearchConstraintToQuery($query, $searchColumn, $search, $whereClause, $this->isSearchForcedCaseInsensitive($query)),
                function (EloquentBuilder $query) use ($search, $searchColumn, $whereClause): EloquentBuilder {
                    /** @var Connection $databaseConnection */
                    $databaseConnection = $query->getConnection();

                    $isSearchForcedCaseInsensitive = $this->isSearchForcedCaseInsensitive($query);

                    $caseAwareSearchColumn = $isSearchForcedCaseInsensitive ?
                        new Expression("lower({$searchColumn})") :
                        $searchColumn;

                    if ($isSearchForcedCaseInsensitive) {
                        $search = Str::lower($search);
                    }

                    return $query->when(
                        $this->queriesRelationships($query->getModel()),
                        fn (EloquentBuilder $query): EloquentBuilder => $query->{"{$whereClause}Relation"}(
                            $this->getRelationshipName(),
                            $caseAwareSearchColumn,
                            'like',
                            "%{$search}%",
                        ),
                        fn (EloquentBuilder $query): EloquentBuilder => $query->{$whereClause}(
                            $caseAwareSearchColumn,
                            'like',
                            "%{$search}%",
                        ),
                    );
                },
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

    public function queriesRelationships(Model $record): bool
    {
        return $this->getRelationship($record) !== null;
    }

    public function getRelationship(Model $record, ?string $name = null): ?Relation
    {
        if (blank($name) && (! str($this->getName())->contains('.'))) {
            return null;
        }

        $relationship = null;

        foreach (explode('.', $name ?? $this->getRelationshipName()) as $nestedRelationshipName) {
            if (! $record->isRelation($nestedRelationshipName)) {
                $relationship = null;

                break;
            }

            $relationship = $record->{$nestedRelationshipName}();
            $record = $relationship->getRelated();
        }

        return $relationship;
    }

    /**
     * @param  array<string> | null  $relationships
     * @return array<Model>
     */
    public function getRelationshipResults(Model $record, ?array $relationships = null): array
    {
        $results = [];

        $relationships ??= explode('.', $this->getRelationshipName());

        while (count($relationships)) {
            $currentRelationshipName = array_shift($relationships);

            $currentRelationshipValue = $record->getRelationValue($currentRelationshipName);

            if ($currentRelationshipValue instanceof Collection) {
                if (! count($relationships)) {
                    $results = [
                        ...$results,
                        ...$currentRelationshipValue->all(),
                    ];

                    continue;
                }

                foreach ($currentRelationshipValue as $valueRecord) {
                    $results = [
                        ...$results,
                        ...$this->getRelationshipResults(
                            $valueRecord,
                            $relationships,
                        ),
                    ];
                }

                break;
            }

            if (! $currentRelationshipValue instanceof Model) {
                break;
            }

            if (! count($relationships)) {
                $results[] = $currentRelationshipValue;

                break;
            }

            $record = $currentRelationshipValue;
        }

        return $results;
    }

    public function getRelationshipAttribute(?string $name = null): string
    {
        $name ??= $this->getName();

        if (! str($name)->contains('.')) {
            return $name;
        }

        return (string) str($name)->afterLast('.');
    }

    public function getInverseRelationshipName(Model $record): string
    {
        if (filled($this->inverseRelationshipName)) {
            return $this->inverseRelationshipName;
        }

        $inverseRelationships = [];

        foreach (explode('.', $this->getRelationshipName()) as $nestedRelationshipName) {
            $relationship = $record->{$nestedRelationshipName}();
            $record = $relationship->getRelated();

            $inverseNestedRelationshipName = (string) str(class_basename($relationship->getParent()::class))
                ->when(
                    ($relationship instanceof BelongsTo ||
                    $relationship instanceof BelongsToMany ||
                    $relationship instanceof \Znck\Eloquent\Relations\BelongsToThrough),
                    fn (Stringable $name) => $name->plural(),
                )
                ->camel();

            if (! $record->isRelation($inverseNestedRelationshipName)) {
                // The conventional relationship doesn't exist, but we can
                // attempt to use the original relationship name instead.

                if (! $record->isRelation($nestedRelationshipName)) {
                    $recordClass = $record::class;

                    throw new Exception("When trying to guess the inverse relationship for table column [{$this->getName()}], relationship [{$inverseNestedRelationshipName}] was not found on model [{$recordClass}]. Please define a custom [inverseRelationship()] for this column.");
                }

                $inverseNestedRelationshipName = $nestedRelationshipName;
            }

            array_unshift($inverseRelationships, $inverseNestedRelationshipName);
        }

        return implode('.', $inverseRelationships);
    }

    public function getRelationshipName(?string $name = null): ?string
    {
        $name ??= $this->getName();

        if (! str($name)->contains('.')) {
            return null;
        }

        return (string) str($name)->beforeLast('.');
    }
}
