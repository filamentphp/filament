<?php

namespace Filament\Tables\Table\Concerns;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Contracts\HasActions;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\IconSize;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\Width;
use Filament\Support\Facades\FilamentView;
use Filament\Support\Icons\Heroicon;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Filament\Support\View\Concerns\CanGenerateBadgeHtml;
use Filament\Support\View\Concerns\CanGenerateDropdownHtml;
use Filament\Support\View\Concerns\CanGenerateLinkHtml;
use Filament\Support\View\Concerns\CanGenerateModalHtml;
use Filament\Support\View\Concerns\CanGeneratePaginationHtml;
use Filament\Support\View\Concerns\InteractsWithLivewireRendering;
use Filament\Tables\Actions\HeaderActionsPosition;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Enums\ColumnManagerLayout;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Enums\RecordCheckboxPosition;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Table;
use Filament\Tables\View\TablesIconAlias;
use Filament\Tables\View\TablesRenderHook;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Js;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\ComponentSlot;
use UnhandledMatchError;

trait CanBeRendered
{
    use CanGenerateBadgeHtml;
    use CanGenerateDropdownHtml;
    use CanGenerateLinkHtml;
    use CanGenerateModalHtml;
    use CanGeneratePaginationHtml;
    use CanRenderComponents;
    use InteractsWithLivewireRendering;

    /**
     * @var array{actionsByRecordKey: array<string, array<Action | ActionGroup>>, hasVisibleActions: bool} | null
     */
    protected ?array $cachedRenderRecordActionScan = null;

    protected ?int $cachedRenderAllSelectableRecordsCount = null;

    protected ?bool $cachedHasRenderSummary = null;

    /**
     * @var array<string, array<string, mixed>> | null
     */
    protected ?array $cachedRenderGroupedSummarySelectedState = null;

    /**
     * @return array{actionsByRecordKey: array<string, array<Action | ActionGroup>>, hasVisibleActions: bool}
     */
    protected function getRenderRecordActionScan(): array
    {
        if ($this->cachedRenderRecordActionScan !== null) {
            return $this->cachedRenderRecordActionScan;
        }

        $actionsByRecordKey = [];

        if ($this->isLoaded() && (! $this->isReordering())) {
            // Retain the hidden prefix and first visible clones for the row loop.
            foreach ($this->getRecords() as $record) {
                $actionsByRecordKey[$this->getRecordKey($record)] = $actions = $this->getVisibleRenderRecordActions($record);

                if ($actions !== []) {
                    return $this->cachedRenderRecordActionScan = ['actionsByRecordKey' => $actionsByRecordKey, 'hasVisibleActions' => true];
                }
            }
        }

        return $this->cachedRenderRecordActionScan = ['actionsByRecordKey' => $actionsByRecordKey, 'hasVisibleActions' => false];
    }

    protected function hasRenderRecordActions(): bool
    {
        return $this->getRenderRecordActionScan()['hasVisibleActions'];
    }

    /**
     * @param  Model | array<string, mixed>  $record
     * @return array<Action | ActionGroup>
     */
    protected function getRenderRecordActions(Model | array $record, string $recordKey): array
    {
        $scan = $this->getRenderRecordActionScan();

        return $scan['actionsByRecordKey'][$recordKey]
            ?? ($scan['hasVisibleActions'] ? $this->getVisibleRenderRecordActions($record) : []);
    }

    /**
     * @param  Model | array<string, mixed>  $record
     * @return array<Action | ActionGroup>
     */
    protected function getVisibleRenderRecordActions(Model | array $record): array
    {
        $actions = [];

        foreach ($this->getRecordActions() as $action) {
            $action = $action->getClone();

            if (! $action instanceof BulkAction) {
                $action->record($record);
            }

            if ($action->isHidden()) {
                continue;
            }

            $actions[] = $action;
        }

        return $actions;
    }

    /**
     * @return array<Action | ActionGroup>
     */
    protected function getRenderHeaderActions(): array
    {
        return array_filter(
            $this->getHeaderActions(),
            static fn (Action | ActionGroup $action): bool => $action->isVisible(),
        );
    }

