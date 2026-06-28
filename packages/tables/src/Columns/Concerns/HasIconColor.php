<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Model;

trait HasIconColor
{
    /**
     * @var string | array<string> | Closure | null
     */
    protected string | array | Closure | null $iconColor = null;

    /**
     * @param  string | array<string> | Closure | null  $color
     */
    public function iconColor(string | array | Closure | null $color): static
    {
        $this->iconColor = $color;

        return $this;
    }

    /**
     * @return string | array<int | string, string | int> | null
     */
    public function getIconColor(mixed $state, ?Model $relationshipRecord = null): string | array | null
    {
        return $this->evaluateForStateItem($this->iconColor, $state, $relationshipRecord);
    }
}
