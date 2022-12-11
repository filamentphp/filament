<?php

namespace Filament\Tables\Columns\Concerns;

use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

trait InteractsWithTableQuery
{
    protected ?string $inverseRelationshipName = null;

    public function inverseRelationship(?string $name): static
    {
        $this->inverseRelationshipName = $name;

        return $this;
    }

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
        if (! $this->queriesRelationships($query->getModel())) {
            return $query;
        }

        return $query->with([$this->getRelationshipName()]);
    }

    public function applySearchConstraint(Builder $query, string $search, bool &$isFirst): Builder
    {
        if ($this->searchQuery) {
            $this->evaluate($this->searchQuery, [
                'query' => $query,
                'search' => $search,
                'searchQuery' => $search,
            ]);

            $isFirst = false;

            return $query;
        }

        /** @var Connection $databaseConnection */
        $databaseConnection = $query->getConnection();

        $searchOperator = match ($databaseConnection->getDriverName()) {
            'pgsql' => 'ilike',
            default => 'like',
        };

        $model = $query->getModel();

        foreach ($this->getSearchColumns() as $searchColumn) {
            $whereClause = $isFirst ? 'where' : 'orWhere';

            $query->when(
                method_exists($model, 'isTranslatableAttribute') && $model->isTranslatableAttribute($searchColumn),
                function (Builder $query) use ($searchColumn, $searchOperator, $search, $whereClause, $databaseConnection): Builder {
                    $activeLocale = $this->getLivewire()->getActiveTableLocale() ?: app()->getLocale();

                    $searchColumn = match ($databaseConnection->getDriverName()) {
                        'pgsql' => "{$searchColumn}->>'{$activeLocale}'",
                        default => "json_extract({$searchColumn}, \"$.{$activeLocale}\")",
                    };

                    return $query->{"{$whereClause}Raw"}(
                        "lower({$searchColumn}) {$searchOperator} ?",
                        "%{$search}%",
                    );
                },
                fn (Builder $query): Builder => $query->when(
                    $this->queriesRelationships($query->getModel()),
                    fn (Builder $query): Builder => $query->{"{$whereClause}Relation"}(
                        $this->getRelationshipName(),
                        $searchColumn,
                        $searchOperator,
                        "%{$search}%",
                    ),
                    fn (Builder $query): Builder => $query->{$whereClause}(
                        $searchColumn,
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
        if ($this->sortQuery) {
            $this->evaluate($this->sortQuery, [
                'direction' => $direction,
                'query' => $query,
            ]);

            return $query;
        }

        // $$$ hugh
        foreach (array_reverse($this->getSortColumns()) as $sortColumn) {
            $query->orderBy(
                $this->getNestedRelationExistenceQuery($query, $sortColumn),
                $direction
            );
        }

        return $query;
    }

    public function getRelationship(Model $record): ?Relation
    {
        return $this->getLastRelationship($record);
    }

    public function getInverseRelationshipName(): ?string
    {
        return $this->inverseRelationshipName;
    }
}
