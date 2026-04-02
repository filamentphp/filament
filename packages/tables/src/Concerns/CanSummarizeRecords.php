<?php

namespace Filament\Tables\Concerns;

use Closure;
use Filament\Support\Services\RelationshipJoiner;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Str;
use stdClass;

trait CanSummarizeRecords
{
    public function getAllTableSummaryQuery(): ?Builder
    {
        return $this->getFilteredTableQuery();
    }

    public function getPageTableSummaryQuery(): ?Builder
    {
        return $this->getFilteredSortedTableQuery()?->forPage(
            page: $this->getTableRecords()->currentPage(),
            perPage: $this->getTableRecords()->perPage(),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function getTableSummarySelectedState(?Builder $query = null, ?Closure $modifyQueryUsing = null): array
    {
        if (! $query) {
            return [];
        }

        $selects = [];

        // https://github.com/filamentphp/filament/issues/19594
        // Check if we have pivot columns selected (BelongsToMany RelationManager context)
        $queryColumns = $query->getQuery()->getColumns() ?? [];
        $hasPivotColumns = collect($queryColumns)->contains(
            fn (mixed $column): bool => is_string($column) && str($column)->contains(' as pivot_'),
        );

        // If we have pivot columns, remove wildcards to prevent duplicate column errors
        // when the query is used as a subquery in MySQL.
        if ($hasPivotColumns) {
            $query->getQuery()->columns = array_filter(
                $queryColumns,
                fn (mixed $column): bool => ! is_string($column) || ! str($column)->endsWith('.*'),
            );
        }

        foreach ($this->getTable()->getVisibleColumns() as $column) {
            $summarizers = $column->getSummarizers($query);

            if (! count($summarizers)) {
                continue;
            }

            if ($column->hasRelationship($query->getModel())) {
                continue;
            }

            $columnName = $column->getName();

            // https://github.com/filamentphp/filament/issues/19594
            // Check if this column is actually a pivot column by looking for its alias
            $pivotAlias = 'pivot_' . $columnName;
            $isPivotColumn = $hasPivotColumns && collect($query->getQuery()->getColumns())
                ->contains(fn (mixed $col): bool => is_string($col) && str($col)->endsWith(" as {$pivotAlias}"));

            // Use the pivot alias if this is a pivot column, otherwise qualify with the model's table
            $qualifiedAttribute = $isPivotColumn
                ? $pivotAlias
                : $query->getModel()->qualifyColumn($columnName);

            foreach ($summarizers as $summarizer) {
                if ($summarizer->hasQueryModification()) {
                    continue;
                }

                $selectStatements = $summarizer
                    ->query($query)
                    ->getSelectStatements($qualifiedAttribute);

                foreach ($selectStatements as $alias => $statement) {
                    $selects[] = "{$statement} as \"{$alias}\"";
                }
            }
        }

        if (! count($selects)) {
            return [];
        }

        $queryToJoin = $query->clone();
        $joins = [];

        $query = $query->getModel()->resolveConnection($query->getModel()->getConnectionName())
            ->table($query->toBase(), $query->getModel()->getTable());

        if ($modifyQueryUsing) {
            $query = $modifyQueryUsing($query) ?? $query;
        }

        $group = $query->groups[0] ?? null;
        $groupSelectAlias = null;

        if ($group !== null) {
            $groupSelectAlias = Str::random();

            if ($group instanceof Expression) {
                $group = $group->getValue($query->getGrammar());
            }

            $selects[] = "{$group} as \"{$groupSelectAlias}\"";

            if (filled($groupingRelationshipName = $this->getTableGrouping()?->getRelationshipName())) {
                $joins = app(RelationshipJoiner::class)->getLeftJoinsForRelationship(
                    query: $queryToJoin,
                    relationship: $groupingRelationshipName,
                );
            }
        }

        $query->joins = [
            ...($query->joins ?? []),
            ...$joins,
        ];

        return $query
            ->selectRaw(implode(', ', $selects))
            ->get()
            ->mapWithKeys(function (stdClass $state, $key) use ($groupSelectAlias): array {
                if ($groupSelectAlias !== null) {
                    $key = $state->{$groupSelectAlias};

                    unset($state->{$groupSelectAlias});
                }

                return [$key => (array) $state];
            })
            ->all();
    }
}
