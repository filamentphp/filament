<?php

namespace Filament\Tables\Filters;

use Illuminate\Database\Eloquent\Builder;

class SoftDeletesFilter extends TernaryFilter
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->placeholder(__('tables::table.filters.soft_deletes.placeholder'));

        $this->trueLabel(__('tables::table.filters.soft_deletes.true_label'));

        $this->falseLabel(__('tables::table.filters.soft_deletes.false_label'));

        $this->queries(
            true: fn (Builder $query) => $query->withTrashed(),
            false: fn (Builder $query) => $query->onlyTrashed(),
            blank: fn (Builder $query) => $query->withoutTrashed(),
        );
    }
}
