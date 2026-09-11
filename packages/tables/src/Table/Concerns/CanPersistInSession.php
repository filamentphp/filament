<?php

namespace Filament\Tables\Table\Concerns;

use Closure;

trait CanPersistInSession
{
    public function persistInSession(bool | Closure $condition = true): static
    {
        $this->persistFiltersInSession($condition);
        $this->persistSearchInSession($condition);
        $this->persistColumnSearchesInSession($condition);
        $this->persistSortInSession($condition);
        $this->persistGroupInSession($condition);
        $this->persistColumnsInSession($condition);

        return $this;
    }
}
