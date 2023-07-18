<?php

namespace Filament\Tables\Columns;

use Closure;

/**
 * @deprecated Use `TextColumn` instead.
 */
class TagsColumn extends TextColumn
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->badge();
    }

    /**
     * @deprecated Use `limitList()` instead.
     */
    public function limit(int | Closure | null $length = 3, string | Closure | null $end = null): static
    {
        $this->limitList($length);

        return $this;
    }
}
