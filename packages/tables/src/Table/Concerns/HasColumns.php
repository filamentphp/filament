<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Filament\Support\Enums\Alignment;
use Filament\Support\Facades\FilamentView;
use Filament\Support\Icons\Heroicon;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\Layout\Component as ColumnLayoutComponent;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Enums\RecordCheckboxPosition;
use Filament\Tables\Table;
use Filament\Tables\View\TablesIconAlias;
use Filament\Tables\View\TablesRenderHook;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Js;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\ComponentSlot;

trait HasColumns
{
    /**
     * @var array<string, Column>
     */
    protected array $columns = [];

    /**
     * @var array<string, Column> | null
     */
    protected ?array $cachedVisibleColumns = null;

    /**
     * @var array<string, ColumnGroup>
     */
    protected array $columnGroups = [];

    /**
     * @var array<Column | ColumnLayoutComponent | ColumnGroup>
     */
    protected array $columnsLayout = [];

    protected ?ColumnLayoutComponent $collapsibleColumnsLayout = null;

    protected bool $hasColumnGroups = false;

    protected bool $hasColumnsLayout = false;

    /**
     * @var array<string, true> | null
     */
    protected ?array $cachedRenderPartiallyRenderableColumnNames = null;

    /**
     * @param  array<Column | ColumnLayoutComponent | ColumnGroup>  $components
     */
    public function columns(array $components): static
    {
        $this->columns = [];
        $this->columnGroups = [];
        $this->columnsLayout = [];
        $this->collapsibleColumnsLayout = null;
        $this->hasColumnsLayout = false;
        $this->pushColumns($components);

        return $this;
    }

    /**
     * @param  array<Column | ColumnLayoutComponent | ColumnGroup>  $components
     */
    public function pushColumns(array $components): static
    {
        $this->cachedVisibleColumns = null;

        foreach ($components as $component) {
            $component->table($this);

            if ($component instanceof ColumnLayoutComponent && $component->isCollapsible()) {
                $this->collapsibleColumnsLayout = $component;
            } else {
                $this->columnsLayout[] = $component;
            }

            if ($component instanceof ColumnGroup) {
                $this->hasColumnGroups = true;

                $this->columnGroups[e($component->getLabel())] = $component;

                $this->columns = [
                    ...$this->columns,
                    ...$component->getColumns(),
                ];

                continue;
            }

            if ($component instanceof ColumnLayoutComponent) {
                $this->hasColumnsLayout = true;

                $this->columns = [
                    ...$this->columns,
                    ...$component->getColumns(),
                ];

                continue;
            }

            $this->columns[$component->getName()] = $component;
        }

        foreach ($this->columns as $column) {
            $action = $column->getAction();

            if (($action === null) || ($action instanceof Closure)) {
                continue;
            }

            $this->cacheAction($action->table($this));
        }

        return $this;
    }

    /**
     * @return array<string, Column>
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @return array<string, ColumnGroup>
     */
    public function getColumnGroups(): array
    {
        return $this->columnGroups;
    }

    /**
     * @return array<string, Column>
     */
    public function getVisibleColumns(): array
    {
        return $this->cachedVisibleColumns ??= array_filter(
            $this->getColumns(),
            fn (Column $column): bool => $column->getCloneForRecord(null)->isVisible() && (! $column->isToggledHidden()),
        );
    }

    public function flushCachedVisibleColumns(): void
    {
        $this->cachedVisibleColumns = null;
    }

    public function getColumn(string $name): ?Column
    {
        return $this->getColumns()[$name] ?? null;
    }

    public function getColumnGroup(string $name): ?ColumnGroup
    {
        return $this->getColumnGroups()[$name] ?? null;
    }

    /**
     * @return array<Column | ColumnLayoutComponent | ColumnGroup>
     */
    public function getColumnsLayout(): array
    {
        return $this->columnsLayout;
    }

    public function getCollapsibleColumnsLayout(): ?ColumnLayoutComponent
    {
        return $this->collapsibleColumnsLayout;
    }

    public function hasColumnGroups(): bool
    {
        if (! $this->hasColumnGroups) {
            return false;
        }

        foreach ($this->getVisibleColumns() as $column) {
            $columnGroup = $column->getGroup();

            if (! $columnGroup) {
                continue;
            }

            if (empty($columnGroup->getVisibleColumns())) {
                continue;
            }

            return true;
        }

        return false;
    }

    public function hasColumnsLayout(): bool
    {
        return $this->hasColumnsLayout;
    }

