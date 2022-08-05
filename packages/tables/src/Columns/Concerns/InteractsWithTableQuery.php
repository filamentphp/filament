<?php

namespace Filament\Tables\Columns\Concerns;

use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;

trait InteractsWithTableQuery
{
    public function applyRelationshipAggregates(Builder $query): Builder
    {
        return $query->when(
            filled([$this->getRelationshipToAvg(), $this->getColumnToAvg()]),
            fn ($query) => $query->withAvg($this->getRelationshipToAvg(), $this->getColumnToAvg())
        )->when(
            filled($this->getRelationshipToCount()),
            fn ($query) => $query->withCount([$this->getRelationshipToCount()])
        )->when(
            filled($this->getRelationshipToExistenceCheck()),
            fn ($query) => $query->withExists($this->getRelationshipToExistenceCheck())
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

    public function applyEagerLoading(Builder $query): Builder
    {
        if ($this->isHidden()) {
            return $query;
        }

        if ($this->queriesRelationships()) {
            $query->with([$this->getRelationshipName()]);
        }

        return $query;
    }

    public function applySearchConstraint(Builder $query, string $search, bool &$isFirst): Builder
    {
        if ($this->isHidden()) {
            return $query;
        }

        if (! $this->isSearchable()) {
            return $query;
        }

        if ($this->searchQuery) {
            $this->evaluate($this->searchQuery, [
                'query' => $query,
                'search' => $search,
            ]);

            return $query;
        }

        /** @var Connection $databaseConnection */
        $databaseConnection = $query->getConnection();

        $searchOperator = match ($databaseConnection->getDriverName()) {
            'pgsql' => 'ilike',
            default => 'like',
        };

        $model = $query->getModel();

        foreach ($this->getSearchColumns() as $searchColumnName) {
            $whereClause = $isFirst ? 'where' : 'orWhere';

            $query->when(
                method_exists($model, 'isTranslatableAttribute') && $model->isTranslatableAttribute($searchColumnName),
                function (Builder $query) use ($searchColumnName, $searchOperator, $search, $whereClause, $databaseConnection): Builder {
                    $activeLocale = $this->getLivewire()->getActiveTableLocale() ?: app()->getLocale();

                    $searchColumn = match ($databaseConnection->getDriverName()) {
                        'pgsql' => "{$searchColumnName}->>'{$activeLocale}'",
                        default => "json_extract({$searchColumnName}, \"$.{$activeLocale}\")",
                    };

                    return $query->{"{$whereClause}Raw"}(
                        "lower({$searchColumn}) {$searchOperator} ?",
                        "%{$search}%",
                    );
                },
                fn (Builder $query): Builder => $query->when(
                    $this->queriesRelationships(),
                    fn (Builder $query): Builder => $query->{"{$whereClause}Relation"}(
                        $this->getRelationshipName(),
                        $searchColumnName,
                        $searchOperator,
                        "%{$search}%",
                    ),
                    fn (Builder $query): Builder => $query->{$whereClause}(
                        $searchColumnName,
                        $searchOperator,
                        "%{$search}%",
                    ),
                ),
            );

            $isFirst = false;
        }

        return $query;
    }

    public function applySort(Builder $query, string $direction = 'asc'): Builder
    {
        if ($this->isHidden()) {
            return $query;
        }

        if (! $this->isSortable()) {
            return $query;
        }

        if ($this->sortQuery) {
            $this->evaluate($this->sortQuery, [
                'direction' => $direction,
                'query' => $query,
            ]);

            return $query;
        }

        foreach (array_reverse($this->getSortColumns()) as $sortColumn) {
            $query->when(
                $this->queriesRelationships(),
                fn ($query) => $query->orderBy(
                    $this
                        ->getRelationship($query)
                        ->getRelationExistenceQuery(
                            $this->getRelatedModel($query)->query(),
                            $query,
                            $sortColumn,
                        )
                        ->getQuery(),
                    $direction,
                ),
                fn ($query) => $query->orderBy($sortColumn, $direction),
            );
        }

        return $query;
    }

    public function queriesRelationships(): bool
    {
        return Str::of($this->getName())->contains('.') && $this->getRecord()?->isRelation($this->getRelationshipName());
    }

    protected function getRelationshipTitleColumnName(): string
    {
        return (string) Str::of($this->getName())->afterLast('.');
    }

    protected function getRelatedModel(Builder $query): Model
    {
        return $this->getRelationship($query)->getModel();
    }

    protected function getRelationship(Builder $query): Relation | Builder
    {
        return $this->getQueryModel($query)->{$this->getRelationshipName()}();
    }

    protected function getRelationshipName(): string
    {
        return (string) Str::of($this->getName())->beforeLast('.');
    }

    protected function getQueryModel(Builder $query): Model
    {
        return $query->getModel();
    }
}
