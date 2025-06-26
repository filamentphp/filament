<?php

namespace Filament\Tables\Columns\Summarizers\Concerns;

use Closure;

trait CanBeHidden
{
    protected bool | Closure $isHidden = false;

    protected bool | Closure $isVisible = true;

    /**
     * @var array<string, bool>
     */
    protected array $visibilityCache = [];

    public function hidden(bool | Closure $condition = true): static
    {
        $this->isHidden = $condition;

        return $this;
    }

    public function visible(bool | Closure $condition = true): static
    {
        $this->isVisible = $condition;

        return $this;
    }

    public function isHidden(): bool
    {
        $query = $this->getQuery();
        $querySql = $query ? md5($query->toRawSql()) : '';

        if (array_key_exists($querySql, $this->visibilityCache)) {
            return $this->visibilityCache[$querySql];
        }

        if ($this->evaluate($this->isHidden)) {
            return $this->visibilityCache[$querySql] = true;
        }

        return $this->visibilityCache[$querySql] = ! $this->evaluate($this->isVisible);
    }

    public function isVisible(): bool
    {
        return ! $this->isHidden();
    }
}
