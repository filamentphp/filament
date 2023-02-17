<?php

namespace Filament\Tables\Columns;

use Closure;

class ProgressColumn extends Column
{
    use Concerns\HasColors;

    protected string $view = 'tables::columns.progress-column';

    protected string | Closure $color = 'primary';

    protected ?Closure $progress = null;

    public function progress(Closure $callback): static
    {
        $this->progress = $callback;

        return $this;
    }

    public function getProgress(): int|float
    {
        if ($this->progress === null) {
            return floor($this->getStateFromRecord());
        }

        return $this->evaluate($this->progress);
    }
}