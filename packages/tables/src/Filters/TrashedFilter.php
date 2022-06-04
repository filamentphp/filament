<?php

namespace Filament\Tables\Filters;

use Illuminate\Database\Eloquent\SoftDeletingScope;

class TrashedFilter extends TernaryFilter
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('tables::table.filters.trashed.label'));

        $this->placeholder(__('tables::table.filters.trashed.without_trashed'));

        $this->trueLabel(__('tables::table.filters.trashed.with_trashed'));

        $this->falseLabel(__('tables::table.filters.trashed.only_trashed'));

        $this->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);

        $this->queries(
            true: fn ($query) => $query->withTrashed(),
            false: fn ($query) => $query->onlyTrashed(),
            blank: fn ($query) => $query->withoutTrashed(),
        );
    }
}
