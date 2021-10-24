<?php

namespace Filament\Tables\Columns\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

trait InteractsWithTableQuery
{
    public function applyEagreLoading(Builder $query): Builder
    {
        if ($this->queriesRelationships()) {
            $query->with([$this->getRelationshipName()]);
        }

        return $query;
    }

    public function applySearchConstraint(Builder $query, string $searchQuery, bool &$isFirst): Builder
    {
        if (! $this->isSearchable()) {
            return $query;
        }

        foreach ($this->getSearchColumns() as $searchColumnName) {
            if ($this->queriesRelationships()) {
                $query->{$isFirst ? 'whereHas' : 'orWhereHas'}(
                    $this->getRelationshipName(),
                    fn ($query) => $query->where(
                        $searchColumnName,
                        $this->getQuerySearchOperator($query),
                        "%{$searchQuery}%",
                    ),
                );
            } else {
                $query->{$isFirst ? 'where' : 'orWhere'}(
                    $searchColumnName,
                    $this->getQuerySearchOperator($query),
                    "%{$searchQuery}%"
                );
            }

            $isFirst = false;

            return $query;
        }
    }

    public function queriesRelationships(): bool
    {
        return Str::of($this->getName())->contains('.');
    }

    protected function getRelationshipColumnName(): string
    {
        return Str::of($this->getName())->afterLast('.');
    }

    protected function getRelationshipName(): string
    {
        return Str::of($this->getName())->beforeLast('.');
    }

    protected function getQuerySearchOperator(Builder $query): string
    {
        return [
            'pgsql' => 'ilike',
        ][$query->getConnection()->getDriverName()] ?? 'like';
    }
}
