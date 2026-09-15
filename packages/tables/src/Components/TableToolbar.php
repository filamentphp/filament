<?php

namespace Filament\Tables\Components;

class TableToolbar extends TableGroup
{
    protected bool $hasDefaultItems = false;

    protected function setUp(): void
    {
        parent::setUp();

        if (($this->childComponents['default'] ?? []) !== []) {
            return;
        }

        $this->hasDefaultItems = true;

        $this->schema([
            TableReorderTrigger::make(),
            TableToolbarActions::make(),
            TableGroupingSettings::make(),
            TableSearch::make(),
            TableFiltersTrigger::make(),
            TableColumnManager::make(),
        ]);
    }

    public function hasDefaultItems(): bool
    {
        return $this->hasDefaultItems;
    }

    public function toEmbeddedHtml(): string
    {
        return view('filament-tables::components.parts.toolbar', [
            'table' => $this->getTable(),
            'part' => $this,
        ])->render();
    }
}