    protected function hasRenderNonBulkToolbarAction(): bool
    {
        foreach ($this->getRenderToolbarActions() as $toolbarAction) {
            if ($toolbarAction instanceof BulkActionGroup) {
                continue;
            }

            if ($toolbarAction instanceof ActionGroup) {
                if ($toolbarAction->hasNonBulkAction()) {
                    return true;
                }

                continue;
            }

            if (! $toolbarAction->isBulk()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<Action | ActionGroup>
     */
    protected function getRenderToolbarActions(): array
    {
        return array_filter(
            $this->getToolbarActions(),
            static fn (Action | ActionGroup $action): bool => $action->isVisible(),
        );
    }

    protected function hasRenderHeader(): bool
    {
        return $this->getHeader() || $this->getHeading() || $this->getDescription() || ($this->getRenderHeaderActions() && (! $this->isReordering())) || $this->isReorderable() || $this->areRenderGroupingSettingsVisible() || $this->isSearchable() || $this->isFilterable() || count($this->getFilterIndicators()) || $this->hasColumnManager();
    }

    protected function getRenderHeaderVisibilityMode(): string
    {
        return ($this->hasRenderHeader() || $this->hasRenderNonBulkToolbarAction())
            ? 'visible'
            : (count($this->getRenderToolbarActions()) ? 'selection' : 'hidden');
    }

    protected function hasRenderHeaderToolbar(): bool
    {
        return $this->isReorderable() || $this->areRenderGroupingSettingsVisible() || $this->isSearchable() || $this->hasRenderFiltersTrigger() || $this->hasColumnManager();
    }

    protected function getRenderHeaderToolbarVisibilityMode(): string
    {
        return ($this->hasRenderHeaderToolbar() || $this->hasRenderNonBulkToolbarAction())
            ? 'visible'
            : (count($this->getRenderToolbarActions()) ? 'selection' : 'hidden');
    }

    /**
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
            modifyQueryUsing: fn (Builder $query) => $group->groupQuery($query, model: $this->getQuery()->getModel()),
        );
    }

    protected function areRenderGroupingSettingsVisible(): bool
    {
        return (! $this->isReordering()) && count($this->getGroups()) && (! $this->areGroupingSettingsHidden());
    }

    protected function isRenderGroupsOnly(): bool
    {
        return $this->isGroupsOnly() && $this->getGrouping();
    }

    protected function isRenderSelectionEnabled(): bool
    {
        return (! $this->isRenderGroupsOnly()) && $this->isSelectionEnabled();
    }

    protected function getRenderColumnsCount(): int
    {
        return count($this->getVisibleColumns())
            + (int) ($this->hasRenderRecordActions() && (! $this->isReordering()))
            + (int) ($this->isRenderSelectionEnabled() || $this->isReordering());
    }

    protected function hasRenderFiltersInLayout(FiltersLayout ...$layouts): bool
    {
        return $this->isFilterable() && in_array($this->getFiltersLayout(), $layouts);
    }

    protected function hasRenderCollapsibleFilters(): bool
    {
        return $this->hasRenderFiltersInLayout(FiltersLayout::AboveContentCollapsible, FiltersLayout::BeforeContentCollapsible, FiltersLayout::AfterContentCollapsible);
    }

    protected function hasRenderFiltersTrigger(): bool
    {
        return $this->isFilterable() && in_array($this->getFiltersLayout(), [
            FiltersLayout::Dropdown,
            FiltersLayout::Modal,
            FiltersLayout::BeforeContent,
            FiltersLayout::BeforeContentCollapsible,
            FiltersLayout::AfterContent,
            FiltersLayout::AfterContentCollapsible,
        ]);
    }

    protected function getRenderRecordActionsAlignment(): Alignment | string | null
    {
        $alignment = $this->getRecordActionsAlignment();

        return filled($alignment) ? (Alignment::tryFrom($alignment) ?? $alignment) : null;
    }

    protected function getRenderFiltersFormWidth(): Width | string | null
    {
        $width = $this->getFiltersFormWidth();

        return is_string($width) ? (Width::tryFrom($width) ?? $width) : $width;
    }

    protected function hasRenderSummary(): bool
    {
        return $this->cachedHasRenderSummary ??= $this->hasSummary($this->getLivewire()->getAllTableSummaryQuery());
    }

    protected function getRenderAllSelectableRecordsCount(): ?int
    {
        if ((! $this->isRenderSelectionEnabled()) || (! $this->isLoaded())) {
            return null;
        }

        if ($this->cachedRenderAllSelectableRecordsCount !== null) {
            return $this->cachedRenderAllSelectableRecordsCount;
        }

        // The Livewire count may use its cached records directly.
        $this->getRecords();

        return $this->cachedRenderAllSelectableRecordsCount = $this->getAllSelectableRecordsCount();
    }

    protected function hasRenderPagination(): bool
    {
        $records = $this->isLoaded() ? $this->getRecords() : null;

        return (($records instanceof Paginator) || ($records instanceof \Illuminate\Contracts\Pagination\CursorPaginator))
            && (($records instanceof LengthAwarePaginator) ? (bool) $records->total() : $records->isNotEmpty());
    }

    public function toEmbeddedHtml(): string
    {
        $viewFactory = app('view');

        // Isolate record-dependent results across repeated and nested renders.
        $previousRecordActionScan = $this->cachedRenderRecordActionScan;
        $previousAllSelectableRecordsCount = $this->cachedRenderAllSelectableRecordsCount;
        $previousHasSummary = $this->cachedHasRenderSummary;
        $previousGroupedSummarySelectedState = $this->cachedRenderGroupedSummarySelectedState;
        $this->cachedRenderRecordActionScan = null;
        $this->cachedRenderAllSelectableRecordsCount = null;
        $this->cachedHasRenderSummary = null;
        $this->cachedRenderGroupedSummarySelectedState = null;
        $bufferLevel = ob_get_level();
        $viewFactory->incrementRender();

        try {
            return $this->generateTableHtml();
        } finally {
            while (ob_get_level() > $bufferLevel) {
                ob_end_clean();
            }

            $this->cachedRenderRecordActionScan = $previousRecordActionScan;
            $this->cachedRenderAllSelectableRecordsCount = $previousAllSelectableRecordsCount;
            $this->cachedHasRenderSummary = $previousHasSummary;
            $this->cachedRenderGroupedSummarySelectedState = $previousGroupedSummarySelectedState;
            $viewFactory->decrementRender();
            $viewFactory->flushStateIfDoneRendering();
        }
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateTableHtml(): string
    {
        $livewire = $this->getLivewire();
        $records = $this->isLoaded() ? $this->getRecords() : null;
        $contentRenderHookData = [
            'hasPagination' => $this->hasRenderPagination(),
            'records' => $records,
            'table' => $livewire->getTable(),
        ];

        ob_start(); ?>
        <div
            <?php if (! $this->isLoaded()) { ?>
                wire:init="loadTable"
            <?php } ?>
            x-data="filamentTable({
                        areGroupsCollapsedByDefault: <?= Js::from($this->areGroupsCollapsedByDefault())->toHtml() ?>,
                        canTrackDeselectedRecords: <?= Js::from($this->canTrackDeselectedRecords())->toHtml() ?>,
                        currentSelectionLivewireProperty: <?= Js::from($this->getCurrentSelectionLivewireProperty())->toHtml() ?>,
                        maxSelectableRecords: <?= Js::from($this->getMaxSelectableRecords())->toHtml() ?>,
                        reorderMessage: <?= Js::from(__('filament-tables::table.reordering.moved'))->toHtml() ?>,
                        selectsCurrentPageOnly: <?= Js::from($this->selectsCurrentPageOnly())->toHtml() ?>,
                    })"
            <?= e($this->getExtraAttributeBag()->class([
                'fi-ta',
                'fi-loading' => $records === null,
            ])); ?>

        >
            <input
                type="hidden"
                value="<?= e($this->getRenderAllSelectableRecordsCount()); ?>"
                x-ref="allSelectableRecordsCount"
            />

            <?= $this->renderMorphMarkerHtml(); ?>
            <?php if ($this->isReorderable()) { ?>
                <span
                    wire:ignore
                    x-ref="tableReorderStatus"
                    role="status"
                    aria-live="polite"
                    aria-atomic="true"
                    class="fi-sr-only"
                ></span>
            <?php } ?>
            <?= $this->renderMorphMarkerHtml(isEnd: true); ?>

            <div
                class="<?= Arr::toCssClasses([
                    'fi-ta-ctn',
                    'fi-ta-ctn-with-content-layout' => $this->getContent() || $this->hasColumnsLayout(),
                    'fi-ta-ctn-with-footer' => $this->hasRenderPagination() || (($records !== null) && ! count($records)) || $this->hasRenderFiltersInLayout(FiltersLayout::BelowContent),
                    'fi-ta-ctn-with-header' => $this->hasRenderHeader(),
                ]); ?>"
            >
                <?= $this->generateFiltersBeforeContentHtml(); ?>

                <div class="fi-ta-main">
                    <?= $this->generateHeaderHtml(); ?>

                    <?= $this->renderMorphMarkerHtml(); ?>
                    <?php if ($this->isReordering()) { ?>
                        <?= $this->generateReorderingIndicatorHtml(); ?>
                    <?php } elseif ($this->isRenderSelectionEnabled() && ($this->getMaxSelectableRecords() !== 1) && $this->isLoaded()) { ?>
                        <?= $this->generateSelectionIndicatorHtml(); ?>
                    <?php } ?>
                    <?= $this->renderMorphMarkerHtml(isEnd: true); ?>

                    <?= $this->generateFilterIndicatorsHtml(); ?>

                    <?= e(FilamentView::renderHook(TablesRenderHook::CONTENT_BEFORE, scopes: $livewire::class, data: $contentRenderHookData)); ?>

                    <?= $this->generateContentHtml(); ?>

                    <?= $this->generateEmptyStateHtml(); ?>

                    <?= e(FilamentView::renderHook(TablesRenderHook::CONTENT_AFTER, scopes: $livewire::class, data: $contentRenderHookData)); ?>

                    <?= $this->generatePaginationContainerHtml(); ?>

                    <?= $this->generateFiltersBelowContentHtml(); ?>
                </div>

                <?= $this->generateFiltersAfterContentHtml(); ?>
            </div>

            <?= $this->generateActionModalsHtml(); ?>
        </div>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateFiltersBeforeContentHtml(): string
    {
        ob_start(); ?>
        <?= $this->renderMorphMarkerHtml(); ?>        <?php if ($this->hasRenderFiltersInLayout(FiltersLayout::BeforeContent, FiltersLayout::BeforeContentCollapsible)) { ?>
            <div
                wire:ignore.self
                x-ref="filtersContentContainer"
                x-transition:enter-start="fi-opacity-0"
                x-transition:leave-end="fi-opacity-0"
                x-bind:class="{ 'fi-open': areFiltersOpen }"
                class="<?= Arr::toCssClasses([
                    'fi-ta-filters-before-content-ctn',
                    'lg:fi-open' => ! $this->hasRenderCollapsibleFilters(),
                    (($filtersFormWidth = $this->getRenderFiltersFormWidth() ?? Width::ExtraSmall) instanceof Width) ? "fi-width-{$filtersFormWidth->value}" : $filtersFormWidth,
                ]); ?>"
            >
                <?= $this->generateFiltersHtml(
                    applyAction: $this->getFiltersApplyAction(),
                    form: $this->getFiltersForm(),
                    headingTag: $this->getHeadingTag($this->getHeading() ? 1 : 0),
                    resetAction: $this->getFiltersResetAction(),
                    resetActionPosition: $this->getFiltersResetActionPosition(),
                    attributes: new ComponentAttributeBag(['class' => 'fi-ta-filters-before-content']),
                ); ?>
            </div>
        <?php } ?><?= $this->renderMorphMarkerHtml(isEnd: true); ?>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateHeaderHtml(): string
    {
        $livewire = $this->getLivewire();

        ob_start(); ?>
        <div
            <?php if (! $this->hasRenderHeader()) { ?> x-cloak <?php } ?>
            x-show="<?= Js::from($this->hasRenderHeader())->toHtml() ?> || <?= Js::from($this->hasRenderNonBulkToolbarAction())->toHtml() ?> || (getSelectedRecordsCount() && <?= Js::from(count($this->getRenderToolbarActions()))->toHtml() ?>)"
            <?php $this->setRenderKey('' . e($this->getLivewire()->getId()) . '.table.header.' . e($this->getRenderHeaderVisibilityMode()) . ''); ?>wire:key="<?= e($this->getLivewire()->getId()); ?>.table.header.<?= e($this->getRenderHeaderVisibilityMode()); ?>"
            class="fi-ta-header-ctn"
        >
            <?= e(FilamentView::renderHook(TablesRenderHook::HEADER_BEFORE, scopes: $livewire::class)); ?>

        <?= $this->generateHeaderContentHtml(); ?>

            <?= e(FilamentView::renderHook(TablesRenderHook::HEADER_AFTER, scopes: $livewire::class)); ?>

        <?= $this->generateFiltersAboveContentHtml(); ?>

            <?= e(FilamentView::renderHook(TablesRenderHook::TOOLBAR_BEFORE, scopes: $livewire::class)); ?>

            <?= $this->generateToolbarHtml(); ?>

            <?= e(FilamentView::renderHook(TablesRenderHook::TOOLBAR_AFTER, scopes: $livewire::class)); ?>

        </div>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateHeaderContentHtml(): string
    {
        $header = $this->getHeader();
        $description = $this->getDescription();
        $heading = $this->getHeading();

        ob_start(); ?>
        <?= $this->renderMorphMarkerHtml(); ?>                <?php if ($header) { ?>
            <?= e($header); ?>

        <?php } elseif ($heading || $description || ($this->getRenderHeaderActions() && (! $this->isReordering()))) { ?>
            <div
                class="<?= Arr::toCssClasses([
                    'fi-ta-header',
                    'fi-ta-header-adaptive-actions-position' => $this->getRenderHeaderActions() && (! $this->isReordering()) && ($this->getHeaderActionsPosition() === HeaderActionsPosition::Adaptive),
                ]); ?>"
            >
                <?= $this->renderMorphMarkerHtml(); ?>
                <?php if ($heading || $description) { ?>
                    <div>
                        <?= $this->renderMorphMarkerHtml(); ?>
                        <?php if ($heading) { ?>
                            <<?= e($this->getHeadingTag()); ?>

                                class="fi-ta-header-heading"
                            >
                                <?= e($heading); ?>

                            </<?= e($this->getHeadingTag()); ?>>
                        <?php } ?>
                        <?= $this->renderMorphMarkerHtml(isEnd: true); ?>

                        <?php if ($description) { ?>
                            <p class="fi-ta-header-description">
                                <?= e($description); ?>

                            </p>
                        <?php } ?>
                    </div>
                <?php } ?>
                <?= $this->renderMorphMarkerHtml(isEnd: true); ?>

                <?php if ((! $this->isReordering()) && $this->getRenderHeaderActions()) { ?>
                    <div
                        class="fi-ta-actions fi-align-start fi-wrapped"
                    >
                        <?php foreach ($this->iterateRenderLoop($this->getRenderHeaderActions()) as $action) {  ?>
                            <?= e($action); ?>

                        <?php }  ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?><?= $this->renderMorphMarkerHtml(isEnd: true); ?>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateFiltersAboveContentHtml(): string
    {
        ob_start(); ?>
        <?= $this->renderMorphMarkerHtml(); ?>                <?php if ($this->hasRenderFiltersInLayout(FiltersLayout::AboveContent, FiltersLayout::AboveContentCollapsible)) { ?>
            <div
                <?php if ($this->hasRenderCollapsibleFilters()) { ?>
                    x-bind:class="{ 'fi-open': areFiltersOpen }"
                <?php } ?>
                class="<?= Arr::toCssClasses([
                    'fi-ta-filters-above-content-ctn',
                ]); ?>"
            >
                <?= $this->generateFiltersHtml(
                    applyAction: $this->getFiltersApplyAction(),
                    form: $this->getFiltersForm(),
                    headingTag: $this->getHeadingTag($this->getHeading() ? 1 : 0),
                    resetAction: $this->getFiltersResetAction(),
                    resetActionPosition: $this->getFiltersResetActionPosition(),
                    attributes: new ComponentAttributeBag(['x-cloak' => true, 'x-show' => BladeCompiler::sanitizeComponentAttribute($this->hasRenderCollapsibleFilters() ? 'areFiltersOpen' : null)]),
                ); ?>

                <?php if ($this->hasRenderCollapsibleFilters()) { ?>
                    <span
                        x-on:click="areFiltersOpen = ! areFiltersOpen"
                        class="fi-ta-filters-trigger-action-ctn"
                    >
                        <?= e($this->getFiltersTriggerAction()->badge((string) $this->getActiveFiltersCount())); ?>

                    </span>
                <?php } ?>
            </div>
        <?php } ?><?= $this->renderMorphMarkerHtml(isEnd: true); ?>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateToolbarHtml(): string
    {
        $livewire = $this->getLivewire();

        ob_start(); ?>
        <div
            <?php if (! $this->hasRenderHeaderToolbar()) { ?> x-cloak <?php } ?>
            x-show="<?= Js::from($this->hasRenderHeaderToolbar())->toHtml() ?> || <?= Js::from($this->hasRenderNonBulkToolbarAction())->toHtml() ?> || (getSelectedRecordsCount() && <?= Js::from(count($this->getRenderToolbarActions()))->toHtml() ?>)"
            <?php $this->setRenderKey('' . e($this->getLivewire()->getId()) . '.table.header-toolbar.' . e($this->getRenderHeaderToolbarVisibilityMode()) . ''); ?>wire:key="<?= e($this->getLivewire()->getId()); ?>.table.header-toolbar.<?= e($this->getRenderHeaderToolbarVisibilityMode()); ?>"
            class="fi-ta-header-toolbar"
        >
            <?= e(FilamentView::renderHook(TablesRenderHook::TOOLBAR_START, scopes: $livewire::class)); ?>

            <?= $this->generateToolbarActionsHtml(); ?>

            <?= $this->renderMorphMarkerHtml(); ?>
            <?php if ($this->isSearchable() || $this->hasRenderFiltersTrigger() || $this->hasColumnManager()) { ?>
                <div>
                    <?= e(FilamentView::renderHook(TablesRenderHook::TOOLBAR_SEARCH_BEFORE, scopes: $livewire::class)); ?>

                    <?= $this->renderMorphMarkerHtml(); ?>
                    <?= $this->generateGlobalSearchHtml(); ?>
                    <?= $this->renderMorphMarkerHtml(isEnd: true); ?>

                    <?= e(FilamentView::renderHook(TablesRenderHook::TOOLBAR_SEARCH_AFTER, scopes: $livewire::class)); ?>

                    <?php if ($this->hasRenderFiltersTrigger() || $this->hasColumnManager()) { ?>
                        <?= $this->renderMorphMarkerHtml(); ?>
                        <?= $this->generateFiltersTriggerHtml(); ?>
                        <?= $this->renderMorphMarkerHtml(isEnd: true); ?>

                        <?= e(FilamentView::renderHook(TablesRenderHook::TOOLBAR_COLUMN_MANAGER_TRIGGER_BEFORE, scopes: $livewire::class)); ?>

                        <?= $this->renderMorphMarkerHtml(); ?>
                        <?= $this->generateColumnManagerTriggerHtml(); ?>
                        <?= $this->renderMorphMarkerHtml(isEnd: true); ?>

                        <?= e(FilamentView::renderHook(TablesRenderHook::TOOLBAR_COLUMN_MANAGER_TRIGGER_AFTER, scopes: $livewire::class)); ?>

                    <?php } ?>
                </div>
            <?php } ?>
            <?= $this->renderMorphMarkerHtml(isEnd: true); ?>

            <?= e(FilamentView::renderHook(TablesRenderHook::TOOLBAR_END, scopes: $livewire::class)); ?>

        </div>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateToolbarActionsHtml(): string
    {
        $livewire = $this->getLivewire();

        ob_start(); ?>
        <div class="fi-ta-actions fi-align-start fi-wrapped">
            <?= e(FilamentView::renderHook(TablesRenderHook::TOOLBAR_REORDER_TRIGGER_BEFORE, scopes: $livewire::class)); ?>

        <?= $this->renderMorphMarkerHtml(); ?>                        <?php if ($this->isReorderable()) { ?>
                <?= e($this->getReorderRecordsTriggerAction($this->isReordering())); ?>

            <?php } ?><?= $this->renderMorphMarkerHtml(isEnd: true); ?>

            <?= e(FilamentView::renderHook(TablesRenderHook::TOOLBAR_REORDER_TRIGGER_AFTER, scopes: $livewire::class)); ?>

        <?= $this->renderMorphMarkerHtml(); ?>                        <?php if ((! $this->isReordering()) && count($this->getRenderToolbarActions())) { ?>
                <?php foreach ($this->iterateRenderLoop($this->getRenderToolbarActions()) as $action) {  ?>
                    <?= e($action); ?>

                <?php }  ?>
            <?php } ?><?= $this->renderMorphMarkerHtml(isEnd: true); ?>

            <?= e(FilamentView::renderHook(TablesRenderHook::TOOLBAR_GROUPING_SELECTOR_BEFORE, scopes: $livewire::class)); ?>

            <?= $this->generateGroupingSettingsHtml(); ?>

            <?= e(FilamentView::renderHook(TablesRenderHook::TOOLBAR_GROUPING_SELECTOR_AFTER, scopes: $livewire::class)); ?>

        </div>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateGroupingSettingsHtml(): string
    {
        ob_start(); ?>
        <?= $this->renderMorphMarkerHtml(); ?>
        <?php if ($this->areRenderGroupingSettingsVisible()) { ?>
            <div
                x-data="filamentTableGrouping"
                class="fi-ta-grouping-settings"
            >
                <?php $dropdownArguments = [
                    'menu' => false,
                    'placement' => 'bottom-start',
                    'shift' => true,
                    'width' => 'xs',
                    'attributes' => new ComponentAttributeBag([
                        'wire:key' => BladeCompiler::sanitizeComponentAttribute($this->getLivewire()->getId() . '.table.grouping'),
                        'class' => BladeCompiler::sanitizeComponentAttribute(Arr::toCssClasses(['sm:fi-hidden' => ! $this->areGroupingSettingsInDropdownOnDesktop()])),
                    ]),
                ];
            ob_start(); ?>
                     <?php $dropdownTrigger = new ComponentSlot(trim(e($this->getGroupRecordsTriggerAction()))); ?>

                    <div class="fi-ta-grouping-settings-fields">
                        <label>
                            <span>
                                <?= e(__('filament-tables::table.grouping.fields.group.label')); ?>

                            </span>

                            <?php $inputWrapperArguments = ['attributes' => new ComponentAttributeBag([])];
            ob_start(); ?>
                                <?php $inputSelectArguments = ['attributes' => new ComponentAttributeBag(['x-model' => 'group', 'x-on:change' => 'resetCollapsedGroups()'])];
            ob_start(); ?>
                                    <option value="">-</option>

                                    <?php foreach ($this->iterateRenderLoop($this->getGroups()) as $groupOption) {  ?>
                                        <option
                                            value="<?= e($groupOption->getId()); ?>"
                                        >
                                            <?= e($groupOption->getLabel()); ?>

                                        </option>
                                    <?php }  ?>
                                 <?= $this->generateInputSelectHtml(...$inputSelectArguments, slot: new ComponentSlot(trim(ob_get_clean()))); ?>
                             <?= $this->generateInputWrapperHtml(...$inputWrapperArguments, slot: new ComponentSlot(trim(ob_get_clean()))); ?>
                        </label>

                        <?php if (! $this->isGroupingDirectionSettingHidden()) { ?>
                            <label x-cloak x-show="group">
                                <span>
                                    <?= e(__('filament-tables::table.grouping.fields.direction.label')); ?>

                                </span>

                                <?php $inputWrapperArguments = ['attributes' => new ComponentAttributeBag([])];
                            ob_start(); ?>
                                    <?php $inputSelectArguments = ['attributes' => new ComponentAttributeBag(['x-model' => 'direction'])];
                            ob_start(); ?>
                                        <option value="asc">
                                            <?= e(__('filament-tables::table.grouping.fields.direction.options.asc')); ?>

                                        </option>

                                        <option value="desc">
                                            <?= e(__('filament-tables::table.grouping.fields.direction.options.desc')); ?>

                                        </option>
                                     <?= $this->generateInputSelectHtml(...$inputSelectArguments, slot: new ComponentSlot(trim(ob_get_clean()))); ?>
                                 <?= $this->generateInputWrapperHtml(...$inputWrapperArguments, slot: new ComponentSlot(trim(ob_get_clean()))); ?>
                            </label>
                        <?php } ?>
                    </div>
                 <?= $this->generateDropdownHtml(...$dropdownArguments, slot: new ComponentSlot(trim(ob_get_clean())), trigger: $dropdownTrigger); ?>

                <?php if (! $this->areGroupingSettingsInDropdownOnDesktop()) { ?>
                    <div class="fi-ta-grouping-settings-fields">
                        <label>
                            <?php $inputWrapperArguments = ['prefix' => __('filament-tables::table.grouping.fields.group.label'), 'attributes' => new ComponentAttributeBag([])];
                    ob_start(); ?>
                                <?php $inputSelectArguments = ['attributes' => new ComponentAttributeBag(['x-model' => 'group', 'x-on:change' => 'resetCollapsedGroups()'])];
                    ob_start(); ?>
                                    <option value="">-</option>

                                    <?php foreach ($this->iterateRenderLoop($this->getGroups()) as $groupOption) {  ?>
                                        <option
                                            value="<?= e($groupOption->getId()); ?>"
                                        >
                                            <?= e($groupOption->getLabel()); ?>

                                        </option>
                                    <?php }  ?>
                                 <?= $this->generateInputSelectHtml(...$inputSelectArguments, slot: new ComponentSlot(trim(ob_get_clean()))); ?>
                             <?= $this->generateInputWrapperHtml(...$inputWrapperArguments, slot: new ComponentSlot(trim(ob_get_clean()))); ?>
                        </label>

                        <?php if (! $this->isGroupingDirectionSettingHidden()) { ?>
                            <label x-cloak x-show="group">
                                <span class="fi-sr-only">
                                    <?= e(__('filament-tables::table.grouping.fields.direction.label')); ?>

                                </span>

                                <?php $inputWrapperArguments = ['attributes' => new ComponentAttributeBag([])];
                            ob_start(); ?>
                                    <?php $inputSelectArguments = ['attributes' => new ComponentAttributeBag(['x-model' => 'direction'])];
                            ob_start(); ?>
                                        <option value="asc">
                                            <?= e(__('filament-tables::table.grouping.fields.direction.options.asc')); ?>

                                        </option>

                                        <option value="desc">
                                            <?= e(__('filament-tables::table.grouping.fields.direction.options.desc')); ?>

                                        </option>
                                     <?= $this->generateInputSelectHtml(...$inputSelectArguments, slot: new ComponentSlot(trim(ob_get_clean()))); ?>
                                 <?= $this->generateInputWrapperHtml(...$inputWrapperArguments, slot: new ComponentSlot(trim(ob_get_clean()))); ?>
                            </label>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
        <?= $this->renderMorphMarkerHtml(isEnd: true); ?>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateGlobalSearchHtml(): string
    {
        ob_start(); ?>
        <?php if ($this->isSearchable()) { ?>
            <?php
                $searchPlaceholder = $this->getSearchPlaceholder();
            ?>

            <?= $this->generateSearchFieldHtml(
                debounce: $this->getSearchDebounce(),
                onBlur: $this->isSearchOnBlur(),
                placeholder: $searchPlaceholder,
                attributes: new ComponentAttributeBag([]),
            ); ?>
        <?php } ?>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateFiltersTriggerHtml(): string
    {
        $filtersTriggerAction = $this->hasRenderFiltersTrigger() ? $this->getFiltersTriggerAction() : null;

        ob_start(); ?>
        <?php if ($this->hasRenderFiltersInLayout(FiltersLayout::Dropdown, FiltersLayout::Modal)) { ?>
            <?php if (($this->getFiltersLayout() === FiltersLayout::Modal) || $filtersTriggerAction->isModalSlideOver()) { ?>
                <?php
                    $filtersTriggerActionModalAlignment = $filtersTriggerAction->getModalAlignment();
                $filtersTriggerActionIsModalAutofocused = $filtersTriggerAction->isModalAutofocused();
                $filtersTriggerActionIsModalClickThrough = $filtersTriggerAction->isModalClickThrough();
                $filtersTriggerActionHasModalCloseButton = $filtersTriggerAction->hasModalCloseButton();
                $filtersTriggerActionIsModalClosedByClickingAway = $filtersTriggerAction->isModalClosedByClickingAway();
                $filtersTriggerActionIsModalClosedByEscaping = $filtersTriggerAction->isModalClosedByEscaping();
                $filtersTriggerActionModalDescription = $filtersTriggerAction->getModalDescription();
                $filtersTriggerActionModalEyebrow = $filtersTriggerAction->getModalEyebrow();
                $filtersTriggerActionExtraModalWindowAttributeBag = $filtersTriggerAction->getExtraModalWindowAttributeBag();
                $filtersTriggerActionExtraModalOverlayAttributeBag = $filtersTriggerAction->getExtraModalOverlayAttributeBag();
                $filtersTriggerActionVisibleModalFooterActions = $filtersTriggerAction->getVisibleModalFooterActions();
                $filtersTriggerActionModalFooterActionsAlignment = $filtersTriggerAction->getModalFooterActionsAlignment();
                $filtersTriggerActionModalHeading = $filtersTriggerAction->getCustomModalHeading() ?? __('filament-tables::table.filters.heading');
                $filtersTriggerActionModalIcon = $filtersTriggerAction->getModalIcon();
                $filtersTriggerActionModalIconColor = $filtersTriggerAction->getModalIconColor();
                $filtersTriggerActionIsModalSlideOver = $filtersTriggerAction->isModalSlideOver();
                $filtersTriggerActionModalSlideOverPosition = $filtersTriggerAction->getModalSlideOverPosition();
                $filtersTriggerActionIsModalFooterSticky = $filtersTriggerAction->isModalFooterSticky();
                $filtersTriggerActionIsModalHeaderSticky = $filtersTriggerAction->isModalHeaderSticky();
                ?>

                <?php $modalArguments = [
                    'alignment' => $filtersTriggerActionModalAlignment,
                    'autofocus' => $filtersTriggerActionIsModalAutofocused,
                    'clickThrough' => $filtersTriggerActionIsModalClickThrough,
                    'closeButton' => $filtersTriggerActionHasModalCloseButton,
                    'closeByClickingAway' => $filtersTriggerActionIsModalClosedByClickingAway,
                    'closeByEscaping' => $filtersTriggerActionIsModalClosedByEscaping,
                    'description' => $filtersTriggerActionModalDescription,
                    'eyebrow' => $filtersTriggerActionModalEyebrow,
                    'extraModalWindowAttributeBag' => $filtersTriggerActionExtraModalWindowAttributeBag,
                    'extraModalOverlayAttributeBag' => $filtersTriggerActionExtraModalOverlayAttributeBag,
                    'footerActions' => $filtersTriggerActionVisibleModalFooterActions,
                    'footerActionsAlignment' => $filtersTriggerActionModalFooterActionsAlignment,
                    'heading' => $filtersTriggerActionModalHeading,
                    'icon' => $filtersTriggerActionModalIcon,
                    'iconColor' => $filtersTriggerActionModalIconColor,
                    'slideOver' => $filtersTriggerActionIsModalSlideOver,
                    'slideOverPosition' => $filtersTriggerActionModalSlideOverPosition,
                    'stickyFooter' => $filtersTriggerActionIsModalFooterSticky,
                    'stickyHeader' => $filtersTriggerActionIsModalHeaderSticky,
                    'width' => $this->getRenderFiltersFormWidth(),
                    'attributes' => new ComponentAttributeBag(['wire:key' => BladeCompiler::sanitizeComponentAttribute($this->getLivewire()->getId() . '.table.filters'), 'class' => 'fi-ta-filters-modal']),
                ];
                ob_start(); ?>
                     <?php $modalTrigger = new ComponentSlot(trim(e($filtersTriggerAction->badge((string) $this->getActiveFiltersCount())))); ?>

                    <?= e($filtersTriggerAction->getModalContent()); ?>

                    <?= e($this->getFiltersForm()); ?>

                    <?= e($filtersTriggerAction->getModalContentFooter()); ?>

                 <?= $this->generateModalHtml(...$modalArguments, slot: new ComponentSlot(trim(ob_get_clean())), trigger: $modalTrigger, livewireId: $this->getLivewire()->getId()); ?>
            <?php } else { ?>
                <?php $dropdownArguments = [
                    'menu' => false,
                    'maxHeight' => $this->getFiltersFormMaxHeight(),
                    'placement' => 'bottom-end',
                    'shift' => true,
                    'flip' => false,
                    'width' => $this->getRenderFiltersFormWidth() ?? Width::ExtraSmall,
                    'attributes' => new ComponentAttributeBag(['wire:key' => BladeCompiler::sanitizeComponentAttribute($this->getLivewire()->getId() . '.table.filters'), 'class' => 'fi-ta-filters-dropdown']),
                ];
                ob_start(); ?>
                     <?php $dropdownTrigger = new ComponentSlot(trim(e($filtersTriggerAction->badge((string) $this->getActiveFiltersCount())))); ?>

                    <?= $this->generateFiltersHtml(
                        applyAction: $this->getFiltersApplyAction(),
                        form: $this->getFiltersForm(),
                        headingTag: $this->getHeadingTag($this->getHeading() ? 1 : 0),
                        resetAction: $this->getFiltersResetAction(),
                        resetActionPosition: $this->getFiltersResetActionPosition(),
                        attributes: new ComponentAttributeBag(['x-data' => 'filamentTableFilters', 'x-on:click' => 'handleActionClick($event)', 'tabindex' => '-1']),
                    ); ?>
                 <?= $this->generateDropdownHtml(...$dropdownArguments, slot: new ComponentSlot(trim(ob_get_clean())), trigger: $dropdownTrigger); ?>
            <?php } ?>
        <?php } elseif ($this->hasRenderFiltersInLayout(FiltersLayout::BeforeContent, FiltersLayout::BeforeContentCollapsible, FiltersLayout::AfterContent, FiltersLayout::AfterContentCollapsible)) { ?>
            <span
                x-ref="filtersTriggerActionContainer"
                x-on:click="toggleFiltersDropdown"
                class="<?= Arr::toCssClasses([
                    'fi-ta-filters-trigger-action-ctn',
                    'lg:fi-hidden' => ! $this->hasRenderCollapsibleFilters(),
                ]); ?>"
            >
                <?= e($filtersTriggerAction->badge((string) $this->getActiveFiltersCount())); ?>

            </span>
        <?php } ?>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateColumnManagerTriggerHtml(): string
    {
        ob_start(); ?>
        <?php if ($this->hasColumnManager()) { ?>
            <?php
                $columnManagerMaxHeight = $this->getColumnManagerMaxHeight();
            $columnManagerWidth = $this->getColumnManagerWidth();
            $columnManagerColumns = $this->getColumnManagerColumns();
            $columnManagerApplyAction = $this->getColumnManagerApplyAction();
            $columnManagerTriggerAction = $this->getColumnManagerTriggerAction();
            ?>

            <?php if (($this->getColumnManagerLayout() === ColumnManagerLayout::Modal) || $columnManagerTriggerAction->isModalSlideOver()) { ?>
                <?php
                    $columnManagerTriggerActionModalAlignment = $columnManagerTriggerAction->getModalAlignment();
                $columnManagerTriggerActionIsModalAutofocused = $columnManagerTriggerAction->isModalAutofocused();
                $columnManagerTriggerActionIsModalClickThrough = $columnManagerTriggerAction->isModalClickThrough();
                $columnManagerTriggerActionHasModalCloseButton = $columnManagerTriggerAction->hasModalCloseButton();
                $columnManagerTriggerActionIsModalClosedByClickingAway = $columnManagerTriggerAction->isModalClosedByClickingAway();
                $columnManagerTriggerActionIsModalClosedByEscaping = $columnManagerTriggerAction->isModalClosedByEscaping();
                $columnManagerTriggerActionModalDescription = $columnManagerTriggerAction->getModalDescription();
                $columnManagerTriggerActionModalEyebrow = $columnManagerTriggerAction->getModalEyebrow();
                $columnManagerTriggerActionExtraModalWindowAttributeBag = $columnManagerTriggerAction->getExtraModalWindowAttributeBag();
                $columnManagerTriggerActionExtraModalOverlayAttributeBag = $columnManagerTriggerAction->getExtraModalOverlayAttributeBag();
                $columnManagerTriggerActionVisibleModalFooterActions = $columnManagerTriggerAction->getVisibleModalFooterActions();
                $columnManagerTriggerActionModalFooterActionsAlignment = $columnManagerTriggerAction->getModalFooterActionsAlignment();
                $columnManagerTriggerActionModalHeading = $columnManagerTriggerAction->getCustomModalHeading() ?? __('filament-tables::table.column_manager.heading');
                $columnManagerTriggerActionModalIcon = $columnManagerTriggerAction->getModalIcon();
                $columnManagerTriggerActionModalIconColor = $columnManagerTriggerAction->getModalIconColor();
                $columnManagerTriggerActionIsModalSlideOver = $columnManagerTriggerAction->isModalSlideOver();
                $columnManagerTriggerActionModalSlideOverPosition = $columnManagerTriggerAction->getModalSlideOverPosition();
                $columnManagerTriggerActionIsModalFooterSticky = $columnManagerTriggerAction->isModalFooterSticky();
                $columnManagerTriggerActionIsModalHeaderSticky = $columnManagerTriggerAction->isModalHeaderSticky();
                ?>

                <?php $modalArguments = [
                    'alignment' => $columnManagerTriggerActionModalAlignment,
                    'autofocus' => $columnManagerTriggerActionIsModalAutofocused,
                    'clickThrough' => $columnManagerTriggerActionIsModalClickThrough,
                    'closeButton' => $columnManagerTriggerActionHasModalCloseButton,
                    'closeByClickingAway' => $columnManagerTriggerActionIsModalClosedByClickingAway,
                    'closeByEscaping' => $columnManagerTriggerActionIsModalClosedByEscaping,
                    'description' => $columnManagerTriggerActionModalDescription,
                    'eyebrow' => $columnManagerTriggerActionModalEyebrow,
                    'extraModalWindowAttributeBag' => $columnManagerTriggerActionExtraModalWindowAttributeBag,
                    'extraModalOverlayAttributeBag' => $columnManagerTriggerActionExtraModalOverlayAttributeBag,
                    'footerActions' => $columnManagerTriggerActionVisibleModalFooterActions,
                    'footerActionsAlignment' => $columnManagerTriggerActionModalFooterActionsAlignment,
                    'heading' => $columnManagerTriggerActionModalHeading,
                    'icon' => $columnManagerTriggerActionModalIcon,
                    'iconColor' => $columnManagerTriggerActionModalIconColor,
                    'slideOver' => $columnManagerTriggerActionIsModalSlideOver,
                    'slideOverPosition' => $columnManagerTriggerActionModalSlideOverPosition,
                    'stickyFooter' => $columnManagerTriggerActionIsModalFooterSticky,
                    'stickyHeader' => $columnManagerTriggerActionIsModalHeaderSticky,
                    'width' => $columnManagerWidth,
                    'attributes' => new ComponentAttributeBag([
                        'wire:key' => BladeCompiler::sanitizeComponentAttribute($this->getLivewire()->getId() . '.table.column-manager'),
                        'class' => 'fi-ta-col-manager-modal',
                    ]),
                ];
                ob_start(); ?>
                     <?php $modalTrigger = new ComponentSlot(trim(e($columnManagerTriggerAction))); ?>

                    <?= e($columnManagerTriggerAction->getModalContent()); ?>

                    <div
                        x-data="filamentTableColumnManager({
                                    columns: $wire.entangle('tableColumns'),
                                    isLive: <?= e($columnManagerApplyAction->isVisible() ? 'false' : 'true'); ?>,
                                })"
                        x-on:apply-table-column-manager.window="applyTableColumnManager()"
                        x-on:reset-table-column-manager.window="resetDeferredColumns()"
                        class="fi-ta-col-manager"
                    >
                        <?= $this->generateColumnManagerContentHtml(
                            hasReorderableColumns: $this->hasReorderableColumns(),
                            hasToggleableColumns: $this->hasToggleableColumns(),
                            columns: $columnManagerColumns,
                            reorderAnimationDuration: $this->getReorderAnimationDuration(),
                            attributes: new ComponentAttributeBag([]),
                        ); ?>
                    </div>

                    <?= e($columnManagerTriggerAction->getModalContentFooter()); ?>

                 <?= $this->generateModalHtml(...$modalArguments, slot: new ComponentSlot(trim(ob_get_clean())), trigger: $modalTrigger, livewireId: $this->getLivewire()->getId()); ?>
            <?php } else { ?>
                <?php $dropdownArguments = [
                    'menu' => false,
                    'maxHeight' => $columnManagerMaxHeight,
                    'placement' => 'bottom-end',
                    'shift' => true,
                    'flip' => false,
                    'width' => $columnManagerWidth,
                    'attributes' => new ComponentAttributeBag([
                        'wire:key' => BladeCompiler::sanitizeComponentAttribute($this->getLivewire()->getId() . '.table.column-manager'),
                        'class' => 'fi-ta-col-manager-dropdown',
                    ]),
                ];
                ob_start(); ?>
                     <?php $dropdownTrigger = new ComponentSlot(trim(e($columnManagerTriggerAction))); ?>

                    <?= $this->generateColumnManagerHtml(
                        applyAction: $columnManagerApplyAction,
                        hasReorderableColumns: $this->hasReorderableColumns(),
                        hasToggleableColumns: $this->hasToggleableColumns(),
                        columns: $columnManagerColumns,
                        headingTag: $this->getHeadingTag($this->getHeading() ? 1 : 0),
                        reorderAnimationDuration: $this->getReorderAnimationDuration(),
                        resetActionPosition: $this->getColumnManagerResetActionPosition(),
                        attributes: new ComponentAttributeBag([]),
                    ); ?>
                 <?= $this->generateDropdownHtml(...$dropdownArguments, slot: new ComponentSlot(trim(ob_get_clean())), trigger: $dropdownTrigger); ?>
            <?php } ?>
        <?php } ?>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateReorderingIndicatorHtml(): string
    {
        ob_start(); ?>
        <div
            x-cloak
            role="status"
            aria-live="polite"
            <?php $this->setRenderKey('' . e($this->getLivewire()->getId()) . '.table.reorder.indicator'); ?>wire:key="<?= e($this->getLivewire()->getId()); ?>.table.reorder.indicator"
            class="fi-ta-reorder-indicator"
        >
            <?= e(\Filament\Support\generate_loading_indicator_html(new FilamentComponentAttributeBag([
                'wire:loading.delay.' . config('filament.livewire_loading_delay', 'default') => '',
                'wire:target' => 'reorderTable,moveTableRecord',
            ]))); ?>

            <?= e(__('filament-tables::table.reorder_indicator')); ?>

        </div>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateSelectionIndicatorHtml(): string
    {
        $livewire = $this->getLivewire();

        ob_start(); ?>
        <div
            x-cloak
            role="status"
            aria-live="polite"
            aria-atomic="true"
            x-bind:hidden="! getSelectedRecordsCount()"
            x-show="getSelectedRecordsCount()"
            <?php $this->setRenderKey('' . e($this->getLivewire()->getId()) . '.table.selection.indicator'); ?>wire:key="<?= e($this->getLivewire()->getId()); ?>.table.selection.indicator"
            class="fi-ta-selection-indicator"
        >
            <div>
                <?= e(\Filament\Support\generate_loading_indicator_html(new FilamentComponentAttributeBag([
                    'x-show' => 'isLoading',
                ]))); ?>

                <span
                    x-text="getSelectedRecordsCountLabel(<?= Js::from(__('filament-tables::table.selection_indicator.selected_count'))->toHtml() ?>, <?= Js::from(str_replace('_', '-', app()->getLocale()))->toHtml() ?>)"
                ></span>
            </div>

            <?php if (! $this->isSelectionDisabled()) { ?>
                <div>
                    <?= e(FilamentView::renderHook(TablesRenderHook::SELECTION_INDICATOR_ACTIONS_BEFORE, scopes: $livewire::class)); ?>

                    <div class="fi-ta-selection-indicator-actions-ctn">
                        <?= $this->renderMorphMarkerHtml(); ?>
                        <?php if (! $this->selectsGroupsOnly()) { ?>
                            <?php $linkArguments = [
                                'color' => 'primary',
                                'tag' => 'button',
                                'attributes' => new ComponentAttributeBag([
                                    'x-on:click' => 'selectAllRecords',
                                    'x-show' => 'canSelectAllRecords()',
                                    'wire:key' => BladeCompiler::sanitizeComponentAttribute($this->getLivewire()->getId() . 'table.selection.indicator.actions.select-all.' . $this->getRenderAllSelectableRecordsCount() . '.' . $livewire->getTablePage()),
                                ]),
                            ];
                            ob_start(); ?>
                                <?= e(trans_choice('filament-tables::table.selection_indicator.actions.select_all.label', $this->getRenderAllSelectableRecordsCount(), ['count' => Number::format($this->getRenderAllSelectableRecordsCount(), locale: app()->getLocale())])); ?>

                             <?= $this->generateLinkHtml(...$linkArguments, label: new ComponentSlot(trim(ob_get_clean())), shouldCheckForPublishedView: true); ?>
                        <?php } ?>
                        <?= $this->renderMorphMarkerHtml(isEnd: true); ?>

                        <?php $linkArguments = ['color' => 'danger', 'tag' => 'button', 'attributes' => new ComponentAttributeBag(['x-on:click' => 'deselectAllRecords'])];
                ob_start(); ?>
                            <?= e(__('filament-tables::table.selection_indicator.actions.deselect_all.label')); ?>

                         <?= $this->generateLinkHtml(...$linkArguments, label: new ComponentSlot(trim(ob_get_clean())), shouldCheckForPublishedView: true); ?>
                    </div>

                    <?= e(FilamentView::renderHook(TablesRenderHook::SELECTION_INDICATOR_ACTIONS_AFTER, scopes: $livewire::class)); ?>

                </div>
            <?php } ?>
        </div>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateFilterIndicatorsHtml(): string
    {
        $livewire = $this->getLivewire();
        $filterIndicators = $this->getFilterIndicators();

        ob_start(); ?>
        <?= $this->renderMorphMarkerHtml(); ?>
        <?php if ($filterIndicators) { ?>
            <?php if (filled($filterIndicatorsView = FilamentView::renderHook(TablesRenderHook::FILTER_INDICATORS, scopes: $livewire::class, data: ['filterIndicators' => $filterIndicators]))) { ?>
                <?= e($filterIndicatorsView); ?>
            <?php } else { ?>
                <div class="fi-ta-filter-indicators">
                    <div>
                        <span class="fi-ta-filter-indicators-label">
                            <?= e(__('filament-tables::table.filters.indicator')); ?>

                        </span>

                        <div
                            class="fi-ta-filter-indicators-badges-ctn"
                            role="list"
                        >
                            <?php foreach ($this->iterateRenderLoop($filterIndicators) as $indicator) { ?>
                                <?= $this->generateFilterIndicatorHtml($indicator); ?>
                            <?php } ?>
                        </div>
                    </div>

                    <?php if (collect($filterIndicators)->contains(fn (Indicator $indicator): bool => $indicator->isRemovable())) { ?>
                        <?= e($this->getFiltersRemoveAllAction()); ?>
                    <?php } ?>
                </div>
            <?php } ?>
        <?php } ?>
        <?= $this->renderMorphMarkerHtml(isEnd: true); ?>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateFilterIndicatorHtml(Indicator $indicator): string
    {
        $color = $indicator->getColor();
        $slot = new ComponentSlot(trim(e($indicator->getLabel())));
        $deleteButton = null;

        if ($indicator->isRemovable()) {
            $removeLivewireClickHandler = $indicator->getRemoveLivewireClickHandler();
            $deleteButton = new ComponentSlot('', [
                'label' => BladeCompiler::sanitizeComponentAttribute(__('filament-tables::table.filters.actions.remove.label')),
                'wire:click' => BladeCompiler::sanitizeComponentAttribute($removeLivewireClickHandler),
                'wire:target' => 'removeTableFilter',
            ]);
        }

        return $this->generateBadgeHtml(
            label: $slot,
            attributes: new ComponentAttributeBag(['role' => 'listitem']),
            color: $color,
            deleteButton: $deleteButton,
            size: Size::Medium,
            shouldCheckForPublishedView: true,
        );
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateContentHtml(): string
    {
        $records = $this->isLoaded() ? $this->getRecords() : null;
        $content = $this->getContent();

        ob_start(); ?>
        <?= $this->renderMorphMarkerHtml(); ?>            <?php if (((! $content) && (! $this->hasColumnsLayout())) || ($records === null) || count($records)) { ?>
            <div
                <?php if ((! $this->isReordering()) && ($pollingInterval = $this->getPollingInterval())) { ?>
                    wire:poll.<?= e($pollingInterval); ?>

                <?php } ?>
                wire:loading.attr="aria-busy"
                <?php if ($this->hasLoadingSkeleton()) { ?>
                    wire:loading.class.delay.<?= e(config('filament.livewire_loading_delay', 'default')); ?>="fi-ta-content-loading"
                <?php } ?>
                wire:target="<?= e(implode(',', Table::LOADING_TARGETS)); ?>"
                class="fi-ta-content-ctn fi-fixed-positioning-context"
            >
                <?= $this->generateResultCountHtml(); ?>

                <?php if (($content || $this->hasColumnsLayout()) && ($records !== null) && count($records)) { ?>
                    <?= $this->renderMorphMarkerHtml(); ?>
                    <?= $this->generateContentHeaderHtml(); ?>
                    <?= $this->renderMorphMarkerHtml(isEnd: true); ?>

                    <?php if ($content) { ?>
                        <?= $this->generateCustomContentHtml($content, ['records' => $records]); ?>

                    <?php } else { ?>
                        <?= $this->generateGridRecordsHtml(); ?>

                    <?php } ?>

                    <?= $this->renderMorphMarkerHtml(); ?>
                    <?php if (($content || $this->hasColumnsLayout()) && ($contentFooter = $this->getContentFooter())) { ?>
                        <?= $this->generateCustomContentHtml($contentFooter, [
                            'columns' => $this->getVisibleColumns(),
                            'records' => $records,
                        ]); ?>

                    <?php } ?>
                    <?= $this->renderMorphMarkerHtml(isEnd: true); ?>

                    <?php if ((! $this->isReordering()) && ($this->hasPageSummary() || $this->hasAllTableSummary()) && $this->hasRenderSummary()) { ?>
                        <table
                            class="<?= Arr::toCssClasses([
                                'fi-ta-table',
                                'fi-ta-table-stacked-on-mobile' => $this->isStackedOnMobile(),
                            ]); ?>"
                        >
                            <tbody>
                                <?= $this->generateSummaryHtml(
                                    columns: $this->getVisibleColumns(),
                                    pluralModelLabel: $this->getPluralModelLabel(),
                                    records: $records,
                                    allTableSummary: $this->hasAllTableSummary(),
                                    extraHeadingColumn: true,
                                    pageSummary: $this->hasPageSummary(),
                                    placeholderColumns: false,
                                    attributes: new ComponentAttributeBag([]),
                                ); ?>
                            </tbody>
                        </table>
                    <?php } ?>
                <?php } elseif ((! ($content || $this->hasColumnsLayout())) && ($records !== null)) { ?>
                    <?php
                        $sortableColumns = $this->isStackedOnMobile() ? array_filter(
                            $this->getVisibleColumns(),
                            fn (Column $column): bool => $column->isSortable(),
                        ) : [];
                    ?>

                    <?= $this->generateRecordsTableHtml($sortableColumns); ?>

                <?php } elseif ($records === null) { ?>
                    <div
                        role="status"
                        aria-busy="true"
                        aria-live="polite"
                        class="fi-ta-table-loading-ctn"
                    >
                        <?= e(\Filament\Support\generate_loading_indicator_html(size: IconSize::TwoExtraLarge)); ?>

                        <span class="fi-sr-only">
                            <?= e(__('filament-tables::table.loading')); ?>

                        </span>
                    </div>
                <?php } ?>
            </div>
        <?php } ?><?= $this->renderMorphMarkerHtml(isEnd: true); ?>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateResultCountHtml(): string
    {
        $records = $this->isLoaded() ? $this->getRecords() : null;

        ob_start(); ?>
        <?php if ($records !== null) { ?>
            <?php
                // The total across all pages, not the current page's count — and since the total is
                // stable across pages, the live region only announces when the result set really
                // changes, not on every pagination click. Non-length-aware paginators fall back to
                // the page count.
                $resultCount = ($records instanceof LengthAwarePaginator)
                    ? $records->total()
                    : count($records);
            ?>

            <div
                role="status"
                aria-live="polite"
                aria-atomic="true"
                class="fi-sr-only"
            >
                <?= e(trans_choice('filament-tables::table.result_count', $resultCount, ['count' => $resultCount])); ?>

            </div>

            <div
                wire:loading.delay.<?= e(config('filament.livewire_loading_delay', 'default')); ?>.block
                wire:target="<?= e(implode(',', Table::LOADING_TARGETS)); ?>"
                role="status"
                aria-live="polite"
                data-testid="table-loading-state"
                class="fi-sr-only"
            >
                <?= e(__('filament-tables::table.loading')); ?>

            </div>
        <?php } ?>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     *
     * @param  array<string, mixed>  $data
     */
    protected function generateCustomContentHtml(View | Htmlable $content, array $data): string
    {
        $content = $content->with($data);

        return $content instanceof Htmlable ? $content->toHtml() : $content->render();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateContentHeaderHtml(): string
    {
        ob_start(); ?>
        <?php if (! $this->isReordering()) { ?>
            <?php
                $sortableColumns = array_filter(
                    $this->getVisibleColumns(),
                    fn (Column $column): bool => $column->isSortable(),
                );
            ?>

            <?php if (($this->isRenderSelectionEnabled() && ($this->getMaxSelectableRecords() !== 1) && (! $this->selectsGroupsOnly())) || count($sortableColumns)) { ?>
                <div class="fi-ta-content-header">
                    <?= $this->renderMorphMarkerHtml(); ?>
                    <?php if ($this->isRenderSelectionEnabled() && ($this->getMaxSelectableRecords() !== 1) && (! $this->selectsGroupsOnly())) { ?>
                        <?= $this->generatePageCheckboxHtml(); ?>

                    <?php } ?>
                    <?= $this->renderMorphMarkerHtml(isEnd: true); ?>

                    <?php if (count($sortableColumns)) { ?>
                        <div
                            x-data="filamentTableSort"
                            class="fi-ta-sorting-settings"
                        >
                            <label>
                                <?php $inputWrapperArguments = ['prefix' => __('filament-tables::table.sorting.fields.column.label'), 'attributes' => new ComponentAttributeBag([])];
                        ob_start(); ?>
                                    <?php $inputSelectArguments = ['attributes' => new ComponentAttributeBag(['x-model' => 'column'])];
                        ob_start(); ?>
                                        <option value="">
                                            <?= e($this->getDefaultSortOptionLabel()); ?>

                                        </option>

                                        <?php foreach ($this->iterateRenderLoop($sortableColumns) as $column) {  ?>
                                            <option
                                                value="<?= e($column->getName()); ?>"
                                            >
                                                <?= e($column->getLabel()); ?>

                                            </option>
                                        <?php }  ?>
                                     <?= $this->generateInputSelectHtml(...$inputSelectArguments, slot: new ComponentSlot(trim(ob_get_clean()))); ?>
                                 <?= $this->generateInputWrapperHtml(...$inputWrapperArguments, slot: new ComponentSlot(trim(ob_get_clean()))); ?>
                            </label>

                            <label x-cloak x-show="column">
                                <span class="fi-sr-only">
                                    <?= e(__('filament-tables::table.sorting.fields.direction.label')); ?>

                                </span>

                                <?php $inputWrapperArguments = ['attributes' => new ComponentAttributeBag([])];
                        ob_start(); ?>
                                    <?php $inputSelectArguments = ['attributes' => new ComponentAttributeBag(['x-model' => 'direction'])];
                        ob_start(); ?>
                                        <option value="asc">
                                            <?= e(__('filament-tables::table.sorting.fields.direction.options.asc')); ?>

                                        </option>

                                        <option value="desc">
                                            <?= e(__('filament-tables::table.sorting.fields.direction.options.desc')); ?>

                                        </option>
                                     <?= $this->generateInputSelectHtml(...$inputSelectArguments, slot: new ComponentSlot(trim(ob_get_clean()))); ?>
                                 <?= $this->generateInputWrapperHtml(...$inputWrapperArguments, slot: new ComponentSlot(trim(ob_get_clean()))); ?>
                            </label>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        <?php } ?>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generatePageCheckboxHtml(bool $isStacked = false): string
    {
        ob_start(); ?>
        <input
            aria-label="<?= e(__('filament-tables::table.fields.bulk_select_page.label')); ?>"
            type="checkbox"
            <?php if ($this->isSelectionDisabled()) { ?>
                disabled
            <?php } elseif ($this->getMaxSelectableRecords()) { ?>
                x-bind:disabled="shouldDisableSelectRecordsOnPageCheckbox()"
            <?php } ?>
            x-bind:checked="syncSelectRecordsOnPageCheckbox($el)"
            x-on:click="toggleSelectRecordsOnPage"

            <?php $this->setRenderKey('' . e($this->getLivewire()->getId()) . '.table.bulk-select-page.checkbox.' . ($isStacked ? 'stacked.' : '') . e(Str::random()) . ''); ?>wire:key="<?= e($this->getLivewire()->getId()); ?>.table.bulk-select-page.checkbox.<?= $isStacked ? 'stacked.' : ''; ?><?= e(Str::random()); ?>"
            wire:loading.attr="disabled"
            wire:target="<?= e(implode(',', Table::LOADING_TARGETS)); ?>"
            class="fi-ta-page-checkbox fi-checkbox-input"
        />
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateGridRecordsHtml(): string
    {
        $livewire = $this->getLivewire();
        $viewFactory = app('view');
        $records = $this->getRecords();

        ob_start(); ?>
        <div
            <?php if ($this->isReorderable()) { ?>
                x-ref="tableReorderRecords"
                x-on:start.stop="startTableReorder($event)"
                x-on:end.stop="reorderTableRecords($event)"
                x-sortable
                data-sortable-animation-duration="<?= e($this->getReorderAnimationDuration()); ?>"
            <?php } ?>
            aria-label="<?= e($this->getPluralModelLabel()); ?>"
            role="list"
            <?php if ($this->hasLoadingSkeleton()) { ?>
                wire:loading.attr.delay.<?= e(config('filament.livewire_loading_delay', 'default')); ?>="inert"
                wire:target="<?= e(implode(',', Table::LOADING_TARGETS)); ?>"
            <?php } ?>
            <?= e((new FilamentComponentAttributeBag)
                ->when($contentGrid = $this->getContentGrid(), fn (ComponentAttributeBag $attributes) => $attributes->grid($contentGrid))
                ->class([
                    'fi-ta-content',
                    'fi-ta-content-grid' => $contentGrid,
                    'fi-ta-content-grouped' => $livewire->getTableGrouping(),
                ])); ?>

        >
            <?php
                $previousRecord = null;
        $previousRecordGroupKey = null;
        $previousRecordGroupTitle = null;
        ?>

            <?php foreach ($this->iterateRenderLoop($records) as $record) {
                $loop = $viewFactory->getLastLoop(); ?>
                <?php
                        $recordKey = $this->getRecordKey($record);
                $recordUrl = $this->getRecordUrl($record);
                $recordAction = $recordUrl ? null : $this->getRecordAction($record);
                $openRecordUrlInNewTab = $this->shouldOpenRecordUrlInNewTab($record);
                $recordGroupKey = $this->getGrouping()?->getStringKey($record);
                $recordGroupTitle = $this->getGrouping()?->getTitle($record, $recordGroupKey);
                $isRecordGroupCollapsible = $this->getGrouping()?->isCollapsible();
                $recordIsSelectable = $this->isRenderSelectionEnabled() && $this->isRecordSelectable($record);

                $this->getCollapsibleColumnsLayout()?->record($record)->recordKey($recordKey);
                $hasCollapsibleColumnsLayout = (bool) $this->getCollapsibleColumnsLayout()?->isVisible();

                $recordActions = $this->getRenderRecordActions($record, $recordKey);
                ?>

                <?php if ((string) $recordGroupTitle !== (string) $previousRecordGroupTitle) { ?>
                    <?php if ((! $this->isReordering()) && filled($previousRecordGroupTitle) && $this->hasRenderSummary()) { ?>
                        <div
                            <?php $groupSummaryKey = $livewire->getId() . '.table.groups.' . md5(json_encode([$this->getGrouping()->getId(), $previousRecordGroupKey])) . '.summary'; ?>
                            <?php $this->setRenderKey($groupSummaryKey); ?>
                            wire:key="<?= e($groupSummaryKey); ?>"
                            role="listitem"
                            class="fi-ta-group-summary"
                        >
                            <table
                                class="<?= Arr::toCssClasses([
                                    'fi-ta-table',
                                    'fi-ta-table-stacked-on-mobile' => $this->isStackedOnMobile(),
                                    'fi-ta-table-reordering' => $this->isReordering(),
                                ]); ?>"
                            >
                                <tbody>
                                    <?php
                                        $groupScopedAllTableSummaryQuery = $this->getGrouping()->scopeQuery($livewire->getAllTableSummaryQuery(), $previousRecord);
                        ?>

                                    <?= $this->generateSummaryRowHtml(
                                        columns: $this->getVisibleColumns(),
                                        heading: __('filament-tables::table.summary.subheadings.group', ['group' => $previousRecordGroupTitle, 'label' => $this->getPluralModelLabel()]),
                                        query: $groupScopedAllTableSummaryQuery,
                                        selectedState: $this->getRenderGroupedSummarySelectedState()[$previousRecordGroupKey] ?? [],
                                        extraHeadingColumn: true,
                                        placeholderColumns: false,
                                        attributes: new ComponentAttributeBag([]),
                                    ); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>

        <?= $this->generateGridGroupHeaderHtml($record, $recordGroupTitle, $isRecordGroupCollapsible, $recordGroupKey); ?>

                <?php } ?>

                <?= $this->generateGridRecordHtml(
                    $hasCollapsibleColumnsLayout,
                    $recordKey,
                    $recordUrl,
                    $recordAction,
                    $recordIsSelectable,
                    $record,
                    $recordGroupTitle,
                    $recordGroupKey,
                    $openRecordUrlInNewTab,
                    $recordActions,
                    $loop,
                ); ?>

                <?php
                    $previousRecordGroupKey = $recordGroupKey;
                $previousRecordGroupTitle = $recordGroupTitle;
                $previousRecord = $record;
                ?>
            <?php } $loop = $viewFactory->getLastLoop(); ?>

            <?php if ((! $this->isReordering()) && filled($previousRecordGroupTitle) && $this->hasRenderSummary() && $livewire->shouldRenderTrailingGroupedTableSummary($previousRecord)) { ?>
                <div
                    <?php $groupSummaryKey = $livewire->getId() . '.table.groups.' . md5(json_encode([$this->getGrouping()->getId(), $previousRecordGroupKey])) . '.summary'; ?>
                    <?php $this->setRenderKey($groupSummaryKey); ?>
                    wire:key="<?= e($groupSummaryKey); ?>"
                    role="listitem"
                    class="fi-ta-group-summary"
                >
                    <table
                        class="<?= Arr::toCssClasses([
                            'fi-ta-table',
                            'fi-ta-table-stacked-on-mobile' => $this->isStackedOnMobile(),
                        ]); ?>"
                    >
                        <tbody>
                            <?php
                                $groupScopedAllTableSummaryQuery = $this->getGrouping()->scopeQuery($livewire->getAllTableSummaryQuery(), $previousRecord);
                ?>

                            <?= $this->generateSummaryRowHtml(
                                columns: $this->getVisibleColumns(),
                                heading: __('filament-tables::table.summary.subheadings.group', ['group' => $previousRecordGroupTitle, 'label' => $this->getPluralModelLabel()]),
                                query: $groupScopedAllTableSummaryQuery,
                                selectedState: $this->getRenderGroupedSummarySelectedState()[$previousRecordGroupKey] ?? [],
                                extraHeadingColumn: true,
                                placeholderColumns: false,
                                attributes: new ComponentAttributeBag([]),
                            ); ?>
                        </tbody>
                    </table>
                </div>
            <?php } ?>
        </div>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     *
     * @param  Model | array<string, mixed>  $record
     */
    protected function generateGridGroupHeaderHtml(Model | array $record, string | Htmlable | null $recordGroupTitle, bool $isRecordGroupCollapsible, ?string $recordGroupKey): string
    {
        $livewire = $this->getLivewire();

        ob_start(); ?>
        <?php
            $recordGroupLabel = $this->getGrouping()->isTitlePrefixedWithLabel() ? $this->getGrouping()->getLabel() : null;
        $recordGroupDescription = $this->getGrouping()->getDescription($record, $recordGroupTitle);
        $hasRecordGroupCheckbox = $this->isRenderSelectionEnabled() && ($this->getMaxSelectableRecords() !== 1);
        $hasRecordGroupHeader = filled($recordGroupTitle)
            || filled($recordGroupLabel)
            || filled($recordGroupDescription)
            || $isRecordGroupCollapsible
            || $hasRecordGroupCheckbox;
        ?>

        <div
            <?php $groupHeaderKey = $livewire->getId() . '.table.groups.' . md5(json_encode([$this->getGrouping()->getId(), $recordGroupKey])) . '.header'; ?>
            <?php $this->setRenderKey($groupHeaderKey); ?>
            wire:key="<?= e($groupHeaderKey); ?>"
            role="listitem"
            <?php if (! $hasRecordGroupHeader) { ?>
                aria-hidden="true"
            <?php } ?>
            <?php if ($isRecordGroupCollapsible) { ?>
                x-on:click="(! $event.target.closest('.fi-ta-group-checkbox')) && toggleCollapseGroup(<?= Js::from($recordGroupTitle)->toHtml() ?>)"
                <?php if (! $this->hasRenderSummary()) { ?>
                    x-bind:class="{ 'fi-collapsed': isGroupCollapsed(<?= Js::from($recordGroupTitle)->toHtml() ?>) }"
                <?php } ?>
            <?php } ?>
            class="<?= Arr::toCssClasses([
                'fi-ta-group-header',
                'fi-collapsible' => $isRecordGroupCollapsible,
            ]); ?>"
        >
            <?= $this->renderMorphMarkerHtml(); ?>
            <?php if ($hasRecordGroupCheckbox) { ?>
                <?= $this->generateGroupCheckboxHtml($recordGroupTitle, $recordGroupKey); ?>
            <?php } ?>
            <?= $this->renderMorphMarkerHtml(isEnd: true); ?>

            <div>
                <<?= e($this->getHeadingTag($this->getHeading() ? 1 : 0)); ?>

                    class="fi-ta-group-heading"
                >
                    <?php if (filled($recordGroupLabel)) { ?>
                            <?= e($recordGroupLabel); ?>:
                    <?php } ?>

                    <?= e($recordGroupTitle); ?>

                </<?= e($this->getHeadingTag($this->getHeading() ? 1 : 0)); ?>>

                <?php if (filled($recordGroupDescription)) { ?>
                    <p
                        class="fi-ta-group-description"
                    >
                        <?= e($recordGroupDescription); ?>

                    </p>
                <?php } ?>
            </div>

            <?php if ($isRecordGroupCollapsible) { ?>
                <button
                    aria-label="<?= e(filled($recordGroupLabel) ? ($recordGroupLabel . ': ' . $recordGroupTitle) : $recordGroupTitle); ?>"
                    x-bind:aria-expanded="! isGroupCollapsed(<?= Js::from($recordGroupTitle)->toHtml() ?>)"
                    type="button"
                    class="fi-icon-btn fi-size-sm"
                >
                    <?= e(\Filament\Support\generate_icon_html(Heroicon::ChevronUp, alias: TablesIconAlias::GROUPING_COLLAPSE_BUTTON, size: IconSize::Small)); ?>

                </button>
            <?php } ?>
        </div>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     *
     * @param  Model | array<string, mixed>  $record
     * @param  array<Action | ActionGroup>  $recordActions
     */
    protected function generateGridRecordHtml(
        bool $hasCollapsibleColumnsLayout,
        string $recordKey,
        ?string $recordUrl,
        ?string $recordAction,
        bool $recordIsSelectable,
        Model | array $record,
        string | Htmlable | null $recordGroupTitle,
        ?string $recordGroupKey,
        bool $openRecordUrlInNewTab,
        array $recordActions,
        \stdClass $loop,
    ): string {
        $viewFactory = app('view');

        ob_start(); ?>
        <div
            <?php if ($hasCollapsibleColumnsLayout) { ?>
                x-data="{ isCollapsed: <?= Js::from($this->getCollapsibleColumnsLayout()->isCollapsed())->toHtml() ?> }"
                x-init="$dispatch('collapsible-table-row-initialized')"
                x-on:collapse-all-table-rows.window="isCollapsed = true"
                x-on:expand-all-table-rows.window="isCollapsed = false"
            <?php } ?>
            role="listitem"
            <?php $this->setRenderKey('' . e($this->getLivewire()->getId()) . '.table.records.' . e($recordKey) . ''); ?>wire:key="<?= e($this->getLivewire()->getId()); ?>.table.records.<?= e($recordKey); ?>"
            <?php if ($this->isReordering()) { ?>
                x-sortable-item="<?= e($recordKey); ?>"
                x-sortable-handle
            <?php } ?>
            class="<?= Arr::toCssClasses([
                'fi-ta-record',
                'fi-clickable' => $recordUrl || $recordAction,
                'fi-ta-record-with-content-prefix' => $this->isReordering() || $recordIsSelectable,
                'fi-ta-record-with-content-suffix' => $hasCollapsibleColumnsLayout && (! $this->isReordering()),
                ...$this->getRecordClasses($record),
            ]); ?>"
            x-bind:class="{
                <?= e($hasCollapsibleColumnsLayout ? '\'fi-ta-record-collapsed\': isCollapsed,' : ''); ?>

                <?= e($this->getGrouping()?->isCollapsible() ? '\'fi-collapsed\': isGroupCollapsed(' . Js::from($recordGroupTitle) . '),' : ''); ?>

                'fi-selected': <?= Js::from($recordIsSelectable)->toHtml() ?> && isRecordSelected(<?= Js::from($recordKey)->toHtml() ?>),
            }"
        >
            <?php
                $hasItemBeforeRecordContent = $this->isReordering() || $recordIsSelectable;
        $hasItemAfterRecordContent = $hasCollapsibleColumnsLayout && (! $this->isReordering());
        ?>

        <?= $this->renderMorphMarkerHtml(); ?>                                        <?php if ($this->isReordering()) { ?>
                <?= $this->generateRecordReorderControlsHtml($record, $recordKey, $loop); ?>

            <?php } elseif ($recordIsSelectable) { ?>
                <?= $this->generateRecordCheckboxHtml($recordKey, $recordGroupKey); ?>

            <?php } ?><?= $this->renderMorphMarkerHtml(isEnd: true); ?>

            <div class="fi-ta-record-content-ctn">
                <div>
                    <?php if ($recordUrl) { ?>
                        <a
                            <?= e(\Filament\Support\generate_href_html($recordUrl, $openRecordUrlInNewTab, hasNestedClickEventHandler: true)); ?>

                            <?= e($this->getExtraRecordLinkAttributeBag($record)->class(['fi-ta-record-content'])); ?>

                        >
                            <?php foreach ($this->iterateRenderLoop($this->getColumnsLayout()) as $columnsLayoutComponent) {
                                $loop = $viewFactory->getLastLoop(); ?>
                                <?= e($columnsLayoutComponent
                                    ->record($record)
                                    ->recordKey($recordKey)
                                    ->rowLoop($loop)
                                    ->renderInLayout()); ?>

                            <?php } $loop = $viewFactory->getLastLoop(); ?>
                        </a>
                    <?php } elseif ($recordAction) { ?>
                        <?php
                                $recordWireClickAction = $this->getRecordAction($record)
                                    ? "mountAction('{$recordAction}', {}, " . Js::from(['table' => true, 'recordKey' => $recordKey]) . ')'
                                    : $recordWireClickAction = "{$recordAction}(" . Js::from($recordKey) . ')';
                        ?>

                        <button
                            type="button"
                            wire:click="<?= e($recordWireClickAction); ?>"
                            wire:loading.attr="disabled"
                            wire:target="<?= e($recordWireClickAction); ?>"
                            class="fi-ta-record-content"
                        >
                            <?php foreach ($this->iterateRenderLoop($this->getColumnsLayout()) as $columnsLayoutComponent) {
                                $loop = $viewFactory->getLastLoop(); ?>
                                <?= e($columnsLayoutComponent
                                    ->record($record)
                                    ->recordKey($recordKey)
                                    ->rowLoop($loop)
                                    ->renderInLayout()); ?>

                            <?php } $loop = $viewFactory->getLastLoop(); ?>
                        </button>
                    <?php } else { ?>
                        <div
                            class="fi-ta-record-content"
                        >
                            <?php foreach ($this->iterateRenderLoop($this->getColumnsLayout()) as $columnsLayoutComponent) {
                                $loop = $viewFactory->getLastLoop(); ?>
                                <?= e($columnsLayoutComponent
                                    ->record($record)
                                    ->recordKey($recordKey)
                                    ->rowLoop($loop)
                                    ->renderInLayout()); ?>

                            <?php } $loop = $viewFactory->getLastLoop(); ?>
                        </div>
                    <?php } ?>

                    <?php if ($hasCollapsibleColumnsLayout && (! $this->isReordering())) { ?>
                        <div
                            x-collapse
                            x-show="! isCollapsed"
                            class="fi-ta-record-content fi-collapsible"
                        >
                            <?= e($this->getCollapsibleColumnsLayout()); ?>

                        </div>
                    <?php } ?>
                </div>

                <?php if ($recordActions && (! $this->isReordering())) { ?>
                    <div
                        class="<?= Arr::toCssClasses([
                            'fi-ta-actions fi-wrapped sm:fi-not-wrapped',
                            match ($this->getRenderRecordActionsAlignment() ?? Alignment::Start) {
                                Alignment::Start => 'fi-align-start',
                                Alignment::Center => 'fi-align-center',
                                Alignment::End => 'fi-align-end',
                                default => throw new UnhandledMatchError,
                            } => (bool) ($contentGrid = $this->getContentGrid()),
                            'fi-align-start md:fi-align-end' => ! $contentGrid,
                            'fi-ta-actions-before-columns-position' => $this->getRecordActionsPosition() === RecordActionsPosition::BeforeColumns,
                        ]); ?>"
                    >
                        <?php foreach ($this->iterateRenderLoop($recordActions) as $action) {
                            $loop = $viewFactory->getLastLoop(); ?>
                            <?= e($action); ?>

                        <?php } $loop = $viewFactory->getLastLoop(); ?>
                    </div>
                <?php } ?>
            </div>

            <?php if ($hasCollapsibleColumnsLayout && (! $this->isReordering())) { ?>
                <button
                    aria-label="<?= e(__('filament-tables::table.actions.toggle_record_content.label', ['key' => $recordKey])); ?>"
                    x-bind:aria-expanded="! isCollapsed"
                    type="button"
                    x-on:click="isCollapsed = ! isCollapsed"
                    class="fi-ta-record-collapse-btn fi-icon-btn"
                >
                    <?= e(\Filament\Support\generate_icon_html(Heroicon::ChevronDown, alias: TablesIconAlias::COLUMNS_COLLAPSE_BUTTON)); ?>

                </button>
            <?php } ?>
        </div>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     *
     * @param  array<string, Column>  $sortableColumns
     */
    protected function generateRecordsTableHtml(array $sortableColumns): string
    {
        $records = $this->isLoaded() ? $this->getRecords() : null;

        ob_start(); ?>
        <table
            aria-label="<?= e(filled($tableAccessibleLabel = trim(strip_tags((string) $this->getHeading()))) ? $tableAccessibleLabel : $this->getPluralModelLabel()); ?>"
            class="<?= Arr::toCssClasses([
                'fi-ta-table',
                'fi-ta-table-stacked-on-mobile' => $this->isStackedOnMobile(),
            ]); ?>"
        >
            <?= $this->generateTableHeaderHtml($sortableColumns); ?>

