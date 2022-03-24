<?php

namespace Filament\Resources\Pages\ListRecords\Concerns;

use Filament\Resources\Pages\Concerns\HasActiveFormLocaleSelect;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;

trait Translatable
{
    use HasActiveFormLocaleSelect;

    public function getTableRecords(): Collection|Paginator
    {
        if ($this->records) {
            return $this->records;
        }

        $query = $this->getFilteredTableQuery();

        $this->applySortingToTableQuery($query);

        if (! $this->activeFormLocale) {
            $this->activeFormLocale = in_array(app()->getLocale(), static::getTranslatableLocales()) ? app()->getLocale() : static::getTranslatableLocales();
        }

        foreach (self::$resource::getTranslatableAttributes() as $translatableAttribute) {
            $query->where($translatableAttribute.'->'.$this->activeFormLocale, '!=', '');
        }

        $this->records = $this->isTablePaginationEnabled() ?
            $this->paginateTableQuery($query) :
            $query->get();

        if ($this->isTablePaginationEnabled()) {
            $this->records->transform(fn ($item) => $item->setLocale($this->activeFormLocale));
        } else {
            $this->records->map(fn ($record) => $record->setLocale($this->activeFormLocale));
        }

        return $this->records;
    }

    protected function getActions(): array
    {
        return array_merge([
            $this->getActiveFormLocaleSelectAction(),
        ], parent::getActions());
    }
}
