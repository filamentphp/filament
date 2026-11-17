<?php

namespace Filament\Tables\Table\Concerns;

use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Support\Components\ViewComponent;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\GridDirection;
use Filament\Support\Icons\Heroicon;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Filament\Tables\Columns\Column;
use Filament\Tables\Enums\ColumnManagerResetActionPosition;
use Filament\Tables\Enums\FiltersResetActionPosition;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Enums\RecordCheckboxPosition;
use Filament\Tables\View\TablesIconAlias;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Js;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\ComponentSlot;

use function Filament\Support\generate_icon_html;

trait CanRenderComponents
{
    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateFiltersHtml(
        Action $applyAction,
        Schema $form,
        string $headingTag = 'h3',
        ?Action $resetAction = null,
        ?FiltersResetActionPosition $resetActionPosition = null,
        ?ComponentAttributeBag $attributes = null,
    ): string {
        $attributes ??= new FilamentComponentAttributeBag;

        if (ViewComponent::hasPublishedEmbeddedViewOverride('filament-tables::components.filters')) {
            return view('filament-tables::components.filters', [
                'applyAction' => $applyAction,
                'form' => $form,
                'headingTag' => $headingTag,
                'resetAction' => $resetAction,
                'resetActionPosition' => $resetActionPosition,
                'attributes' => $attributes,
                'slot' => new ComponentSlot,
                'this' => $this->getLivewire(),
            ])->render();
        }

        $resetActionPosition ??= FiltersResetActionPosition::Header;
        $isApplyActionVisible = $applyAction->isVisible();
        $isResetActionVisible = $resetAction?->isVisible() ?? false;

        $html = '<div ' . $attributes->class(['fi-ta-filters'])->toHtml() . '><div class="fi-ta-filters-header">';
        $html .= '<' . e($headingTag) . ' class="fi-ta-filters-heading">' . e(__('filament-tables::table.filters.heading')) . '</' . e($headingTag) . '>';

        if (($resetActionPosition === FiltersResetActionPosition::Header) && $isResetActionVisible) {
            $html .= '<div>' . e($resetAction->defaultView($resetAction::LINK_VIEW)) . '</div>';
        }

        $html .= '</div>' . e($form);

        if ($isApplyActionVisible || (($resetActionPosition === FiltersResetActionPosition::Footer) && $isResetActionVisible)) {
            $html .= '<div class="fi-ta-filters-actions-ctn">';
            $html .= $this->renderMorphMarkerHtml();

            if ($isApplyActionVisible) {
                $html .= e($applyAction);
            }

            $html .= $this->renderMorphMarkerHtml(isEnd: true);

            if (($resetActionPosition === FiltersResetActionPosition::Footer) && $isResetActionVisible) {
                $html .= e($resetAction);
            }

            $html .= '</div>';
        }

        return $html . '</div>';
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateSearchFieldHtml(
        string | int $debounce = '500ms',
        string | Htmlable | null $label = null,
        bool $labelHidden = true,
        bool $onBlur = false,
        ?string $placeholder = null,
        string $wireModel = 'tableSearch',
        ?ComponentAttributeBag $attributes = null,
    ): string {
        $attributes ??= new FilamentComponentAttributeBag;
        $label ??= __('filament-tables::table.fields.search.label');
        $placeholder ??= __('filament-tables::table.fields.search.placeholder');
        $livewire = $this->getLivewire();

        if (ViewComponent::hasPublishedEmbeddedViewOverride('filament-tables::components.search-field')) {
            return view('filament-tables::components.search-field', [
                'debounce' => $debounce,
                'label' => $label,
                'labelHidden' => $labelHidden,
                'onBlur' => $onBlur,
                'placeholder' => $placeholder,
                'wireModel' => $wireModel,
                'attributes' => $attributes,
                'slot' => new ComponentSlot,
                'this' => $livewire,
            ])->render();
        }

        $wireModelAttribute = $onBlur ? 'wire:model.blur' : "wire:model.live.debounce.{$debounce}";
        $inputKey = $livewire->getId() . '.table.' . $wireModel . '.field.input';
        $this->setRenderKey($inputKey);

        $inputHtml = $this->generateInputHtml(
            attributes: (new ComponentAttributeBag)->merge([
                'autocomplete' => 'off',
                'maxlength' => 1000,
                'placeholder' => $placeholder,
                'type' => 'search',
                'wire:key' => $inputKey,
                $wireModelAttribute => $wireModel,
                'x-bind:id' => '$id(\'input\')',
                'x-on:keyup' => '$event.key === \'Enter\' && $wire.$refresh()',
            ], escape: false),
            inlinePrefix: true,
        );

        return '<div x-id="[\'input\']" ' . $attributes->class(['fi-ta-search-field'])->toHtml() . '>'
            . '<label x-bind:for="$id(\'input\')" class="' . ($labelHidden ? 'fi-sr-only' : 'fi-ta-cell-label') . '">' . e($label) . '</label>'
            . $this->generateInputWrapperHtml(
                attributes: new FilamentComponentAttributeBag(['wire:target' => $wireModel]),
                slot: new HtmlString($inputHtml),
                inlinePrefix: true,
                prefixIcon: Heroicon::MagnifyingGlass,
                prefixIconAlias: TablesIconAlias::SEARCH_FIELD,
            )
            . '</div>';
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     *
     * @param  array<string, ?int> | int | null  $columns
     */
    protected function generateColumnManagerHtml(
        Action $applyAction,
        bool $hasReorderableColumns,
        bool $hasToggleableColumns,
        array | int | null $columns = null,
        string $headingTag = 'h3',
        int $reorderAnimationDuration = 300,
        ?ColumnManagerResetActionPosition $resetActionPosition = null,
        ?ComponentAttributeBag $attributes = null,
    ): string {
        $attributes ??= new FilamentComponentAttributeBag;

        if (ViewComponent::hasPublishedEmbeddedViewOverride('filament-tables::components.column-manager.index')) {
            return view('filament-tables::components.column-manager.index', [
                'applyAction' => $applyAction,
                'columns' => $columns,
                'hasReorderableColumns' => $hasReorderableColumns,
                'hasToggleableColumns' => $hasToggleableColumns,
                'headingTag' => $headingTag,
                'reorderAnimationDuration' => $reorderAnimationDuration,
                'resetActionPosition' => $resetActionPosition,
                'attributes' => $attributes,
                'slot' => new ComponentSlot,
                'this' => $this->getLivewire(),
            ])->render();
        }

        $resetActionPosition ??= ColumnManagerResetActionPosition::Header;
        $isApplyActionVisible = $applyAction->isVisible();
        $resetLabel = __('filament-tables::table.column_manager.actions.reset.label');

        $html = '<div x-data="filamentTableColumnManager({ columns: $wire.entangle(\'tableColumns\'), isLive: ' . ($isApplyActionVisible ? 'false' : 'true') . ', })" class="fi-ta-col-manager">';
        $html .= '<div class="fi-ta-col-manager-header"><' . e($headingTag) . ' class="fi-ta-col-manager-heading">' . e(__('filament-tables::table.column_manager.heading')) . '</' . e($headingTag) . '>';

        if ($resetActionPosition === ColumnManagerResetActionPosition::Header) {
            $html .= '<div>' . $this->generateLinkHtml(
                attributes: new FilamentComponentAttributeBag([
                    'wire:click' => 'resetTableColumnManager',
                    'wire:loading.remove.delay.' . config('filament.livewire_loading_delay', 'default') => '',
                    'wire:target' => 'resetTableColumnManager',
                    'x-on:click' => 'resetDeferredColumns',
                ]),
                color: 'danger',
                tag: 'button',
                label: $resetLabel,
                shouldCheckForPublishedView: true,
            ) . '</div>';
        }

        $html .= '</div>' . $this->generateColumnManagerContentHtml(
            hasReorderableColumns: $hasReorderableColumns,
            hasToggleableColumns: $hasToggleableColumns,
            columns: $columns,
            reorderAnimationDuration: $reorderAnimationDuration,
        );

        if ($isApplyActionVisible || ($resetActionPosition === ColumnManagerResetActionPosition::Footer)) {
            $html .= '<div class="fi-ta-col-manager-actions-ctn">';
            $html .= $this->renderMorphMarkerHtml();

            if ($isApplyActionVisible) {
                $html .= e($applyAction);
            }

            $html .= $this->renderMorphMarkerHtml(isEnd: true);

            if ($resetActionPosition === ColumnManagerResetActionPosition::Footer) {
                $html .= $this->generateButtonHtml(
                    attributes: new FilamentComponentAttributeBag([
                        'wire:click' => 'resetTableColumnManager',
                        'x-on:click' => 'resetDeferredColumns',
                    ]),
                    color: 'danger',
                    label: $resetLabel,
                    shouldCheckForPublishedView: true,
                );
            }

            $html .= '</div>';
        }

        return $html . '</div>';
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     *
     * @param  array<string, ?int> | int | null  $columns
     */
    protected function generateColumnManagerContentHtml(
        bool $hasReorderableColumns,
        bool $hasToggleableColumns,
        array | int | null $columns = null,
        int $reorderAnimationDuration = 300,
        ?ComponentAttributeBag $attributes = null,
    ): string {
        $attributes ??= new FilamentComponentAttributeBag;

        if (ViewComponent::hasPublishedEmbeddedViewOverride('filament-tables::components.column-manager.content')) {
            return view('filament-tables::components.column-manager.content', [
                'columns' => $columns,
                'hasReorderableColumns' => $hasReorderableColumns,
                'hasToggleableColumns' => $hasToggleableColumns,
                'reorderAnimationDuration' => $reorderAnimationDuration,
                'attributes' => $attributes,
                'slot' => new ComponentSlot,
                'this' => $this->getLivewire(),
            ])->render();
        }

        $html = '<div ';

        if ($hasToggleableColumns) {
            $html .= 'x-id="[\'fi-ta-col-manager-group-checkbox\', \'fi-ta-col-manager-column-checkbox\']" ';
        }

        if ($hasReorderableColumns) {
            $html .= 'x-sortable x-on:end.stop="reorderColumns($event.target.sortable.toArray())" x-on:focusin.window="focusRevision++" x-on:pointerdown.window="focusRevision++" data-sortable-animation-duration="' . e($reorderAnimationDuration) . '" ';
        }

        // The original component does not forward its attribute bag to this grid.
        $html .= (new FilamentComponentAttributeBag)->grid($columns, GridDirection::Column)->class(['fi-ta-col-manager-items'])->toHtml() . '>';
        $html .= '<template x-for="column in getVisibleColumns()" x-bind:key="getColumnKey(column)"><div';

        if ($hasReorderableColumns) {
            $html .= ' x-bind:x-sortable-item="column.type === \'group\' ? \'group::\' + column.name : \'column::\' + column.name"';
        }

        $html .= '><template x-if="column.type === \'group\'"><div class="fi-ta-col-manager-group"><div class="fi-ta-col-manager-item">';
        $html .= $this->generateColumnManagerLabelHtml(isGroup: true, isGroupedColumn: false, hasToggleableColumns: $hasToggleableColumns);

        if ($hasReorderableColumns) {
            $html .= $this->generateColumnManagerReorderControlsHtml();
        }

        $html .= '</div><div';

        if ($hasReorderableColumns) {
            $html .= ' x-sortable x-on:end.stop="reorderGroupColumns($event.target.sortable.toArray(), column.name)" data-sortable-animation-duration="' . e($reorderAnimationDuration) . '"';
        }

        $html .= ' class="fi-ta-col-manager-group-items"><template x-for="groupColumn in getVisibleColumns(column.name)" x-bind:key="getColumnKey(groupColumn)"><div';

        if ($hasReorderableColumns) {
            $html .= ' x-bind:x-sortable-item="\'column::\' + groupColumn.name"';
        }

        $html .= '><div class="fi-ta-col-manager-item">';
        $html .= $this->generateColumnManagerLabelHtml(isGroup: false, isGroupedColumn: true, hasToggleableColumns: $hasToggleableColumns);

        if ($hasReorderableColumns) {
            $html .= $this->generateColumnManagerReorderControlsHtml(isGroupedColumn: true);
        }

        $html .= '</div></div></template></div></div></template><template x-if="column.type !== \'group\'"><div class="fi-ta-col-manager-item">';
        $html .= $this->generateColumnManagerLabelHtml(isGroup: false, isGroupedColumn: false, hasToggleableColumns: $hasToggleableColumns);

        if ($hasReorderableColumns) {
            $html .= $this->generateColumnManagerReorderControlsHtml();
        }

        $html .= '</div></template></div></template>';

        if ($hasReorderableColumns) {
            $html .= '<p role="status" aria-live="polite" aria-atomic="true" class="fi-ta-col-manager-status" x-text="reorderAnnouncement"></p>';
        }

        return $html . '</div>';
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateColumnManagerLabelHtml(bool $isGroup, bool $isGroupedColumn, bool $hasToggleableColumns): string
    {
        $columnExpression = $isGroupedColumn ? 'groupColumn' : 'column';
        $checkboxId = $isGroup ? 'fi-ta-col-manager-group-checkbox' : 'fi-ta-col-manager-column-checkbox';
        $idExpression = '$id(\'' . $checkboxId . '\', ' . $columnExpression . '.name)';
        $html = '<label' . ($hasToggleableColumns ? ' x-bind:for="' . e($idExpression) . '"' : '') . ' class="fi-ta-col-manager-label">';

        if ($hasToggleableColumns) {
            $html .= '<input type="checkbox" class="fi-checkbox-input fi-valid" x-bind:id="' . e($idExpression) . '"';

            if ($isGroup) {
                $html .= ' x-bind:checked="(groupedColumns[column.name] || {}).checked || false" x-bind:disabled="(groupedColumns[column.name] || {}).disabled || false" x-effect="syncGroupCheckboxIndeterminate($el, column.name)" x-on:change="toggleGroup(column.name)"';
            } else {
                $getColumnExpression = 'getColumn(' . $columnExpression . '.name, ' . ($isGroupedColumn ? 'column.name' : 'null') . ')';
                $html .= ' x-bind:checked="(' . $getColumnExpression . ' || {}).isToggled || false" x-bind:disabled="(' . $getColumnExpression . ' || {}).isToggleable === false" x-on:change="toggleColumn(' . $columnExpression . '.name' . ($isGroupedColumn ? ', column.name' : '') . ')"';
            }

            $html .= ' /> ';
        }

        return $html . '<span x-html="' . $columnExpression . '.label"></span></label>';
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateColumnManagerReorderControlsHtml(bool $isGroupedColumn = false): string
    {
        $columnExpression = $isGroupedColumn ? 'groupColumn' : 'column';
        $groupArgument = $isGroupedColumn ? ', column.name' : '';
        $position = Js::from(__('filament-tables::table.column_manager.' . ($isGroupedColumn ? 'position_in_group' : 'position')))->toHtml();
        $position .= ".replace(':position', getColumnPosition({$columnExpression}{$groupArgument})).replace(':count', getVisibleColumns(" . ($isGroupedColumn ? 'column.name' : '') . ').length)';

        if ($isGroupedColumn) {
            $position .= ".replace(':group', getColumnLabel(column))";
        }

        $announcement = Js::from(__('filament-tables::table.column_manager.' . ($isGroupedColumn ? 'reordered_in_group' : 'reordered')))->toHtml();
        $deferredAnnouncement = Js::from(__('filament-tables::table.column_manager.' . ($isGroupedColumn ? 'reordered_in_group_deferred' : 'reordered_deferred')))->toHtml();
        $html = '<div x-data="{}" x-id="[\'column-manager-position\']" class="fi-ta-col-manager-reorder-controls"><span x-bind:id="$id(\'column-manager-position\')" x-text="' . e($position) . '"></span>';

        foreach ($this->iterateRenderLoop(['up', 'down']) as $direction) {
            $directionExpression = Js::from($direction)->toHtml();
            $label = Js::from(__('filament-tables::table.column_manager.actions.move_' . $direction . '.label'))->toHtml() . ".replace(':column', getColumnLabel({$columnExpression}))";
            $canMove = 'canMoveColumn(' . $columnExpression . ', ' . $directionExpression . $groupArgument . ')';
            $click = 'moveColumn(' . $columnExpression . ', ' . $directionExpression . ', ' . ($isGroupedColumn ? 'column.name' : 'null') . ', $event.currentTarget, isLive ? ' . $announcement . ' : ' . $deferredAnnouncement . ',)';
            $html .= '<button type="button" tabindex="-1" x-text="' . e($label) . '" x-bind:aria-describedby="$id(\'column-manager-position\')" x-bind:disabled="! ' . e($canMove) . '" x-bind:aria-disabled="isLoading || ! ' . e($canMove) . '" x-bind:data-testid="\'column-manager-\' + ' . $columnExpression . '.name + \'-move-' . $direction . '\'" x-on:click.stop="' . e($click) . '"></button>';
        }

        return $html . '</div><button x-sortable-handle aria-hidden="true" tabindex="-1" x-on:click.stop class="fi-ta-col-manager-reorder-handle fi-icon-btn" type="button">'
            . generate_icon_html(Heroicon::Bars2, alias: TablesIconAlias::REORDER_HANDLE)?->toHtml() . '</button>';
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     *
     * @param  array<Column>  $columns
     * @param  Collection<array-key, mixed> | Paginator | CursorPaginator  $records
     */
    protected function generateSummaryHtml(
        array $columns,
        string $pluralModelLabel,
        Collection | Paginator | CursorPaginator $records,
        bool $actions = false,
        ?RecordActionsPosition $actionsPosition = null,
        bool $allTableSummary = true,
        bool $extraHeadingColumn = false,
        ?string $groupColumn = null,
        bool $groupsOnly = false,
        bool $pageSummary = true,
        bool $placeholderColumns = true,
        ?RecordCheckboxPosition $recordCheckboxPosition = null,
        bool $selectionEnabled = false,
        ?ComponentAttributeBag $attributes = null,
    ): string {
        $attributes ??= new FilamentComponentAttributeBag;
        $livewire = $this->getLivewire();

        if (ViewComponent::hasPublishedEmbeddedViewOverride('filament-tables::components.summary.index')) {
            return view('filament-tables::components.summary.index', [
                'actions' => $actions,
                'actionsPosition' => $actionsPosition,
                'allTableSummary' => $allTableSummary,
                'columns' => $columns,
                'extraHeadingColumn' => $extraHeadingColumn,
                'groupColumn' => $groupColumn,
                'groupsOnly' => $groupsOnly,
                'pageSummary' => $pageSummary,
                'placeholderColumns' => $placeholderColumns,
                'pluralModelLabel' => $pluralModelLabel,
                'recordCheckboxPosition' => $recordCheckboxPosition,
                'records' => $records,
                'selectionEnabled' => $selectionEnabled,
                'attributes' => $attributes,
                'slot' => new ComponentSlot,
                'this' => $livewire,
            ])->render();
        }

        if ($groupsOnly && $groupColumn) {
            $columns = array_filter($columns, static fn (Column $column): bool => $column->getName() !== $groupColumn);
        }

        $hasPageSummary = $pageSummary && (! $groupsOnly) && ($records instanceof Paginator) && $records->hasPages();
        $pageTableSummaryQuery = $hasPageSummary ? $livewire->getPageTableSummaryQuery() : null;
        $allTableSummaryQuery = $allTableSummary ? $livewire->getAllTableSummaryQuery() : null;
        $heading = __('filament-tables::table.summary.heading', ['label' => $pluralModelLabel]);
        $viewFactory = app('view');
        $html = '';

        // The optional page header and row precede the unkeyed all-records row.
        $html .= $this->renderMorphMarkerHtml();

        if ($hasPageSummary) {
            $html .= '<tr class="fi-ta-row fi-ta-summary-header-row fi-striped">';
            $html .= $this->renderMorphMarkerHtml();
            $html .= $this->generateSummaryPlaceholderCellsHtml($placeholderColumns, $actions, $actionsPosition, $selectionEnabled, $recordCheckboxPosition, isBefore: true);
            $html .= $this->renderMorphMarkerHtml(isEnd: true);

            if ($extraHeadingColumn) {
                $html .= '<th scope="col" class="fi-ta-cell fi-ta-summary-header-cell fi-align-start">' . e($heading) . '</th>';
            }

            foreach ($this->iterateRenderLoop($columns) as $column) {
                $loop = $viewFactory->getLastLoop();
                $columnHasSummary = ($pageTableSummaryQuery && $column->hasSummary($pageTableSummaryQuery)) || $column->hasSummary($allTableSummaryQuery);

                if ((! $placeholderColumns) && (! $columnHasSummary)) {
                    continue;
                }

                $alignment = $column->getAlignment() ?? Alignment::Start;

                if (! $alignment instanceof Alignment) {
                    $alignment = filled($alignment) ? (Alignment::tryFrom($alignment) ?? $alignment) : null;
                }

                $hasColumnHeaderLabel = (! $placeholderColumns) || $columnHasSummary;
                $isFirstSummaryHeading = $loop->first && (! $extraHeadingColumn);
                $isLabelledHeaderCell = $isFirstSummaryHeading || $hasColumnHeaderLabel;
                $headerCellTag = $isLabelledHeaderCell ? 'th' : 'td';
                $alignmentClass = $isFirstSummaryHeading ? 'fi-align-start' : (($alignment instanceof Alignment) ? "fi-align-{$alignment->value}" : (is_string($alignment) ? $alignment : ''));
                $columnHiddenFrom = $column->getHiddenFrom();
                $columnVisibleFrom = $column->getVisibleFrom();
                $headerAttributes = $column->getExtraHeaderAttributeBag()->class([
                    'fi-ta-cell fi-ta-summary-header-cell',
                    'fi-wrapped' => $column->canHeaderWrap(),
                    $alignmentClass => $isFirstSummaryHeading || $hasColumnHeaderLabel,
                    filled($columnHiddenFrom) ? "{$columnHiddenFrom}:fi-hidden" : '',
                    filled($columnVisibleFrom) ? "{$columnVisibleFrom}:fi-visible" : '',
                ]);
                $html .= '<' . $headerCellTag . ($isLabelledHeaderCell ? ' scope="col"' : '') . ' ' . $headerAttributes->toHtml() . '>';
                $html .= $isFirstSummaryHeading ? e($heading) : ($hasColumnHeaderLabel ? e($column->getLabel()) : '');
                $html .= '</' . $headerCellTag . '>';
            }

            $html .= $this->generateSummaryPlaceholderCellsHtml($placeholderColumns, $actions, $actionsPosition, $selectionEnabled, $recordCheckboxPosition, isBefore: false) . '</tr>';
            $html .= $this->generateSummaryRowHtml(
                columns: $columns,
                heading: __('filament-tables::table.summary.subheadings.page', ['label' => $pluralModelLabel]),
                query: $pageTableSummaryQuery,
                selectedState: $livewire->getTableSummarySelectedState($pageTableSummaryQuery)[0] ?? [],
                actions: $actions,
                actionsPosition: $actionsPosition,
                extraHeadingColumn: $extraHeadingColumn,
                placeholderColumns: $placeholderColumns,
                selectionEnabled: $selectionEnabled,
                recordCheckboxPosition: $recordCheckboxPosition,
            );
        }

        $html .= $this->renderMorphMarkerHtml(isEnd: true);

        if ($allTableSummary) {
            $html .= $this->generateSummaryRowHtml(
                columns: $columns,
                heading: __($hasPageSummary ? 'filament-tables::table.summary.subheadings.all' : 'filament-tables::table.summary.heading', ['label' => $pluralModelLabel]),
                query: $allTableSummaryQuery,
                selectedState: $livewire->getTableSummarySelectedState($allTableSummaryQuery)[0] ?? [],
                actions: $actions,
                actionsPosition: $actionsPosition,
                extraHeadingColumn: $extraHeadingColumn,
                groupsOnly: $groupsOnly,
                placeholderColumns: $placeholderColumns,
                selectionEnabled: $selectionEnabled,
                recordCheckboxPosition: $recordCheckboxPosition,
                attributes: (new FilamentComponentAttributeBag)->class(['fi-striped' => ! $hasPageSummary]),
            );
        }

        return $html;
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     *
     * @param  array<Column>  $columns
     * @param  array<string, mixed>  $selectedState
     */
    protected function generateSummaryRowHtml(
        array $columns,
        string | Htmlable $heading,
        ?Builder $query,
        array $selectedState,
        bool $actions = false,
        ?RecordActionsPosition $actionsPosition = null,
        bool $extraHeadingColumn = false,
        ?string $groupColumn = null,
        bool $groupsOnly = false,
        bool $placeholderColumns = true,
        bool $selectionEnabled = false,
        ?RecordCheckboxPosition $recordCheckboxPosition = null,
        ?ComponentAttributeBag $attributes = null,
    ): string {
        $attributes ??= new FilamentComponentAttributeBag;

        if (ViewComponent::hasPublishedEmbeddedViewOverride('filament-tables::components.summary.row')) {
            return view('filament-tables::components.summary.row', [
                'actions' => $actions,
                'actionsPosition' => $actionsPosition,
                'columns' => $columns,
                'extraHeadingColumn' => $extraHeadingColumn,
                'groupColumn' => $groupColumn,
                'groupsOnly' => $groupsOnly,
                'heading' => $heading,
                'placeholderColumns' => $placeholderColumns,
                'query' => $query,
                'selectionEnabled' => $selectionEnabled,
                'selectedState' => $selectedState,
                'recordCheckboxPosition' => $recordCheckboxPosition,
                'attributes' => $attributes,
                'slot' => new ComponentSlot,
                'this' => $this->getLivewire(),
            ])->render();
        }

        if ($groupsOnly && $groupColumn) {
            $columns = array_filter($columns, static fn (Column $column): bool => $column->getName() !== $groupColumn);
        }

        $isStackedOnMobile = $this->isStackedOnMobile();
        $firstColumnKey = array_key_first($columns);
        $columnsWithSummary = [];

        foreach ($columns as $columnKey => $column) {
            $summarizers = $column->getSummarizers($query);
            $columnsWithSummary[$columnKey] = [
                'summarizers' => $summarizers,
                'hasSummary' => (bool) count($summarizers),
                'hiddenFrom' => $column->getHiddenFrom(),
                'visibleFrom' => $column->getVisibleFrom(),
            ];
        }

        $summaryHeadingHiddenAt = [];
        $summaryHeadingFallbackVisibleAt = [];

        if ((! $extraHeadingColumn) && (! $groupsOnly) && filled($columns)) {
            $breakpointOrder = ['base' => 0, 'sm' => 1, 'md' => 2, 'lg' => 3, 'xl' => 4, '2xl' => 5];
            $responsiveBreakpointOrder = array_diff_key($breakpointOrder, ['base' => true]);
            $isColumnVisibleAt = static function (array $columnState, int $breakpointIndex) use ($responsiveBreakpointOrder): bool {
                $visibleFromIndex = filled($columnState['visibleFrom']) ? ($responsiveBreakpointOrder[$columnState['visibleFrom']] ?? null) : null;

                if ($visibleFromIndex !== null) {
                    return $breakpointIndex >= $visibleFromIndex;
                }

                $hiddenFromIndex = filled($columnState['hiddenFrom']) ? ($responsiveBreakpointOrder[$columnState['hiddenFrom']] ?? null) : null;

                return ($hiddenFromIndex === null) || ($breakpointIndex < $hiddenFromIndex);
            };
            $firstColumnState = $columnsWithSummary[$firstColumnKey];

            foreach ($breakpointOrder as $breakpoint => $breakpointIndex) {
                if ((($breakpoint === 'base') && $isStackedOnMobile) || $isColumnVisibleAt($firstColumnState, $breakpointIndex)) {
                    continue;
                }

                $summaryHeadingHiddenAt[] = $breakpoint;

                foreach ($columnsWithSummary as $columnKey => $columnState) {
                    if ((! $columnState['hasSummary']) || (! $isColumnVisibleAt($columnState, $breakpointIndex))) {
                        continue;
                    }

                    $summaryHeadingFallbackVisibleAt[$columnKey][] = $breakpoint;

                    break;
                }
            }
        }

        $html = '<tr' . (filled($summaryHeadingHiddenAt) ? ' data-summary-heading-hidden-at="' . e(implode(' ', $summaryHeadingHiddenAt)) . '"' : '') . ' ' . $attributes->class(['fi-ta-row fi-ta-summary-row'])->toHtml() . '>';
        $html .= $this->renderMorphMarkerHtml();
        $html .= $this->generateSummaryPlaceholderCellsHtml($placeholderColumns, $actions, $actionsPosition, $selectionEnabled, $recordCheckboxPosition, isBefore: true);
        $html .= $this->renderMorphMarkerHtml(isEnd: true);
        $headingColumnSpan = 1;

        if ($extraHeadingColumn || $groupsOnly) {
            $html .= '<th scope="row" class="fi-ta-cell fi-ta-summary-row-heading-cell fi-align-start">' . e($heading) . '</th>';
        } else {
            foreach ($columns as $columnKey => $column) {
                if ($columnKey === $firstColumnKey) {
                    if (filled($columnsWithSummary[$columnKey]['hiddenFrom']) || filled($columnsWithSummary[$columnKey]['visibleFrom'])) {
                        break;
                    }

                    continue;
                }

                if ($columnsWithSummary[$columnKey]['hasSummary'] || filled($columnsWithSummary[$columnKey]['hiddenFrom']) || filled($columnsWithSummary[$columnKey]['visibleFrom'])) {
                    break;
                }

                $headingColumnSpan++;
            }
        }

        $viewFactory = app('view');

        foreach ($this->iterateRenderLoop($columns) as $columnKey => $column) {
            $loop = $viewFactory->getLastLoop();
            $columnState = $columnsWithSummary[$columnKey];

            if ((! ($loop->first || $extraHeadingColumn || $groupsOnly || ($loop->iteration > $headingColumnSpan))) || ((! $placeholderColumns) && (! $columnState['hasSummary']))) {
                continue;
            }

            $alignment = $column->getAlignment() ?? Alignment::Start;

            if (! $alignment instanceof Alignment) {
                $alignment = filled($alignment) ? (Alignment::tryFrom($alignment) ?? $alignment) : null;
            }

            $isSummaryRowHeadingCell = $loop->first && (! $extraHeadingColumn) && (! $groupsOnly);
            $summaryCellTag = $isSummaryRowHeadingCell ? 'th' : 'td';
            $columnHiddenFrom = $columnState['hiddenFrom'];
            $columnVisibleFrom = $columnState['visibleFrom'];
            $classes = Arr::toCssClasses([
                'fi-ta-cell',
                ($alignment instanceof Alignment) ? "fi-align-{$alignment->value}" : (is_string($alignment) ? $alignment : ''),
                'fi-ta-summary-row-heading-cell' => $isSummaryRowHeadingCell,
                'fi-ta-summary-row-heading-cell-responsive' => $isSummaryRowHeadingCell && (filled($columnHiddenFrom) || filled($columnVisibleFrom)),
                filled($columnHiddenFrom) ? "{$columnHiddenFrom}:fi-hidden" : '',
                filled($columnVisibleFrom) ? "{$columnVisibleFrom}:fi-visible" : '',
            ]);
            $html .= '<' . $summaryCellTag . ($isSummaryRowHeadingCell ? ' scope="row"' : '') . (($isSummaryRowHeadingCell && ($headingColumnSpan > 1)) ? ' colspan="' . $headingColumnSpan . '"' : '') . ' class="' . e($classes) . '">';

            if ($isSummaryRowHeadingCell) {
                $html .= e($heading);
            } elseif ((! $placeholderColumns) || $columnState['hasSummary']) {
                // These optional `div` siblings must not morph into the label or a
                // custom summarizer's `div` when responsive configuration changes.
                $html .= $this->renderMorphMarkerHtml();

                if (filled($summaryHeadingHiddenAt) && $columnState['hasSummary']) {
                    $html .= '<div class="fi-ta-summary-row-heading-fallback"><span data-visible-at="' . e(implode(' ', $summaryHeadingFallbackVisibleAt[$columnKey] ?? [])) . '">' . e($heading) . '</span></div>';
                }

                $html .= $this->renderMorphMarkerHtml(isEnd: true);
                $html .= $this->renderMorphMarkerHtml();

                if ($isStackedOnMobile) {
                    $html .= '<div class="fi-ta-cell-label">' . e($column->getLabel()) . '</div>';
                }

                $html .= $this->renderMorphMarkerHtml(isEnd: true);

                foreach ($this->iterateRenderLoop($columnState['summarizers']) as $summarizer) {
                    $html .= e($summarizer->query($query)->selectedState($selectedState));
                }
            }

            $html .= '</' . $summaryCellTag . '>';
        }

        return $html . $this->generateSummaryPlaceholderCellsHtml($placeholderColumns, $actions, $actionsPosition, $selectionEnabled, $recordCheckboxPosition, isBefore: false) . '</tr>';
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateSummaryPlaceholderCellsHtml(
        bool $placeholderColumns,
        bool $actions,
        ?RecordActionsPosition $actionsPosition,
        bool $selectionEnabled,
        ?RecordCheckboxPosition $recordCheckboxPosition,
        bool $isBefore,
    ): string {
        if (! $placeholderColumns) {
            return '';
        }

        $html = '';

        if ($actions && in_array($actionsPosition, $isBefore ? [RecordActionsPosition::BeforeCells, RecordActionsPosition::BeforeColumns] : [RecordActionsPosition::AfterColumns, RecordActionsPosition::AfterCells])) {
            $html .= '<td></td>';
        }

        if ($selectionEnabled && ($recordCheckboxPosition === ($isBefore ? RecordCheckboxPosition::BeforeCells : RecordCheckboxPosition::AfterCells))) {
            $html .= '<td></td>';
        }

        return $html;
    }
}
