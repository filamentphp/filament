<?php

namespace Filament\Tables\Concerns;

use Closure;
use Filament\Support\Services\RelationshipJoiner;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Str;
use stdClass;

trait CanSummarizeRecords
{
    /**
     * @param  Model | array<string, mixed> | null  $lastRecord
     */
    public function shouldRenderTrailingGroupedTableSummary(Model | array | null $lastRecord): bool
    {
        if ($lastRecord === null) {
            return false;
        }

        $records = $this->getTableRecords();

        if ((! $records instanceof Paginator) || (! $records->hasMorePages())) {
            return true;
        }

        $group = $this->getTableGrouping();

        if (! $group) {
            return true;
        }

        $query = $this->getFilteredSortedTableQuery();

        if ($query === null) {
            return true;
        }

        $nextPageFirstRecord = (clone $query)
            ->skip($records->currentPage() * $records->perPage())
            ->first();

        if ($nextPageFirstRecord === null) {
            return true;
        }

        return $group->getStringKey($nextPageFirstRecord) !== $group->getStringKey($lastRecord);
    }

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

        foreach ($this->getTable()->getVisibleColumns() as $column) {
            $summarizers = $column->getSummarizers($query);

            if (! count($summarizers)) {
                continue;
            }

            if ($column->hasRelationship($query->getModel())) {
                continue;
            }

            $qualifiedAttribute = $query->getModel()->qualifyColumn($column->getName());

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
