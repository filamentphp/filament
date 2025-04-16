<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Illuminate\Support\Arr;

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

    protected static function isPersonalPivotData(array $data, array $state): bool
    {
        if (count($data) !== count($state)) return false;
        if (collect($state)->some(fn($key) => !array_key_exists($key, $data))) return false;
        if (collect($data)->some(fn($value) => !is_array($value) || !Arr::isAssoc($value))) return false;

        return true;
    }
}