        <?= $this->generateColumnSearchesHtml(); ?>

            <?= $this->generateRecordsHtml(); ?>

            <?php if (($records !== null) && count($records) && ($contentFooter = $this->getContentFooter())) { ?>
                <tfoot>
                    <tr>
                        <?= $this->generateCustomContentHtml($contentFooter, [
                            'columns' => $this->getVisibleColumns(),
                            'records' => $records,
                        ]); ?>

                    </tr>
                </tfoot>
            <?php } ?>
        </table>
        <?php return ob_get_clean();
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

                    <?= e(\Filament\Support\generate_icon_html(($isColumnActivelySorted && $sortDirection === 'asc') ? Heroicon::ChevronUp : Heroicon::ChevronDown, alias: match (true) {
                        $isColumnActivelySorted && ($sortDirection === 'asc') => TablesIconAlias::HEADER_CELL_SORT_ASC_BUTTON,
                        $isColumnActivelySorted && ($sortDirection === 'desc') => TablesIconAlias::HEADER_CELL_SORT_DESC_BUTTON,
                        default => TablesIconAlias::HEADER_CELL_SORT_BUTTON,
                    }, attributes: (new FilamentComponentAttributeBag([
                        'wire:loading.remove.delay.' . config('filament.livewire_loading_delay', 'default') => true,
                        'wire:target' => "sortTable('{$columnName}')",
                    ])))); ?>

