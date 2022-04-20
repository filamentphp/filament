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

    public function applySearchConstraint(Builder $query, string $searchQuery, bool &$isFirst): Builder
    {
        if ($this->isHidden()) {
            return $query;
        }

        if (! $this->isSearchable()) {
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

            if (method_exists($model, 'isTranslatableAttribute') && $model->isTranslatableAttribute($searchColumnName)) {
                $whereClause = $isFirst ? 'whereRaw' : 'orWhereRaw';
                $query->{$whereClause}('LOWER('.$searchColumnName . '->"$.'.app()->getLocale().'") LIKE ?', "%{$searchQuery}%");
            }
            else {
                $query->when(
                    $this->queriesRelationships(),
                    fn($query) => $query->{"{$whereClause}Relation"}(
                        $this->getRelationshipName(),
                        $searchColumnName,
                        $searchOperator,
                        "%{$searchQuery}%",
                    ),
                    fn($query) => $query->{$whereClause}(
                        $searchColumnName,
                        $searchOperator,
                        "%{$searchQuery}%",
                    ),
                );
            }
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

        foreach (array_reverse($this->getSortColumns()) as $sortColumnName) {
            $query->when(
                $this->queriesRelationships(),
                fn ($query) => $query->orderBy(
                    $this
                        ->getRelationship($query)
                        ->getRelationExistenceQuery(
                            $this->getRelatedModel($query)->query(),
                            $query,
                            $sortColumnName,
                        )
                        ->getQuery(),
                    $direction,
                ),
                fn ($query) => $query->orderBy($sortColumnName, $direction),
            );
        }

        return $query;
    }

    public function queriesRelationships(): bool
    {
        return Str::of($this->getName())->contains('.');
    }

    protected function getRelationshipDisplayColumnName(): string
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
