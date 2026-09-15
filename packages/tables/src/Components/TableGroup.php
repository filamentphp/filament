<?php

namespace Filament\Tables\Components;

use Filament\Schemas\Components\Group;
use Filament\Schemas\Concerns\CanBeInline;
use Filament\Tables\Components\Concerns\BelongsToTable;

/**
 * A plain `<div>` container for table layouts. Unlike `Group`, its children are
 * rendered without the schema grid wrapper, and boolean attributes are printed
 * as bare names (`x-cloak`), so it can reproduce the table's structural markup exactly.
 */
class TableGroup extends Group
{
    use BelongsToTable;
    use CanBeInline;

    public function toEmbeddedHtml(): string
    {
        $attributes = ['id' => $this->getId(), ...$this->getExtraAttributes()];

        $classes = array_filter([
            $attributes['class'] ?? null,
            // A group is a block by default, so parts stack; `inline()` lays them out as a row, like the toolbar does with its groups.
            $this->isInline() ? 'fi-ta-group fi-inline' : null,
            // The same `grow()` as a `Split` child: a growing group takes the free space of its row, which pushes the groups after it to the end.
            $this->canGrow(default: false) ? 'fi-growable' : null,
        ]);

        if ($classes !== []) {
            $attributes['class'] = implode(' ', $classes);
        }

        $html = '';

        foreach ($attributes as $name => $value) {
            if (($value === false) || ($value === null)) {
                continue;
            }

            $html .= ($value === true) ? " {$name}" : " {$name}=\"{$value}\"";
        }

        return "<div{$html}>" . $this->getTable()->renderLayout($this->getChildSchema()) . '</div>';
    }
}
