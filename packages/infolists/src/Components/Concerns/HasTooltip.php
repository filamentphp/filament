<?php

namespace Filament\Infolists\Components\Concerns;

use Closure;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

trait HasTooltip
{
    protected string | Htmlable | Closure | null $tooltip = null;

    protected string | Htmlable | Closure | null $emptyTooltip = null;

    public function tooltip(string | Htmlable | Closure | null $tooltip): static
    {
        $this->tooltip = $tooltip;

        return $this;
    }

    public function getTooltip(mixed $state = null, ?Model $relatedRecord = null): string | Htmlable | null
    {
        return $this->evaluate($this->tooltip, [
            'state' => $state,
            'relatedRecord' => $relatedRecord,
        ]);
    }

    public function emptyTooltip(string | Htmlable | Closure | null $tooltip): static
    {
        $this->emptyTooltip = $tooltip;

        return $this;
    }

    public function getEmptyTooltip(): string | Htmlable | null
    {
        return $this->evaluate($this->emptyTooltip);
    }
}
