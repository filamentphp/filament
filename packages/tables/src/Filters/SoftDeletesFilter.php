<?php

namespace Filament\Tables\Filters;

class SoftDeletesFilter extends TernaryFilter
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->placeholder(__('tables::table.filters.soft_deletes.placeholder'));

        $this->trueLabel(__('tables::table.filters.soft_deletes.true_label'));

        $this->falseLabel(__('tables::table.filters.soft_deletes.false_label'));

        $this->queries(
            true: fn ($query) => $query->withTrashed(),
            false: fn ($query) => $query->onlyTrashed(),
            blank: fn ($query) => $query->withoutTrashed(),
        );
    }
}
