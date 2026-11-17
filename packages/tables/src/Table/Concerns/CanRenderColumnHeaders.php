<?php

namespace Filament\Tables\Table\Concerns;

use Filament\Support\Enums\Alignment;
use Filament\Support\Facades\FilamentView;
use Filament\Support\Icons\Heroicon;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Enums\RecordCheckboxPosition;
use Filament\Tables\Table;
use Filament\Tables\View\TablesIconAlias;
use Filament\Tables\View\TablesRenderHook;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Js;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\ComponentSlot;

trait CanRenderColumnHeaders
{
    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function getRenderColumnsCount(): int
    {
        return count($this->getVisibleColumns())
            + (int) ($this->hasRenderRecordActions() && (! $this->isReordering()))
            + (int) ($this->isRenderSelectionEnabled() || $this->isReordering());
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     *
     * @param  array<string, Column>  $sortableColumns
     */
    protected function generateTableHeaderHtml(array $sortableColumns): string
    {
        $records = $this->getRecords();
        $livewire = $this->getLivewire();
        $recordActionsColumnLabel = $this->getRecordActionsColumnLabel();

        ob_start(); ?>
        <thead>
        <?= $this->generateStackedHeaderHtml($sortableColumns); ?>

        <?= $this->generateColumnGroupsHtml(); ?>

            <tr>
        <?= $this->renderMorphMarkerHtml(); ?>                                    <?php if (count($records)) { ?>
                    <?php if ($this->isReordering()) { ?>
                        <th></th>
                    <?php } else { ?>
                        <?= $this->renderMorphMarkerHtml(); ?>
                        <?php if ($this->hasRenderRecordActions() && $this->getRecordActionsPosition() === RecordActionsPosition::BeforeCells) { ?>
                            <?php if ($recordActionsColumnLabel) { ?>
                                <th
                                    scope="col"
                                    class="fi-ta-header-cell"
                                >
                                    <?= e($recordActionsColumnLabel); ?>

                                </th>
                            <?php } else { ?>
                                <th
                                    aria-label="<?= e(trans_choice('filament-tables::table.columns.actions.label', count($this->getFlatRecordActions()))); ?>"
                                    scope="col"
                                    class="fi-ta-actions-header-cell fi-ta-empty-header-cell"
                                ></th>
                            <?php } ?>
                        <?php } ?>
                        <?= $this->renderMorphMarkerHtml(isEnd: true); ?>

                        <?= $this->renderMorphMarkerHtml(); ?>
                        <?php if ($this->isRenderSelectionEnabled() && $this->getRecordCheckboxPosition() === RecordCheckboxPosition::BeforeCells) { ?>
                            <th
                                scope="col"
                                class="fi-ta-cell fi-ta-selection-cell"
                            >
                                <?php if (($this->getMaxSelectableRecords() !== 1) && (! $this->selectsGroupsOnly())) { ?>
                                    <?= $this->generatePageCheckboxHtml(); ?>

                                <?php } ?>
                            </th>
                        <?php } ?>
                        <?= $this->renderMorphMarkerHtml(isEnd: true); ?>

                        <?php if ($this->hasRenderRecordActions() && $this->getRecordActionsPosition() === RecordActionsPosition::BeforeColumns) { ?>
                            <?php if ($recordActionsColumnLabel) { ?>
                                <th
                                    scope="col"
                                    class="fi-ta-header-cell"
                                >
                                    <?= e($recordActionsColumnLabel); ?>

                                </th>
                            <?php } else { ?>
                                <th
                                    aria-label="<?= e(trans_choice('filament-tables::table.columns.actions.label', count($this->getFlatRecordActions()))); ?>"
                                    scope="col"
                                    class="fi-ta-actions-header-cell fi-ta-empty-header-cell"
                                ></th>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                <?php } ?><?= $this->renderMorphMarkerHtml(isEnd: true); ?>

                <?php
                    $hasHeaderCellRenderHook = FilamentView::hasRenderHook(TablesRenderHook::HEADER_CELL, scopes: $livewire::class);
        ?>

        <?= $this->renderMorphMarkerHtml(); ?>                                    <?php foreach ($this->iterateRenderLoop($this->getVisibleColumns()) as $column) {  ?>
                    <?php if ($hasHeaderCellRenderHook && filled($headerCellView = FilamentView::renderHook(TablesRenderHook::HEADER_CELL, scopes: $livewire::class, data: [
                        'column' => $column,
                        'isReordering' => $this->isReordering(),
                    ]))) { ?>
                        <?= e($headerCellView); ?>

                    <?php } else { ?>
                        <?= $this->generateColumnHeaderHtml($column); ?>

                    <?php } ?>
                <?php }  ?><?= $this->renderMorphMarkerHtml(isEnd: true); ?>

                <?php if ((! $this->isReordering()) && count($records)) { ?>
                    <?= $this->renderMorphMarkerHtml(); ?>
                    <?php if ($this->hasRenderRecordActions() && $this->getRecordActionsPosition() === RecordActionsPosition::AfterColumns) { ?>
                        <?php if ($recordActionsColumnLabel) { ?>
                            <th
                                scope="col"
                                class="fi-ta-header-cell fi-align-end"
                            >
                                <?= e($recordActionsColumnLabel); ?>

                            </th>
                        <?php } else { ?>
                            <th
                                aria-label="<?= e(trans_choice('filament-tables::table.columns.actions.label', count($this->getFlatRecordActions()))); ?>"
                                scope="col"
                                class="fi-ta-actions-header-cell fi-ta-empty-header-cell"
                            ></th>
                        <?php } ?>
                    <?php } ?>
                    <?= $this->renderMorphMarkerHtml(isEnd: true); ?>

                    <?= $this->renderMorphMarkerHtml(); ?>
                    <?php if ($this->isRenderSelectionEnabled() && $this->getRecordCheckboxPosition() === RecordCheckboxPosition::AfterCells) { ?>
                        <th
                            scope="col"
                            class="fi-ta-cell fi-ta-selection-cell"
                        >
                            <?php if (($this->getMaxSelectableRecords() !== 1) && (! $this->selectsGroupsOnly())) { ?>
                                <?= $this->generatePageCheckboxHtml(); ?>

                            <?php } ?>
                        </th>
                    <?php } ?>
                    <?= $this->renderMorphMarkerHtml(isEnd: true); ?>

                    <?php if ($this->hasRenderRecordActions() && $this->getRecordActionsPosition() === RecordActionsPosition::AfterCells) { ?>
                        <?php if ($recordActionsColumnLabel) { ?>
                            <th
                                scope="col"
                                class="fi-ta-header-cell fi-align-end"
                            >
                                <?= e($recordActionsColumnLabel); ?>

                            </th>
                        <?php } else { ?>
                            <th
                                aria-label="<?= e(trans_choice('filament-tables::table.columns.actions.label', count($this->getFlatRecordActions()))); ?>"
                                scope="col"
                                class="fi-ta-actions-header-cell fi-ta-empty-header-cell"
                            ></th>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
            </tr>
        </thead>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     *
     * @param  array<string, Column>  $sortableColumns
     */
    protected function generateStackedHeaderHtml(array $sortableColumns): string
    {
        ob_start(); ?>
        <?= $this->renderMorphMarkerHtml(); ?>                                <?php if ($this->isStackedOnMobile() && (count($sortableColumns) || ($this->isRenderSelectionEnabled() && ($this->getMaxSelectableRecords() !== 1) && (! $this->selectsGroupsOnly()))) && (! $this->isReordering())) { ?>
            <tr class="fi-ta-table-stacked-header-row">
                <td
                    colspan="100%"
                    class="fi-ta-table-stacked-header-cell"
                >
                    <?php if (count($sortableColumns)) { ?>
                        <div
                            x-data="filamentTableSort"
                            class="fi-ta-table-stacked-sorting"
                        >
                            <label>
                                <?php $inputWrapperArguments = ['prefix' => __('filament-tables::table.sorting.fields.column.label'), 'attributes' => new ComponentAttributeBag([])];
                        ob_start(); ?>
                                    <?php $inputSelectArguments = ['attributes' => new ComponentAttributeBag(['x-model' => 'column'])];
                        ob_start(); ?>
                                        <option
                                            value=""
                                        >
                                            <?= e($this->getDefaultSortOptionLabel()); ?>

                                        </option>

                                        <?php foreach ($this->iterateRenderLoop($sortableColumns) as $sortableColumn) {  ?>
                                            <option
                                                value="<?= e($sortableColumn->getName()); ?>"
                                            >
                                                <?= e($sortableColumn->getLabel()); ?>

                                            </option>
                                        <?php }  ?>
                                     <?= $this->generateInputSelectHtml(...$inputSelectArguments, slot: new ComponentSlot(trim(ob_get_clean()))); ?>
                                 <?= $this->generateInputWrapperHtml(...$inputWrapperArguments, slot: new ComponentSlot(trim(ob_get_clean()))); ?>
                            </label>

                            <label
                                x-cloak
                                x-show="column"
                            >
                                <span
                                    class="fi-sr-only"
                                >
                                    <?= e(__('filament-tables::table.sorting.fields.direction.label')); ?>

                                </span>

                                <?php $inputWrapperArguments = ['attributes' => new ComponentAttributeBag([])];
                        ob_start(); ?>
                                    <?php $inputSelectArguments = ['attributes' => new ComponentAttributeBag(['x-model' => 'direction'])];
                        ob_start(); ?>
                                        <option
                                            value="asc"
                                        >
                                            <?= e(__('filament-tables::table.sorting.fields.direction.options.asc')); ?>

                                        </option>

                                        <option
                                            value="desc"
                                        >
                                            <?= e(__('filament-tables::table.sorting.fields.direction.options.desc')); ?>

                                        </option>
                                     <?= $this->generateInputSelectHtml(...$inputSelectArguments, slot: new ComponentSlot(trim(ob_get_clean()))); ?>
                                 <?= $this->generateInputWrapperHtml(...$inputWrapperArguments, slot: new ComponentSlot(trim(ob_get_clean()))); ?>
                            </label>
                        </div>
                    <?php } ?>

                    <?php if ($this->isRenderSelectionEnabled() && ($this->getMaxSelectableRecords() !== 1) && (! $this->selectsGroupsOnly())) { ?>
                        <?= $this->generatePageCheckboxHtml(isStacked: true); ?>

                    <?php } ?>
                </td>
            </tr>
        <?php } ?><?= $this->renderMorphMarkerHtml(isEnd: true); ?>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateColumnGroupsHtml(): string
    {
        $records = $this->getRecords();

        ob_start(); ?>
        <?= $this->renderMorphMarkerHtml(); ?>                                <?php if ($this->hasColumnGroups()) { ?>
            <tr class="fi-ta-table-head-groups-row">
                <?= $this->renderMorphMarkerHtml(); ?>
                <?php if (count($records)) { ?>
                    <?php if ($this->isReordering()) { ?>
                        <th></th>
                    <?php } else { ?>
                        <?php if ($this->hasRenderRecordActions() && in_array($this->getRecordActionsPosition(), [RecordActionsPosition::BeforeCells, RecordActionsPosition::BeforeColumns])) { ?>
                            <th></th>
                        <?php } ?>

                        <?php if ($this->isRenderSelectionEnabled() && $this->getRecordCheckboxPosition() === RecordCheckboxPosition::BeforeCells) { ?>
                            <th></th>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
                <?= $this->renderMorphMarkerHtml(isEnd: true); ?>

                <?php foreach ($this->iterateRenderLoop($this->getColumnsLayout()) as $columnGroup) {  ?>
                    <?php if ($columnGroup instanceof Column) { ?>
                        <?php if ($columnGroup->isVisible() && (! $columnGroup->isToggledHidden())) { ?>
                            <th></th>
                        <?php } ?>
                    <?php } elseif ($columnGroup instanceof ColumnGroup) { ?>
                        <?php
                            $columnGroupColumnsCount = count($columnGroup->getVisibleColumns());
                        ?>

                        <?php if ($columnGroupColumnsCount) { ?>
                            <th
                                colspan="<?= e($columnGroupColumnsCount); ?>"
                                scope="colgroup"
                                <?= e($columnGroup->getExtraHeaderAttributeBag()->class([
                                    'fi-ta-header-group-cell',
                                    'fi-wrapped' => $columnGroup->canHeaderWrap(),
                                    ((($columnGroupAlignment = $columnGroup->getAlignment()) instanceof Alignment) ? "fi-align-{$columnGroupAlignment->value}" : (is_string($columnGroupAlignment) ? $columnGroupAlignment : '')),
                                    (filled($columnGroupHiddenFrom = $columnGroup->getHiddenFrom()) ? "{$columnGroupHiddenFrom}:fi-hidden" : ''),
                                    (filled($columnGroupVisibleFrom = $columnGroup->getVisibleFrom()) ? "{$columnGroupVisibleFrom}:fi-visible" : ''),
                                ])); ?>

                            >
                                <?= e($columnGroup->getLabel()); ?>

                            </th>
                        <?php } ?>
                    <?php } ?>
                <?php }  ?>

                <?php if ((! $this->isReordering()) && count($records)) { ?>
                    <?php if ($this->hasRenderRecordActions() && in_array($this->getRecordActionsPosition(), [RecordActionsPosition::AfterColumns, RecordActionsPosition::AfterCells])) { ?>
                        <th></th>
                    <?php } ?>

                    <?php if ($this->isRenderSelectionEnabled() && $this->getRecordCheckboxPosition() === RecordCheckboxPosition::AfterCells) { ?>
                        <th></th>
                    <?php } ?>
                <?php } ?>
            </tr>
        <?php } ?><?= $this->renderMorphMarkerHtml(isEnd: true); ?>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateColumnHeaderHtml(Column $column): string
    {
        $sortDirection = $this->getSortDirection();

        ob_start(); ?>
        <?php
            $columnName = $column->getName();
        $columnLabel = $column->getLabel();
        $columnAlignment = $column->getAlignment();
        $columnWidth = $column->getWidth();
        $isColumnActivelySorted = $this->getSortColumn() === $column->getName();
        $isColumnSortable = $column->isSortable() && (! $this->isReordering());

        // A custom label may contain interactive elements, which are invalid inside a native `<button>`, so a `<span>` with button semantics is used instead.
        $columnSortControlTag = ($columnLabel instanceof Htmlable) ? 'span' : 'button';

        $columnHeaderTooltip = $column->getHeaderTooltip();
        $columnHeaderTooltipAttribute = ($columnHeaderTooltip instanceof Htmlable)
            ? 'x-tooltip.html'
            : 'x-tooltip';
        ?>

        <th
            <?php if ($isColumnSortable) { ?>
                aria-sort="<?= e($isColumnActivelySorted ? ($sortDirection === 'asc' ? 'ascending' : 'descending') : 'none'); ?>"
            <?php } ?>
            scope="col"
            <?= e($column->getExtraHeaderAttributeBag()
                ->class([
                    'fi-ta-header-cell',
                    'fi-ta-header-cell-' . str($columnName)->camel()->kebab(),
                    'fi-growable' => blank($columnWidth) && $column->canGrow(default: false),
                    'fi-grouped' => $column->getGroup(),
                    'fi-wrapped' => $column->canHeaderWrap(),
                    'fi-ta-header-cell-sorted' => $isColumnActivelySorted,
                    ((($columnAlignment = $column->getAlignment()) instanceof Alignment) ? "fi-align-{$columnAlignment->value}" : (is_string($columnAlignment) ? $columnAlignment : '')),
                    (filled($columnHiddenFrom = $column->getHiddenFrom()) ? "{$columnHiddenFrom}:fi-hidden" : ''),
                    (filled($columnVisibleFrom = $column->getVisibleFrom()) ? "{$columnVisibleFrom}:fi-visible" : ''),
                ])
                ->style([
                    ('width: ' . e($columnWidth)) => filled($columnWidth),
                ])); ?>

        >
            <?php if ($isColumnSortable) { ?>
                <<?= e($columnSortControlTag); ?>

                    <?php if ($columnSortControlTag === 'button') { ?>
                        type="button"
                    <?php } else { ?>
                        role="button"
                        tabindex="0"
                        x-on:keydown.enter.self.prevent.stop="$el.click()"
                        x-on:keydown.space.self.prevent.stop="$el.click()"
                    <?php } ?>
                    data-prevent-disabled-clicks
                    wire:click="sortTable('<?= e($columnName); ?>')"
                    wire:loading.attr="aria-disabled"
                    wire:loading.class="fi-disabled"
                    wire:target="<?= e(implode(',', Table::LOADING_TARGETS)); ?>"
                    class="fi-ta-header-cell-sort-btn"
                >
                    <?php if (filled($columnHeaderTooltip)) { ?>
                        <span
                            <?= e($columnHeaderTooltipAttribute); ?>="{
                                content: <?= Js::from($columnHeaderTooltip)->toHtml() ?>,
                                theme: $store.theme,
                            }"
                            class="fi-ta-header-cell-tooltip"
                        >
                            <?= e($columnLabel); ?>

                        </span>
                    <?php } else { ?>
                        <?= e($columnLabel); ?>

                    <?php } ?>

                    <?= e(\Filament\Support\generate_loading_indicator_html(new FilamentComponentAttributeBag([
                        'wire:loading.class.delay.' . config('filament.livewire_loading_delay', 'default') => 'fi-loading',
                        'wire:target' => "sortTable('{$columnName}')",
                    ]))); ?>

                    <?= e(\Filament\Support\generate_icon_html(($isColumnActivelySorted && $sortDirection === 'asc') ? Heroicon::ChevronUp : Heroicon::ChevronDown, alias: match (true) {
                        $isColumnActivelySorted && ($sortDirection === 'asc') => TablesIconAlias::HEADER_CELL_SORT_ASC_BUTTON,
                        $isColumnActivelySorted && ($sortDirection === 'desc') => TablesIconAlias::HEADER_CELL_SORT_DESC_BUTTON,
                        default => TablesIconAlias::HEADER_CELL_SORT_BUTTON,
                    })); ?>

                </<?= e($columnSortControlTag); ?>>
            <?php } else { ?>
                <?php if (filled($columnHeaderTooltip)) { ?>
                    <span
                        <?= e($columnHeaderTooltipAttribute); ?>="{
                            content: <?= Js::from($columnHeaderTooltip)->toHtml() ?>,
                            theme: $store.theme,
                        }"
                        class="fi-ta-header-cell-tooltip"
                    >
                        <?= e($columnLabel); ?>

                    </span>
                <?php } else { ?>
                    <?= e($columnLabel); ?>

                <?php } ?>
            <?php } ?>
        </th>
        <?php return ob_get_clean();
    }
}