                    <?= e(\Filament\Support\generate_loading_indicator_html(new FilamentComponentAttributeBag([
                        'wire:loading.delay.' . config('filament.livewire_loading_delay', 'default') => '',
                        'wire:target' => "sortTable('{$columnName}')",
                    ]))); ?>

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
     */
    protected function generateColumnSearchesHtml(): string
    {
        $records = $this->getRecords();

        ob_start(); ?>
        <?= $this->renderMorphMarkerHtml(); ?>                            <?php if ($this->isSearchableByColumn()) { ?>
            <tbody>
                <?php
                    $breakpointOrder = [
                        'base' => 0,
                        'sm' => 1,
                        'md' => 2,
                        'lg' => 3,
                        'xl' => 4,
                        '2xl' => 5,
                    ];
            $responsiveBreakpointOrder = array_diff_key($breakpointOrder, ['base' => true]);
            $individualSearchHiddenAt = [];

            $individualSearchColumnStates = [];

            foreach ($this->getVisibleColumns() as $columnKey => $column) {
                $individualSearchColumnStates[$columnKey] = [
                    'hiddenFrom' => $column->getHiddenFrom(),
                    'isIndividuallySearchable' => $column->isIndividuallySearchable(),
                    'visibleFrom' => $column->getVisibleFrom(),
                ];
            }

            foreach ($breakpointOrder as $breakpoint => $breakpointIndex) {
                foreach ($individualSearchColumnStates as $columnState) {
                    if (! $columnState['isIndividuallySearchable']) {
                        continue;
                    }

                    $visibleFromIndex = filled($columnState['visibleFrom']) ? ($responsiveBreakpointOrder[$columnState['visibleFrom']] ?? null) : null;

                    if ($visibleFromIndex !== null) {
                        if ($breakpointIndex >= $visibleFromIndex) {
                            continue 2;
                        }

                        continue;
                    }

                    $hiddenFromIndex = filled($columnState['hiddenFrom']) ? ($responsiveBreakpointOrder[$columnState['hiddenFrom']] ?? null) : null;

                    if (($hiddenFromIndex === null) || ($breakpointIndex < $hiddenFromIndex)) {
                        continue 2;
                    }
                }

                $individualSearchHiddenAt[] = $breakpoint;
            }
            ?>

                <tr
                    <?php if (filled($individualSearchHiddenAt)) { ?> data-search-hidden-at="<?= e(implode(' ', $individualSearchHiddenAt)); ?>" <?php } ?>
                    class="fi-ta-row fi-ta-row-not-reorderable fi-ta-individual-search-row"
                >
                    <?= $this->renderMorphMarkerHtml(); ?>
                    <?php if (count($records)) { ?>
                        <?php if ($this->isReordering()) { ?>
                            <td></td>
                        <?php } else { ?>
                            <?php if ($this->hasRenderRecordActions() && in_array($this->getRecordActionsPosition(), [RecordActionsPosition::BeforeCells, RecordActionsPosition::BeforeColumns])) { ?>
                                <td></td>
                            <?php } ?>

                            <?php if ($this->isRenderSelectionEnabled() && $this->getRecordCheckboxPosition() === RecordCheckboxPosition::BeforeCells) { ?>
                                <td></td>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                    <?= $this->renderMorphMarkerHtml(isEnd: true); ?>

                    <?php foreach ($this->iterateRenderLoop($this->getVisibleColumns()) as $columnKey => $column) {  ?>
                        <?php
                        $columnName = $column->getName();
                        $columnState = $individualSearchColumnStates[$columnKey];
                        $isIndividuallySearchable = $columnState['isIndividuallySearchable'];
                        $columnHiddenFrom = $columnState['hiddenFrom'];
                        $columnVisibleFrom = $columnState['visibleFrom'];
                        ?>

                        <td
                            class="<?= Arr::toCssClasses([
                                'fi-ta-cell',
                                'fi-ta-individual-search-cell' => $isIndividuallySearchable,
                                'fi-ta-individual-search-cell-' . str($columnName)->camel()->kebab() => $isIndividuallySearchable,
                                filled($columnHiddenFrom) ? "{$columnHiddenFrom}:fi-hidden" : '',
                                filled($columnVisibleFrom) ? "{$columnVisibleFrom}:fi-visible" : '',
                            ]); ?>"
                        >
                            <?php if ($isIndividuallySearchable) { ?>
                                <?= $this->generateSearchFieldHtml(
                                    debounce: $this->getSearchDebounce(),
                                    label: $column->getLabel(),
                                    labelHidden: ! $this->isStackedOnMobile(),
                                    onBlur: $this->isSearchOnBlur(),
                                    wireModel: 'tableColumnSearches.' . $columnName,
                                    attributes: new ComponentAttributeBag([]),
                                ); ?>
                            <?php } ?>
                        </td>
                    <?php }  ?>

                    <?php if ((! $this->isReordering()) && count($records)) { ?>
                        <?php if ($this->hasRenderRecordActions() && in_array($this->getRecordActionsPosition(), [RecordActionsPosition::AfterColumns, RecordActionsPosition::AfterCells])) { ?>
                            <td></td>
                        <?php } ?>

                        <?php if ($this->isRenderSelectionEnabled() && $this->getRecordCheckboxPosition() === RecordCheckboxPosition::AfterCells) { ?>
                            <td></td>
                        <?php } ?>
                    <?php } ?>
                </tr>
            </tbody>
        <?php } ?><?= $this->renderMorphMarkerHtml(isEnd: true); ?>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateRecordsHtml(): string
    {
        $livewire = $this->getLivewire();
        $viewFactory = app('view');
        $records = $this->getRecords();

        ob_start(); ?>
        <?php if (count($records)) { ?>
            <tbody
                class="fi-ta-records"
                <?php if ($this->isReorderable()) { ?>
                    x-ref="tableReorderRecords"
                    x-on:start.stop="startTableReorder($event)"
                    x-on:end.stop="reorderTableRecords($event)"
                    x-sortable
                    data-sortable-animation-duration="<?= e($this->getReorderAnimationDuration()); ?>"
                <?php } ?>
                <?php if ($this->hasLoadingSkeleton()) { ?>
                    wire:loading.attr.delay.<?= e(config('filament.livewire_loading_delay', 'default')); ?>="inert"
                    wire:target="<?= e(implode(',', Table::LOADING_TARGETS)); ?>"
                <?php } ?>
            >
                <?php
                    $isRecordRowStriped = false;
            $previousRecord = null;
            $previousRecordGroupKey = null;
            $previousRecordGroupTitle = null;
            ?>

                <?= $this->renderMorphMarkerHtml(); ?>
                <?php foreach ($this->iterateRenderLoop($records) as $record) {
                    $loop = $viewFactory->getLastLoop(); ?>
                    <?php
                            $recordKey = $this->getRecordKey($record);
                    $recordUrl = $this->getRecordUrl($record);
                    $recordAction = $recordUrl ? null : $this->getRecordAction($record);
                    $openRecordUrlInNewTab = $this->shouldOpenRecordUrlInNewTab($record);
                    $recordGroupKey = $this->getGrouping()?->getStringKey($record);
                    $recordGroupTitle = $this->getGrouping()?->getTitle($record, $recordGroupKey);
                    $recordIsSelectable = $this->isRenderSelectionEnabled() && $this->isRecordSelectable($record);

                    $recordActions = $this->getRenderRecordActions($record, $recordKey);
                    ?>

                    <?php if ((string) $recordGroupTitle !== (string) $previousRecordGroupTitle) { ?>
                        <?php if ((! $this->isReordering()) && filled($previousRecordGroupTitle) && $this->hasRenderSummary()) { ?>
                            <?php
                                $groupColumn = $this->getGrouping()->getColumn();
                            $groupScopedAllTableSummaryQuery = $this->getGrouping()->scopeQuery($livewire->getAllTableSummaryQuery(), $previousRecord);
                            $groupSummaryKey = $livewire->getId() . '.table.groups.' . md5(json_encode([$this->getGrouping()->getId(), $previousRecordGroupKey])) . '.summary';
                            $this->setRenderKey($groupSummaryKey);
                            ?>

                            <?= $this->generateSummaryRowHtml(
                                columns: $this->getVisibleColumns(),
                                heading: $this->isRenderGroupsOnly() ? $previousRecordGroupTitle : __('filament-tables::table.summary.subheadings.group', ['group' => $previousRecordGroupTitle, 'label' => $this->getPluralModelLabel()]),
                                query: $groupScopedAllTableSummaryQuery,
                                selectedState: $this->getRenderGroupedSummarySelectedState()[$previousRecordGroupKey] ?? [],
                                actions: $this->hasRenderRecordActions(),
                                actionsPosition: $this->getRecordActionsPosition(),
                                groupColumn: $groupColumn,
                                groupsOnly: $this->isRenderGroupsOnly(),
                                selectionEnabled: $this->isRenderSelectionEnabled(),
                                recordCheckboxPosition: $this->getRecordCheckboxPosition(),
                                attributes: new ComponentAttributeBag(['wire:key' => $groupSummaryKey]),
                            ); ?>
                        <?php } ?>

                        <?= $this->generateTableGroupHeaderHtml($record, $recordGroupTitle, $recordGroupKey); ?>

                        <?php
                            $isRecordRowStriped = false;
                        ?>
                    <?php } ?>

                    <?= $this->generateRecordHtml(
                        $recordKey,
                        $recordGroupTitle,
                        $recordIsSelectable,
                        $recordAction,
                        $recordUrl,
                        $isRecordRowStriped,
                        $record,
                        $recordActions,
                        $recordGroupKey,
                        $openRecordUrlInNewTab,
                        $loop,
                    ); ?>

                    <?php
                        $isRecordRowStriped = ! $isRecordRowStriped;
                    $previousRecord = $record;
                    $previousRecordGroupKey = $recordGroupKey;
                    $previousRecordGroupTitle = $recordGroupTitle;
                    ?>
                <?php } $loop = $viewFactory->getLastLoop(); ?>

                <?php if ((! $this->isReordering()) && filled($previousRecordGroupTitle) && $this->hasRenderSummary() && $livewire->shouldRenderTrailingGroupedTableSummary($previousRecord)) { ?>
                    <?php
                        $groupColumn = $this->getGrouping()->getColumn();
                    $groupScopedAllTableSummaryQuery = $this->getGrouping()->scopeQuery($livewire->getAllTableSummaryQuery(), $previousRecord);
                    $groupSummaryKey = $livewire->getId() . '.table.groups.' . md5(json_encode([$this->getGrouping()->getId(), $previousRecordGroupKey])) . '.summary';
                    $this->setRenderKey($groupSummaryKey);
                    ?>

                    <?= $this->generateSummaryRowHtml(
                        columns: $this->getVisibleColumns(),
                        heading: $this->isRenderGroupsOnly() ? $previousRecordGroupTitle : __('filament-tables::table.summary.subheadings.group', ['group' => $previousRecordGroupTitle, 'label' => $this->getPluralModelLabel()]),
                        query: $groupScopedAllTableSummaryQuery,
                        selectedState: $this->getRenderGroupedSummarySelectedState()[$previousRecordGroupKey] ?? [],
                        actions: $this->hasRenderRecordActions(),
                        actionsPosition: $this->getRecordActionsPosition(),
                        groupColumn: $groupColumn,
                        groupsOnly: $this->isRenderGroupsOnly(),
                        selectionEnabled: $this->isRenderSelectionEnabled(),
                        recordCheckboxPosition: $this->getRecordCheckboxPosition(),
                        attributes: new ComponentAttributeBag(['wire:key' => $groupSummaryKey]),
                    ); ?>
                <?php } ?><?= $this->renderMorphMarkerHtml(isEnd: true); ?>

                <?php if ((! $this->isReordering()) && $this->hasRenderSummary()) { ?>
                    <?php
                $groupColumn = $this->getGrouping()?->getColumn();
                    ?>

                    <?= $this->generateSummaryHtml(
                        columns: $this->getVisibleColumns(),
                        pluralModelLabel: $this->getPluralModelLabel(),
                        records: $records,
                        actions: $this->hasRenderRecordActions(),
                        actionsPosition: $this->getRecordActionsPosition(),
                        allTableSummary: $this->hasAllTableSummary(),
                        groupColumn: $groupColumn,
                        groupsOnly: $this->isRenderGroupsOnly(),
                        pageSummary: $this->hasPageSummary(),
                        recordCheckboxPosition: $this->getRecordCheckboxPosition(),
                        selectionEnabled: $this->isRenderSelectionEnabled(),
                        attributes: new ComponentAttributeBag([]),
                    ); ?>
                <?php } ?>
            </tbody>
        <?php } ?>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateGroupCheckboxHtml(string | Htmlable | null $recordGroupTitle, ?string $recordGroupKey): string
    {
        $livewire = $this->getLivewire();

        ob_start(); ?>
        <input
                    aria-label="<?= e(__('filament-tables::table.fields.bulk_select_group.label', ['title' => $recordGroupTitle])); ?>"
                    type="checkbox"
                    data-group-selectable-record-keys="<?= e(json_encode($livewire->getGroupedSelectableTableRecordKeys($recordGroupKey))); ?>"
                    <?php if ($this->isSelectionDisabled()) { ?>
                        disabled
                    <?php } else { ?>
                        x-on:click="toggleSelectGroupRecords($el)"
                        <?php if ($this->getMaxSelectableRecords()) { ?>
                            x-bind:disabled="shouldDisableSelectGroupRecordsCheckbox($el)"
                        <?php } ?>
                    <?php } ?>
                    x-bind:checked="syncSelectGroupRecordsCheckbox($el)"
                    <?php $this->setRenderKey('' . e($this->getLivewire()->getId()) . '.table.bulk_select_group.checkbox.' . e($livewire->getTablePage()) . ''); ?>wire:key="<?= e($this->getLivewire()->getId()); ?>.table.bulk_select_group.checkbox.<?= e($livewire->getTablePage()); ?>"
                    wire:loading.attr="disabled"
                    wire:target="<?= e(implode(',', Table::LOADING_TARGETS)); ?>"
                    class="fi-ta-group-checkbox fi-checkbox-input"
                />
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     *
     * @param  Model | array<string, mixed>  $record
     */
    protected function generateTableGroupHeaderHtml(Model | array $record, string | Htmlable | null $recordGroupTitle, ?string $recordGroupKey): string
    {
        $livewire = $this->getLivewire();

        ob_start();
        if (! $this->isRenderGroupsOnly()) { ?>
                            <?php
                $isRecordGroupCollapsible = $this->getGrouping()?->isCollapsible();
            $recordGroupLabel = $this->getGrouping()->isTitlePrefixedWithLabel() ? $this->getGrouping()->getLabel() : null;
            $recordGroupDescription = $this->getGrouping()->getDescription($record, $recordGroupTitle);
            $hasRecordGroupCheckbox = $this->isRenderSelectionEnabled() && ($this->getMaxSelectableRecords() !== 1);
            $hasRecordGroupHeader = filled($recordGroupTitle)
                || filled($recordGroupLabel)
                || filled($recordGroupDescription)
                || $isRecordGroupCollapsible
                || $hasRecordGroupCheckbox;
            ?>

                            <tr
                                <?php $groupHeaderKey = $livewire->getId() . '.table.groups.' . md5(json_encode([$this->getGrouping()->getId(), $recordGroupKey])) . '.header'; ?>
                                <?php $this->setRenderKey($groupHeaderKey); ?>
                                wire:key="<?= e($groupHeaderKey); ?>"
                                <?php if (! $hasRecordGroupHeader) { ?>
                                    aria-hidden="true"
                                <?php } ?>
                                class="<?= Arr::toCssClasses([
                                    'fi-ta-row fi-ta-group-header-row',
                                    'fi-ta-group-header-row-with-selection' => $hasRecordGroupCheckbox,
                                ]); ?>"
                            >
                                <?php
                    $groupHeaderColspan = $this->getRenderColumnsCount();

            if ($this->isRenderSelectionEnabled()) {
                $groupHeaderColspan--;

                if (
                    ($this->getRecordCheckboxPosition() === RecordCheckboxPosition::BeforeCells) &&
                    $this->hasRenderRecordActions() &&
                    ($this->getRecordActionsPosition() === RecordActionsPosition::BeforeCells)
                ) {
                    $groupHeaderColspan--;
                }
            }
            ?>

                                <?= $this->renderMorphMarkerHtml(); ?>
                                <?php if ($this->isRenderSelectionEnabled() && $this->getRecordCheckboxPosition() === RecordCheckboxPosition::BeforeCells) { ?>
                                    <?= $this->renderMorphMarkerHtml(); ?>
                                    <?php if ($this->hasRenderRecordActions() && $this->getRecordActionsPosition() === RecordActionsPosition::BeforeCells) { ?>
                                        <td></td>
                                    <?php } ?>
                                    <?= $this->renderMorphMarkerHtml(isEnd: true); ?>

                                    <td
                                        class="fi-ta-cell fi-ta-group-selection-cell"
                                    >
                                        <?php if ($this->getMaxSelectableRecords() !== 1) { ?>
                                            <?= $this->generateGroupCheckboxHtml($recordGroupTitle, $recordGroupKey); ?>
                                        <?php } ?>
                                    </td>
                                <?php } ?>
                                <?= $this->renderMorphMarkerHtml(isEnd: true); ?>

                                <td
                                    colspan="<?= e($groupHeaderColspan); ?>"
                                    class="fi-ta-group-header-cell"
                                >
                                    <div
                                        <?php if ($isRecordGroupCollapsible) { ?>
                                            x-on:click="toggleCollapseGroup(<?= Js::from($recordGroupTitle)->toHtml() ?>)"
                                            x-bind:class="isGroupCollapsed(<?= Js::from($recordGroupTitle)->toHtml() ?>) ? 'fi-collapsed' : null"
                                        <?php } ?>
                                        class="<?= Arr::toCssClasses([
                                            'fi-ta-group-header',
                                            'fi-collapsible' => $isRecordGroupCollapsible,
                                        ]); ?>"
                                    >
                                        <div>
                                            <<?= e($this->getHeadingTag($this->getHeading() ? 1 : 0)); ?>

                                                class="fi-ta-group-heading"
                                            >
                                                <?php if (filled($recordGroupLabel)) { ?>
                                                        <?= e($recordGroupLabel); ?>:
                                                <?php } ?>

                                                <?= e($recordGroupTitle); ?>

                                            </<?= e($this->getHeadingTag($this->getHeading() ? 1 : 0)); ?>>

                                            <?php if (filled($recordGroupDescription)) { ?>
                                                <p
                                                    class="fi-ta-group-description"
                                                >
                                                    <?= e($recordGroupDescription); ?>

                                                </p>
                                            <?php } ?>
                                        </div>

                                        <?php if ($isRecordGroupCollapsible) { ?>
                                            <button
                                                aria-label="<?= e(filled($recordGroupLabel) ? ($recordGroupLabel . ': ' . $recordGroupTitle) : $recordGroupTitle); ?>"
                                                x-bind:aria-expanded="! isGroupCollapsed(<?= Js::from($recordGroupTitle)->toHtml() ?>)"
                                                type="button"
                                                class="fi-icon-btn fi-size-sm"
                                            >
                                                <?= e(\Filament\Support\generate_icon_html(Heroicon::ChevronUp, alias: TablesIconAlias::GROUPING_COLLAPSE_BUTTON, size: IconSize::Small)); ?>

                                            </button>
                                        <?php } ?>
                                    </div>
                                </td>

                                <?php if ($this->isRenderSelectionEnabled() && $this->getRecordCheckboxPosition() === RecordCheckboxPosition::AfterCells) { ?>
                                    <td
                                        class="fi-ta-cell fi-ta-group-selection-cell"
                                    >
                                        <?php if ($this->getMaxSelectableRecords() !== 1) { ?>
                                            <?= $this->generateGroupCheckboxHtml($recordGroupTitle, $recordGroupKey); ?>
                                        <?php } ?>
                                    </td>
                                <?php } ?>
                            </tr>
                        <?php }

        return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     *
     * @param  Model | array<string, mixed>  $record
     * @param  array<Action | ActionGroup>  $recordActions
     */
    protected function generateRecordHtml(
        string $recordKey,
        string | Htmlable | null $recordGroupTitle,
        bool $recordIsSelectable,
        ?string $recordAction,
        ?string $recordUrl,
        bool $isRecordRowStriped,
        Model | array $record,
        array $recordActions,
        ?string $recordGroupKey,
        bool $openRecordUrlInNewTab,
        \stdClass $loop,
    ): string {
        $viewFactory = app('view');

        ob_start(); ?>
        <?php if (! $this->isRenderGroupsOnly()) { ?>
            <tr
                <?php $this->setRenderKey('' . e($this->getLivewire()->getId()) . '.table.records.' . e($recordKey) . ''); ?>wire:key="<?= e($this->getLivewire()->getId()); ?>.table.records.<?= e($recordKey); ?>"
                <?= e($this->isReordering() ? 'x-sortable-handle' : null); ?>

                <?= $this->isReordering() ? 'x-sortable-item="' . e($recordKey) . '"' : null; ?>

                x-bind:class="{
                        <?= e($this->getGrouping()?->isCollapsible() ? '\'fi-collapsed\': isGroupCollapsed(' . Js::from($recordGroupTitle) . '),' : ''); ?>

                        'fi-selected': <?= Js::from($recordIsSelectable)->toHtml() ?> && isRecordSelected(<?= Js::from($recordKey)->toHtml() ?>),
                    }"
                class="<?= Arr::toCssClasses([
                    'fi-ta-row',
                    'fi-clickable' => $recordAction || $recordUrl,
                    'fi-striped' => $this->isStriped() && $isRecordRowStriped,
                    ...$this->getRecordClasses($record),
                ]); ?>"
            >
                <?= $this->renderMorphMarkerHtml(); ?>
                <?php if ($this->isReordering()) { ?>
                    <td class="fi-ta-cell">
                        <?= $this->generateRecordReorderControlsHtml($record, $recordKey, $loop); ?>

                    </td>
                <?php } ?>
                <?= $this->renderMorphMarkerHtml(isEnd: true); ?>

