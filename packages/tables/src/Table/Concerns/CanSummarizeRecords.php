<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Filament\Support\Components\ViewComponent;
use Filament\Support\Enums\Alignment;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Filament\Tables\Columns\Column;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Enums\RecordCheckboxPosition;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\ComponentSlot;

trait CanSummarizeRecords
{
    protected bool | Closure $hasPageSummary = true;

    protected bool | Closure $hasAllTableSummary = true;

    protected ?bool $cachedHasRenderSummary = null;

    /**
     * @var array<string, array<string, mixed>> | null
     */
    protected ?array $cachedRenderGroupedSummarySelectedState = null;

    public function summaries(bool | Closure $pageCondition = true, bool | Closure $allTableCondition = true): static
    {
        $this->hasPageSummary = $pageCondition;
        $this->hasAllTableSummary = $allTableCondition;

        return $this;
    }

    public function hasPageSummary(): bool
    {
        return (bool) $this->evaluate($this->hasPageSummary);
    }

    public function hasAllTableSummary(): bool
    {
        return (bool) $this->evaluate($this->hasAllTableSummary);
    }

    public function hasSummary(Builder | Closure | null $query): bool
    {
        foreach ($this->getColumns() as $column) {
            if ($column->hasSummary($query)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     *
     * @return array<string, array<string, mixed>>
     */
    protected function getRenderGroupedSummarySelectedState(): array
    {
        if ($this->cachedRenderGroupedSummarySelectedState !== null) {
            return $this->cachedRenderGroupedSummarySelectedState;
        }

        $group = $this->getGrouping();

        if (! $group) {
            return $this->cachedRenderGroupedSummarySelectedState = [];
        }

        $livewire = $this->getLivewire();

        return $this->cachedRenderGroupedSummarySelectedState = $livewire->getTableSummarySelectedState(
            $livewire->getAllTableSummaryQuery(),
            modifyQueryUsing: fn (QueryBuilder $query) => $group->groupQuery($query, model: $this->getQuery()->getModel()),
        );
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function hasRenderSummary(): bool
    {
        return $this->cachedHasRenderSummary ??= $this->hasSummary($this->getLivewire()->getAllTableSummaryQuery());
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
            return view('filament-tables::components.summary.index', [ // @phpstan-ignore argument.type (Published overrides only exist in consuming applications.)
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
        $html .= $this->generateMorphMarkerHtml();

        if ($hasPageSummary) {
            $html .= '<tr class="fi-ta-row fi-ta-summary-header-row fi-striped">';
            $html .= $this->generateMorphMarkerHtml();
            $html .= $this->generateSummaryPlaceholderCellsHtml($placeholderColumns, $actions, $actionsPosition, $selectionEnabled, $recordCheckboxPosition, isBefore: true);
            $html .= $this->generateMorphMarkerHtml(isEnd: true);

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

        $html .= $this->generateMorphMarkerHtml(isEnd: true);

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
            return view('filament-tables::components.summary.row', [ // @phpstan-ignore argument.type (Published overrides only exist in consuming applications.)
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
        $html .= $this->generateMorphMarkerHtml();
        $html .= $this->generateSummaryPlaceholderCellsHtml($placeholderColumns, $actions, $actionsPosition, $selectionEnabled, $recordCheckboxPosition, isBefore: true);
        $html .= $this->generateMorphMarkerHtml(isEnd: true);
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
                $html .= $this->generateMorphMarkerHtml();

                if (filled($summaryHeadingHiddenAt) && $columnState['hasSummary']) {
                    $html .= '<div class="fi-ta-summary-row-heading-fallback"><span data-visible-at="' . e(implode(' ', $summaryHeadingFallbackVisibleAt[$columnKey] ?? [])) . '">' . e($heading) . '</span></div>';
                }

                $html .= $this->generateMorphMarkerHtml(isEnd: true);
                $html .= $this->generateMorphMarkerHtml();

                if ($isStackedOnMobile) {
                    $html .= '<div class="fi-ta-cell-label">' . e($column->getLabel()) . '</div>';
                }

                $html .= $this->generateMorphMarkerHtml(isEnd: true);

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
