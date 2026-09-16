<?php

namespace Filament\Tables\Components;

/**
 * The row above a content grid. It is a `TableSplit`, so its parts share the row and `grow(false)` pins one to its content.
 */
class TableContentHeader extends TableSplit
{
    protected function setUp(): void
    {
        parent::setUp();

        if (($this->childComponents['default'] ?? []) !== []) {
            return;
        }

        // The default row keeps both parts at the start, like the header the content renders itself.
        $this->schema([
            TablePageCheckbox::make()->grow(false),
            TableSortingSettings::make()->grow(false),
        ]);
    }

    public function toEmbeddedHtml(): string
    {
        $itemsHtml = $this->renderItems();

        if ($this->getTable()->isLayoutHtmlBlank($itemsHtml)) {
            return '';
        }

        return "<div {$this->getSplitAttributeBag()->class(['fi-ta-content-header'])->toHtml()}>{$itemsHtml}</div>";
    }
}
