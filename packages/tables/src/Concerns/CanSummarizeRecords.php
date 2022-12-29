<?php

namespace Filament\Tables\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use stdClass;

trait CanSummarizeRecords
{
    public function getAllTableSummaryQuery(): Builder
    {
        return $this->getFilteredTableQuery();
    }

    public function getPageTableSummaryQuery(): Builder
    {
        return $this->getFilteredSortedTableQuery()->forPage(
            page: $this->getTableRecords()->currentPage(),
            perPage: $this->getTableRecords()->perPage(),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function getTableSummarySelectedState(Builder $query, ?Closure $modifyQueryUsing = null): array
    {
        $selects = [];

        foreach ($this->getTable()->getVisibleColumns() as $column) {
            $summarizers = $column->getSummarizers();

            if (! count($summarizers)) {
                continue;
            }

            if (filled($column->getRelationshipName())) {
                continue;
            }

            $qualifiedAttribute = $query->getModel()->qualifyColumn($column->getName());

            foreach ($summarizers as $summarizer) {
                $selectStatements = $summarizer
                    ->query($query)
                    ->getSelectStatements($qualifiedAttribute);

                foreach ($selectStatements as $alias => $statement) {
                    $selects[] = "{$statement} as '{$alias}'";
                }
            }
        }

        if (! count($selects)) {
            return [];
        }

        $query = DB::table($query->toBase(), $query->getModel()->getTable());

        if ($modifyQueryUsing) {
            $query = $modifyQueryUsing($query) ?? $query;
        }

        $group = $query->groups[0] ?? null;

        if ($group !== null) {
            $selects[] = $group;
        }

        return $query
            ->selectRaw(implode(', ', $selects))
            ->get()
            ->mapWithKeys(function (stdClass $state, $key) use ($group): array {
                if ($group !== null) {
                    $key = $state->{$group};

                    unset($state->{$group});
                }

                return [$key => (array) $state];
            })
            ->all();
    }
}
