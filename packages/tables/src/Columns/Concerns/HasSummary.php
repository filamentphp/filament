<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;

trait HasSummary
{
    protected string | Closure | null $summary = null;

    public function summary(Closure | string $summary): static
    {
        $this->summary = $summary;

        return $this;
    }

    public function getSummary(array $records): ?string
    {
        if (class_exists($this->summary)) {
            return (new $this->summary($this, $records))();
        }

        return $this->evaluate($this->summary, [
            'records' => $records,
            'column' => $this,
        ]); //TODO: sprawdzic czy $records jest widoczne uzywajac callbackow
    }

    public function hasSummary(): bool
    {
        return $this->summary !== null;
    }
}