        <?= $this->renderMorphMarkerHtml(); ?>                                                <?php if ($this->hasRenderRecordActions() && $this->getRecordActionsPosition() === RecordActionsPosition::BeforeCells && (! $this->isReordering())) { ?>
                    <td class="fi-ta-cell">
                        <?= $this->generateRecordActionsHtml($recordActions); ?>

                    </td>
                <?php } ?><?= $this->renderMorphMarkerHtml(isEnd: true); ?>

        <?= $this->renderMorphMarkerHtml(); ?>                                                <?php if ($this->isRenderSelectionEnabled() && ($this->getRecordCheckboxPosition() === RecordCheckboxPosition::BeforeCells) && (! $this->isReordering())) { ?>
                    <td
                        class="fi-ta-cell fi-ta-selection-cell"
                    >
                        <?php if ($recordIsSelectable) { ?>
                            <?= $this->generateRecordCheckboxHtml($recordKey, $recordGroupKey); ?>

                        <?php } ?>
                    </td>
                <?php } ?><?= $this->renderMorphMarkerHtml(isEnd: true); ?>

        <?= $this->renderMorphMarkerHtml(); ?>                                                <?php if ($this->hasRenderRecordActions() && $this->getRecordActionsPosition() === RecordActionsPosition::BeforeColumns && (! $this->isReordering())) { ?>
                    <td class="fi-ta-cell">
                        <?= $this->generateRecordActionsHtml($recordActions); ?>

                    </td>
                <?php } ?><?= $this->renderMorphMarkerHtml(isEnd: true); ?>

