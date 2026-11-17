<?php

namespace Filament\Tables\Table\Concerns;

use Filament\Actions\Contracts\HasActions;
use Filament\Support\Facades\FilamentView;
use Filament\Support\View\Concerns\CanGenerateBadgeHtml;
use Filament\Support\View\Concerns\CanGenerateDropdownHtml;
use Filament\Support\View\Concerns\CanGenerateLinkHtml;
use Filament\Support\View\Concerns\CanGenerateModalHtml;
use Filament\Support\View\Concerns\CanGeneratePaginationHtml;
use Filament\Support\View\Concerns\InteractsWithLivewireRendering;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\View\TablesRenderHook;
use Illuminate\Support\Arr;
use Illuminate\Support\Js;

trait CanBeRendered
{
    use CanGenerateBadgeHtml;
    use CanGenerateDropdownHtml;
    use CanGenerateLinkHtml;
    use CanGenerateModalHtml;
    use CanGeneratePaginationHtml;
    use CanRenderColumnHeaders;
    use CanRenderColumnManager;
    use CanRenderContent;
    use CanRenderFilters;
    use CanRenderGroups;
    use CanRenderHeader;
    use CanRenderRecordActions;
    use CanRenderRecords;
    use CanRenderReordering;
    use CanRenderSearch;
    use CanRenderSelection;
    use CanRenderSummaries;
    use InteractsWithLivewireRendering;

    public function toEmbeddedHtml(): string
    {
        $viewFactory = app('view');

        // Isolate record-dependent results across repeated and nested renders.
        $previousRecordActionScan = $this->cachedRenderRecordActionScan;
        $previousAllSelectableRecordsCount = $this->cachedRenderAllSelectableRecordsCount;
        $previousHasSummary = $this->cachedHasRenderSummary;
        $previousGroupedSummarySelectedState = $this->cachedRenderGroupedSummarySelectedState;
        $previousPartiallyRenderableColumnNames = $this->cachedRenderPartiallyRenderableColumnNames;
        $this->cachedRenderRecordActionScan = null;
        $this->cachedRenderAllSelectableRecordsCount = null;
        $this->cachedHasRenderSummary = null;
        $this->cachedRenderGroupedSummarySelectedState = null;
        $this->cachedRenderPartiallyRenderableColumnNames = null;
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
            $this->cachedRenderPartiallyRenderableColumnNames = $previousPartiallyRenderableColumnNames;
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
                x-intersect.once="$wire.loadTable()"
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
