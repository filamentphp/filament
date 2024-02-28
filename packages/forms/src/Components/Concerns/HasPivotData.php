<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait HasPivotData
{
    /**
     * @var array<string, mixed> | Closure
     */
    protected array | Closure $pivotData = [];

    /**
     * @param  array<string, mixed> | Closure  $data
     */
    public function pivotData(array | Closure $data): static
    {
        $this->pivotData = $data;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getPivotData(): array
    {
        return $this->evaluate($this->pivotData) ?? [];
    }
}