                <?= $this->renderMorphMarkerHtml(); ?>
                <?php foreach ($this->iterateRenderLoop($this->getVisibleColumns()) as $column) {
                    $loop = $viewFactory->getLastLoop(); ?>
                    <?= $this->generateRecordCellHtml($column, $record, $loop, $recordKey, $recordUrl, $recordAction, $openRecordUrlInNewTab); ?>

                <?php } $loop = $viewFactory->getLastLoop(); ?>
                <?= $this->renderMorphMarkerHtml(isEnd: true); ?>

        <?= $this->renderMorphMarkerHtml(); ?>                                                <?php if ($this->hasRenderRecordActions() && $this->getRecordActionsPosition() === RecordActionsPosition::AfterColumns && (! $this->isReordering())) { ?>
                    <td class="fi-ta-cell">
                        <?= $this->generateRecordActionsHtml($recordActions); ?>

                    </td>
                <?php } ?><?= $this->renderMorphMarkerHtml(isEnd: true); ?>

        <?= $this->renderMorphMarkerHtml(); ?>                                                <?php if ($this->isRenderSelectionEnabled() && $this->getRecordCheckboxPosition() === RecordCheckboxPosition::AfterCells && (! $this->isReordering())) { ?>
                    <td
                        class="fi-ta-cell fi-ta-selection-cell"
                    >
                        <?php if ($recordIsSelectable) { ?>
                            <?= $this->generateRecordCheckboxHtml($recordKey, $recordGroupKey); ?>

                        <?php } ?>
                    </td>
                <?php } ?><?= $this->renderMorphMarkerHtml(isEnd: true); ?>

