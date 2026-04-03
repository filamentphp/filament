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
        // Check if we have pivot columns selected (`BelongsToMany` `RelationManager` context)
        $hasPivotColumns = collect($query->getQuery()->getColumns())
            ->contains(fn (string $column): bool => str($column)->contains(' as pivot_'));

        // If we have pivot columns, remove the join table's wildcard to prevent
        // duplicate column errors (e.g., both tables have `id`) when the query
        // is used as a subquery in MySQL. Only the join table's wildcard is removed
        // so that non-pivot columns from the related model remain accessible.
        if ($hasPivotColumns && ($joinTable = ($query->getQuery()->joins[0]->table ?? null))) {
            $query->getQuery()->columns = array_filter(
                $query->getQuery()->columns,
                fn (mixed $column): bool => ! is_string($column) || $column !== "{$joinTable}.*",
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
            // Check if this column is actually a pivot column by looking for its alias.
            // Handle both `pivot.amount_total` (explicit) and `quantity` (implicit) column names.
            $pivotAlias = str($columnName)->startsWith('pivot.')
                ? (string) str($columnName)->after('pivot.')->prepend('pivot_')
                : 'pivot_' . $columnName;
            $isPivotColumn = $hasPivotColumns && collect($query->getQuery()->getColumns())
                ->contains(fn (string $col): bool => str($col)->endsWith(" as {$pivotAlias}"));

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
