<?php

namespace Filament\Tables\Components;

class TableContentHeader extends TableStack
{
    protected function setUp(): void
    {
        parent::setUp();

        if (($this->childComponents['default'] ?? []) !== []) {
            return;
        }

        $this->schema([
            TablePageCheckbox::make(),
            TableSortingSettings::make(),
        ]);
    }

    public function toEmbeddedHtml(): string
    {
        return view('filament-tables::components.parts.content-header', [
            'table' => $this->getTable(),
            'part' => $this,
        ])->render();
    }
}
