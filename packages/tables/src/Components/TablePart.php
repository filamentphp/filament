<?php

namespace Filament\Tables\Components;

use Filament\Schemas\Components\Component;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Tables\Components\Concerns\BelongsToTable;

abstract class TablePart extends Component implements HasEmbeddedView
{
    use BelongsToTable;

    public static function make(): static
    {
        $static = app(static::class);
        $static->configure();

        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->liberatedFromContainerGrid();
    }

    abstract protected function getPartView(): string;

    public function toEmbeddedHtml(): string
    {
        return view($this->getPartView(), [
            'table' => $this->getTable(),
            'part' => $this,
        ])->render();
    }
}
