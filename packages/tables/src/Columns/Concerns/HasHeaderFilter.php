<?php

namespace Filament\Tables\Columns\Concerns;

use Filament\Tables\Filters\BaseFilter;

trait HasHeaderFilter
{
    protected ?BaseFilter $headerFilter = null;

    public function headerFilter(?BaseFilter $filter): static
    {
        $this->headerFilter = $filter;

        return $this;
    }

    public function getHeaderFilter(): ?BaseFilter
    {
        return $this->headerFilter;
    }

    public function hasHeaderFilter(): bool
    {
        return $this->headerFilter !== null;
    }
}
