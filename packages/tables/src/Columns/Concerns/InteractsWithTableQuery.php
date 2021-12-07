<?php

namespace Filament\Tables\Columns\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;

trait InteractsWithTableQuery
{
    public function applyEagreLoading(Builder $query): Builder
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

        $searchOperator = match ($query->getConnection()->getDriverName()) {
            'pgsql' => 'ilike',
            default => 'like',
        };

        foreach ($this->getSearchColumns() as $searchColumnName) {
            if ($this->queriesRelationships()) {
                $query->{$isFirst ? 'whereHas' : 'orWhereHas'}(
                    $this->getRelationshipName(),
                    fn ($query) => $query->where(
                        $searchColumnName,
                        $searchOperator,
                        "%{$searchQuery}%",
                    ),
                );
            } else {
                $query->{$isFirst ? 'where' : 'orWhere'}(
                    $searchColumnName,
                    $searchOperator,
                    "%{$searchQuery}%"
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
            if ($this->queriesRelationships()) {
                $relatedModel = $this->getRelatedModel($query);
                $relationship = $this->getRelationship($query);

                $query->orderBy(
                    $relatedModel
                        ->query()
                        ->select($sortColumnName)
                        ->whereColumn(
                            "{$relatedModel->getTable()}.{$relationship->getOwnerKeyName()}",
                            "{$this->getQueryModel($query)->getTable()}.{$relationship->getForeignKeyName()}",
                        ),
                    $direction,
                );
            } else {
                $query->orderBy(
                    $sortColumnName,
                    $direction,
                );
            }
        }

        return $query;
    }

    public function queriesRelationships(): bool
    {
        return Str::of($this->getName())->contains('.');
    }

    protected function getRelatedModel(Builder $query): Model
    {
        return $this->getRelationship($query)->getModel();
    }

    protected function getRelationship(Builder $query): Relation
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