    /**
     * @return array<string, true>
     */
    public function getPartiallyRenderableColumnNames(): array
    {
        if ($this->getContent() || $this->hasColumnsLayout()) {
            return [];
        }

        $columnNames = [];

        foreach ($this->getVisibleColumns() as $column) {
            if (! method_exists($column, 'getPartiallyRenderableColumnNamesAfterStateUpdated')) {
                continue;
            }

            foreach ($column->getPartiallyRenderableColumnNamesAfterStateUpdated() as $columnName) {
                $columnNames[$columnName] = true;
            }
        }

        return $columnNames;
    }

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
                <?= $this->generateMorphMarkerHtml(); ?>
                <?php if (count($records)) { ?>
                    <?php if ($this->isReordering()) { ?>
                        <th></th>
                    <?php } else { ?>
                        <?= $this->generateMorphMarkerHtml(); ?>
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
                        <?= $this->generateMorphMarkerHtml(isEnd: true); ?>

                        <?= $this->generateMorphMarkerHtml(); ?>
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
                        <?= $this->generateMorphMarkerHtml(isEnd: true); ?>

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
                <?php } ?>
                <?= $this->generateMorphMarkerHtml(isEnd: true); ?>

                <?php $hasHeaderCellRenderHook = FilamentView::hasRenderHook(TablesRenderHook::HEADER_CELL, scopes: $livewire::class); ?>

                <?= $this->generateMorphMarkerHtml(); ?>
                <?php foreach ($this->iterateRenderLoop($this->getVisibleColumns()) as $column) { ?>
                    <?php if ($hasHeaderCellRenderHook && filled($headerCellView = FilamentView::renderHook(TablesRenderHook::HEADER_CELL, scopes: $livewire::class, data: [
                        'column' => $column,
                        'isReordering' => $this->isReordering(),
                    ]))) { ?>
                        <?= e($headerCellView); ?>
                    <?php } else { ?>
                        <?= $this->generateColumnHeaderHtml($column); ?>
                    <?php } ?>
                <?php } ?>
                <?= $this->generateMorphMarkerHtml(isEnd: true); ?>

                <?php if ((! $this->isReordering()) && count($records)) { ?>
                    <?= $this->generateMorphMarkerHtml(); ?>
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
                    <?= $this->generateMorphMarkerHtml(isEnd: true); ?>

                    <?= $this->generateMorphMarkerHtml(); ?>
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
                    <?= $this->generateMorphMarkerHtml(isEnd: true); ?>

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
        <?= $this->generateMorphMarkerHtml(); ?>
        <?php if ($this->isStackedOnMobile() && (count($sortableColumns) || ($this->isRenderSelectionEnabled() && ($this->getMaxSelectableRecords() !== 1) && (! $this->selectsGroupsOnly()))) && (! $this->isReordering())) { ?>
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

                                        <?php foreach ($this->iterateRenderLoop($sortableColumns) as $sortableColumn) { ?>
                                            <option
                                                value="<?= e($sortableColumn->getName()); ?>"
                                            >
                                                <?= e($sortableColumn->getLabel()); ?>
                                            </option>
                                        <?php } ?>
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
        <?php } ?>
        <?= $this->generateMorphMarkerHtml(isEnd: true); ?>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateColumnGroupsHtml(): string
    {
        $records = $this->getRecords();

        ob_start(); ?>
        <?= $this->generateMorphMarkerHtml(); ?>
        <?php if ($this->hasColumnGroups()) { ?>
            <tr class="fi-ta-table-head-groups-row">
                <?= $this->generateMorphMarkerHtml(); ?>
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
                <?= $this->generateMorphMarkerHtml(isEnd: true); ?>

                <?php foreach ($this->iterateRenderLoop($this->getColumnsLayout()) as $columnGroup) { ?>
                    <?php if ($columnGroup instanceof Column) { ?>
                        <?php if ($columnGroup->isVisible() && (! $columnGroup->isToggledHidden())) { ?>
                            <th></th>
                        <?php } ?>
                    <?php } elseif ($columnGroup instanceof ColumnGroup) {
                        $columnGroupColumnsCount = count($columnGroup->getVisibleColumns());

                        if ($columnGroupColumnsCount) { ?>
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
                <?php } ?>

                <?php if ((! $this->isReordering()) && count($records)) { ?>
                    <?php if ($this->hasRenderRecordActions() && in_array($this->getRecordActionsPosition(), [RecordActionsPosition::AfterColumns, RecordActionsPosition::AfterCells])) { ?>
                        <th></th>
                    <?php } ?>

                    <?php if ($this->isRenderSelectionEnabled() && $this->getRecordCheckboxPosition() === RecordCheckboxPosition::AfterCells) { ?>
                        <th></th>
                    <?php } ?>
                <?php } ?>
            </tr>
        <?php } ?>
        <?= $this->generateMorphMarkerHtml(isEnd: true); ?>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateColumnHeaderHtml(Column $column): string
    {
        $sortDirection = $this->getSortDirection();

        ob_start();

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

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     *
     * @param  Model | array<string, mixed>  $record
     */
    protected function generateRecordCellHtml(
        Column $column,
        Model | array $record,
        \stdClass $loop,
        string $recordKey,
        ?string $recordUrl,
        ?string $recordAction,
        bool $openRecordUrlInNewTab,
        bool $isReordering,
        bool $isStackedOnMobile,
    ): string {
        $column->record($record);
        $column->rowLoop($loop->parent);
        $column->recordKey($recordKey);

        $this->setRenderKey($this->getLivewire()->getId() . '.table.record.' . $recordKey . '.column.' . $column->getName());

        return $column->renderInTableCell(
            recordAction: $recordAction,
            recordUrl: $recordUrl,
            shouldOpenRecordUrlInNewTab: $openRecordUrlInNewTab,
            isPartiallyRenderable: array_key_exists($column->getName(), $this->cachedRenderPartiallyRenderableColumnNames ??= $this->getPartiallyRenderableColumnNames()),
            isReordering: $isReordering,
            isStackedOnMobile: $isStackedOnMobile,
        )->toHtml();
    }
}
