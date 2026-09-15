<?php

namespace Filament\Tables\Components;

class TablePagination extends TableStack
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
            TablePaginationOverview::make(),
            TablePaginationRecordsPerPage::make(),
            TablePaginationLinks::make(),
        ]);
    }

    public function hasDefaultItems(): bool
    {
        return $this->hasDefaultItems;
    }

    public function toEmbeddedHtml(): string
    {
        return view('filament-tables::components.parts.pagination', [
            'table' => $this->getTable(),
            'part' => $this,
        ])->render();
    }
}