                <?php if ($this->hasRenderRecordActions() && $this->getRecordActionsPosition() === RecordActionsPosition::AfterCells && (! $this->isReordering())) { ?>
                    <td class="fi-ta-cell">
                        <?= $this->generateRecordActionsHtml($recordActions); ?>

                    </td>
                <?php } ?>
            </tr>
        <?php } ?>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     *
     * @param  Model | array<string, mixed>  $record
     */
    protected function generateRecordReorderControlsHtml(Model | array $record, string $recordKey, \stdClass $loop): string
    {
        $viewFactory = app('view');

        ob_start(); ?>
        <?php
            $reorderLabel = ($record instanceof Model && ($this->hasCustomRecordTitle() || filled($this->getRecordTitleAttribute()))) ? $this->getRecordTitle($record) : $recordKey;
        $reorderPosition = $loop->iteration;
        $reorderCount = $loop->count;
        $reorderDescriptionId = $this->getLivewire()->getId() . '-table-reorder-' . md5($recordKey);
        ?>

        <button
            aria-hidden="true"
            tabindex="-1"
            class="fi-ta-reorder-handle fi-icon-btn"
            type="button"
        >
            <?= e(\Filament\Support\generate_icon_html(Heroicon::Bars2, alias: TablesIconAlias::REORDER_HANDLE)); ?>

        </button>

        <span
            id="<?= e($reorderDescriptionId); ?>"
            class="fi-sr-only"
            data-table-reorder-label="<?= e($reorderLabel); ?>"
        >
            <?= e(__('filament-tables::table.reordering.position', ['label' => $reorderLabel, 'position' => $reorderPosition, 'count' => $reorderCount])); ?>

        </span>

        <div class="fi-sr-only">
            <?php foreach ($this->iterateRenderLoop(['up', 'down']) as $direction) {
                $loop = $viewFactory->getLastLoop(); ?>
                <button
                    type="button"
                    aria-describedby="<?= e($reorderDescriptionId); ?>"
                    <?php if (($direction === 'up') ? ($reorderPosition === 1) : ($reorderPosition === $reorderCount)) {
                        echo 'disabled';
                    } ?>
                    tabindex="-1"
                    data-table-reorder-direction="<?= e($direction); ?>"
                    x-bind:aria-disabled="tableReorderOperation !== null || $el.disabled"
                    x-on:click.stop="moveTableRecord($event, <?= Js::from($recordKey)->toHtml() ?>, <?= Js::from($direction)->toHtml() ?>)"
                >
                    <?= e(__('filament-tables::table.actions.move_record_' . $direction . '.label', ['label' => $reorderLabel])); ?>

                </button>
            <?php } $loop = $viewFactory->getLastLoop(); ?>
        </div>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     *
     * @param  array<Action | ActionGroup>  $recordActions
     */
    protected function generateRecordActionsHtml(array $recordActions): string
    {
        ob_start(); ?>
        <div
            class="<?= Arr::toCssClasses([
                'fi-ta-actions',
                match ($recordActionsAlignment = $this->getRenderRecordActionsAlignment()) {
                    Alignment::Center => 'fi-align-center',
                    Alignment::Start, Alignment::Left => 'fi-align-start',
                    Alignment::Between, Alignment::Justify => 'fi-align-between',
                    Alignment::End, Alignment::Right => '',
                    default => is_string($recordActionsAlignment) ? $recordActionsAlignment : '',
                },
            ]); ?>"
        >
            <?php foreach ($this->iterateRenderLoop($recordActions) as $action) {  ?>
                <?= e($action); ?>

            <?php }  ?>
        </div>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateRecordCheckboxHtml(string $recordKey, ?string $recordGroupKey): string
    {
        ob_start(); ?>
        <input
            aria-label="<?= e(__('filament-tables::table.fields.bulk_select_record.label', ['key' => $recordKey])); ?>"
            type="checkbox"
            <?php if ($this->isSelectionDisabled()) { ?>
                disabled
            <?php } elseif ($this->getMaxSelectableRecords() && ($this->getMaxSelectableRecords() !== 1)) { ?>
                x-bind:disabled="! areRecordsToggleable([<?= Js::from($recordKey)->toHtml() ?>])"
            <?php } ?>
            value="<?= e($recordKey); ?>"
            x-on:click="toggleSelectedRecord(<?= Js::from($recordKey)->toHtml() ?>)"
            x-bind:checked="isRecordSelected(<?= Js::from($recordKey)->toHtml() ?>) ? 'checked' : null"
            data-group="<?= e($recordGroupKey); ?>"
            wire:loading.attr="disabled"
            wire:target="<?= e(implode(',', Table::LOADING_TARGETS)); ?>"
            class="fi-ta-record-checkbox fi-checkbox-input"
        />
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
    ): string {
        $livewire = $this->getLivewire();

        ob_start(); ?>
        <?php
            $column->record($record);
        $column->rowLoop($loop->parent);
        $column->recordKey($recordKey);

        $columnAction = $column->getAction();
        $columnUrl = $column->getUrl();
        $columnHasStateBasedUrls = $column->hasStateBasedUrls();
        $isColumnClickDisabled = $column->isClickDisabled() || $this->isReordering();

        $columnWrapperTag = match (true) {
            ($columnUrl || ($recordUrl && $columnAction === null)) && (! $columnHasStateBasedUrls) && (! $isColumnClickDisabled) => 'a',
            ($columnAction || $recordAction) && (! $columnHasStateBasedUrls) && (! $isColumnClickDisabled) => 'button',
            default => 'div',
        };

        if ($columnWrapperTag === 'button') {
            if ($columnAction instanceof Action) {
                $columnWireClickAction = "mountAction('{$columnAction->getName()}', {}, " . Js::from(['table' => true, 'recordKey' => $recordKey]) . ')';
            } elseif ($columnAction) {
                $columnWireClickAction = "callTableColumnAction('{$column->getName()}', " . Js::from($recordKey) . ')';
            } else {
                if ($livewire->getTable()->getAction($recordAction)) {
                    $columnWireClickAction = "mountAction('{$recordAction}', {}, " . Js::from(['table' => true, 'recordKey' => $recordKey]) . ')';
                } else {
                    $columnWireClickAction = "{$recordAction}(" . Js::from($recordKey) . ')';
                }
            }
        }
        ?>

        <td
            <?php $this->setRenderKey('' . e($this->getLivewire()->getId()) . '.table.record.' . e($recordKey) . '.column.' . e($column->getName()) . ''); ?>wire:key="<?= e($this->getLivewire()->getId()); ?>.table.record.<?= e($recordKey); ?>.column.<?= e($column->getName()); ?>"
            <?= $column->getCachedCellAttributeHtml(); ?>

        >
            <?= $this->isStackedOnMobile() ? '<div class="fi-ta-cell-label">' . e($column->getLabel()) . '</div><div class="fi-ta-cell-content">' : ''; ?>

            <<?= e($columnWrapperTag); ?>

                <?php if ($columnWrapperTag === 'a') { ?>
                    <?= e(\Filament\Support\generate_href_html($columnUrl ?: $recordUrl, $columnUrl ? $column->shouldOpenUrlInNewTab() : $openRecordUrlInNewTab, hasNestedClickEventHandler: true)); ?>

                    <?php if (blank($columnUrl) && filled($recordUrl)) { ?>
                        <?= e($this->getExtraRecordLinkAttributeBag($record)); ?>

                    <?php } ?>
                <?php } elseif ($columnWrapperTag === 'button') { ?>
                    type="button"
                    wire:click.prevent.stop="<?= e($columnWireClickAction); ?>"
                    wire:loading.attr="disabled"
                    wire:target="<?= e($columnWireClickAction); ?>"
                <?php } ?>
                class="<?= Arr::toCssClasses([
                    'fi-ta-col',
                    'fi-ta-col-has-column-url' => ($columnWrapperTag === 'a') && filled($columnUrl),
                ]); ?>"
            >
                <?= e($column); ?>

            </<?= e($columnWrapperTag); ?>>
            <?= $this->isStackedOnMobile() ? '</div>' : ''; ?>

        </td>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateEmptyStateHtml(): string
    {
        $records = $this->isLoaded() ? $this->getRecords() : null;

        ob_start(); ?>
        <?= $this->renderMorphMarkerHtml(); ?>            <?php if (($records !== null) && ! count($records)) { ?>
            <?php if ($emptyState = $this->getEmptyState()) { ?>
                <?= e($emptyState); ?>

            <?php } else { ?>
                <div class="fi-ta-empty-state" role="status">
                    <div class="fi-ta-empty-state-content">
                        <div class="fi-ta-empty-state-icon-bg">
                            <?= e(\Filament\Support\generate_icon_html($this->getEmptyStateIcon(), attributes: (new FilamentComponentAttributeBag([
                                'wire:loading.remove.delay.' . config('filament.livewire_loading_delay', 'default') => '',
                                'wire:target' => implode(',', Table::LOADING_TARGETS),
                            ])), size: IconSize::Large)); ?>

                            <?= e(\Filament\Support\generate_loading_indicator_html((new FilamentComponentAttributeBag([
                                'wire:loading.delay.' . config('filament.livewire_loading_delay', 'default') => '',
                                'wire:target' => implode(',', Table::LOADING_TARGETS),
                            ])), size: IconSize::Large)); ?>

                        </div>

                        <<?= e($this->getHeadingTag($this->getHeading() ? 1 : 0)); ?>

                            class="fi-ta-empty-state-heading"
                        >
                            <?= e($this->getEmptyStateHeading()); ?>

                        </<?= e($this->getHeadingTag($this->getHeading() ? 1 : 0)); ?>>

                        <?= $this->renderMorphMarkerHtml(); ?>
                        <?php if (filled($emptyStateDescription = $this->getEmptyStateDescription())) { ?>
                            <p class="fi-ta-empty-state-description">
                                <?= e($emptyStateDescription); ?>

                            </p>
                        <?php } ?>
                        <?= $this->renderMorphMarkerHtml(isEnd: true); ?>

                        <?php if ($emptyStateActions = array_filter(
                            $this->getEmptyStateActions(),
                            fn (\Filament\Actions\Action | ActionGroup $action): bool => $action->isVisible(),
                        )) { ?>
                            <div
                                class="fi-ta-actions fi-align-center fi-wrapped"
                            >
                                <?php foreach ($this->iterateRenderLoop($emptyStateActions) as $action) {  ?>
                                    <?= e($action); ?>

                                <?php }  ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        <?php } ?><?= $this->renderMorphMarkerHtml(isEnd: true); ?>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generatePaginationContainerHtml(): string
    {
        $records = $this->isLoaded() ? $this->getRecords() : null;

        ob_start(); ?>
        <?= $this->renderMorphMarkerHtml(); ?>
        <?php if ($this->hasRenderPagination()) { ?>
            <?php
                $hasExtremePaginationLinks = $this->hasExtremePaginationLinks();
            $paginationPageOptions = $this->getPaginationPageOptions();
            ?>

            <?= ($records instanceof AbstractPaginator) || ($records instanceof CursorPaginator)
                ? $this->generatePaginationHtml(
                    paginator: $records,
                    livewireId: $this->getLivewire()->getId(),
                    extremeLinks: $hasExtremePaginationLinks,
                    pageOptions: $paginationPageOptions,
                    loadingTargets: Table::LOADING_TARGETS,
                    isRtl: __('filament-panels::layout.direction') === 'rtl',
                )
                : view('filament::components.pagination.index', [
                    'paginator' => $records,
                    'extremeLinks' => $hasExtremePaginationLinks,
                    'pageOptions' => $paginationPageOptions,
                    'loadingTargets' => Table::LOADING_TARGETS,
                    'attributes' => new ComponentAttributeBag,
                ])->render(); ?>
        <?php } ?>
        <?= $this->renderMorphMarkerHtml(isEnd: true); ?>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateFiltersBelowContentHtml(): string
    {
        if (! $this->hasRenderFiltersInLayout(FiltersLayout::BelowContent)) {
            return '';
        }

        return $this->generateFiltersHtml(
            applyAction: $this->getFiltersApplyAction(),
            form: $this->getFiltersForm(),
            headingTag: $this->getHeadingTag($this->getHeading() ? 1 : 0),
            resetAction: $this->getFiltersResetAction(),
            resetActionPosition: $this->getFiltersResetActionPosition(),
            attributes: new ComponentAttributeBag(['class' => 'fi-ta-filters-below-content']),
        );
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateFiltersAfterContentHtml(): string
    {
        ob_start(); ?>
        <?php if ($this->hasRenderFiltersInLayout(FiltersLayout::AfterContent, FiltersLayout::AfterContentCollapsible)) { ?>
            <div
                wire:ignore.self
                x-ref="filtersContentContainer"
                x-transition:enter-start="fi-opacity-0"
                x-transition:leave-end="fi-opacity-0"
                x-bind:class="{ 'fi-open': areFiltersOpen }"
                class="<?= Arr::toCssClasses([
                    'fi-ta-filters-after-content-ctn',
                    'lg:fi-open' => ! $this->hasRenderCollapsibleFilters(),
                    (($filtersFormWidth = $this->getRenderFiltersFormWidth() ?? Width::ExtraSmall) instanceof Width) ? "fi-width-{$filtersFormWidth->value}" : $filtersFormWidth,
                ]); ?>"
            >
                <?= $this->generateFiltersHtml(
                    applyAction: $this->getFiltersApplyAction(),
                    form: $this->getFiltersForm(),
                    headingTag: $this->getHeadingTag($this->getHeading() ? 1 : 0),
                    resetAction: $this->getFiltersResetAction(),
                    resetActionPosition: $this->getFiltersResetActionPosition(),
                    attributes: new ComponentAttributeBag(['class' => 'fi-ta-filters-after-content']),
                ); ?>
            </div>
        <?php } ?>
        <?php return ob_get_clean();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateActionModalsHtml(): string
    {
        $livewire = $this->getLivewire();

        if (! $livewire instanceof HasActions) {
            return '';
        }

        if (method_exists($livewire, 'getActionModalsHtml')) {
            return $livewire->getActionModalsHtml()->toHtml();
        }

        return view('filament-actions::components.modals')->render();
    }
}
