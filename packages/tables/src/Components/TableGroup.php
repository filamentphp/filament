<?php

namespace Filament\Tables\Components;

use Filament\Schemas\Components\Group;
use Filament\Tables\Components\Concerns\BelongsToTable;

/**
 * A plain `<div>` container for table layouts. Unlike `Group`, its children are
 * rendered without the schema grid wrapper, and boolean attributes are printed
 * as bare names (`x-cloak`), so it can reproduce the table's structural markup exactly.
 */
class TableGroup extends Group
{
    use BelongsToTable;

    public function toEmbeddedHtml(): string
    {
        $attributes = '';

        foreach (['id' => $this->getId(), ...$this->getExtraAttributes()] as $name => $value) {
            if (($value === false) || ($value === null)) {
                continue;
            }

            $attributes .= ($value === true) ? " {$name}" : " {$name}=\"{$value}\"";
        }

        return "<div{$attributes}>" . $this->getTable()->renderLayout($this->getChildSchema()) . '</div>';
    }
}
