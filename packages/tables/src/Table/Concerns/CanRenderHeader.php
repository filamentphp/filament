<?php

namespace Filament\Tables\Table\Concerns;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Support\Facades\FilamentView;
use Filament\Tables\Actions\HeaderActionsPosition;
use Filament\Tables\View\TablesRenderHook;
use Illuminate\Support\Arr;
use Illuminate\Support\Js;

trait CanRenderHeader
{
    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     *
     * @return array<Action | ActionGroup>
     */
    protected function getRenderHeaderActions(): array
    {
        return array_filter(
            $this->getHeaderActions(),
            static fn (Action | ActionGroup $action): bool => $action->isVisible(),
        );
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
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
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     *
     * @return array<Action | ActionGroup>
     */
    protected function getRenderToolbarActions(): array
    {
        return array_filter(
            $this->getToolbarActions(),
            static fn (Action | ActionGroup $action): bool => $action->isVisible(),
        );
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function hasRenderHeader(): bool
    {
        return $this->getHeader() || $this->getHeading() || $this->getDescription() || ($this->getRenderHeaderActions() && (! $this->isReordering())) || $this->isReorderable() || $this->areRenderGroupingSettingsVisible() || $this->isSearchable() || $this->isFilterable() || count($this->getFilterIndicators()) || $this->hasColumnManager();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function getRenderHeaderVisibilityMode(): string
    {
        return ($this->hasRenderHeader() || $this->hasRenderNonBulkToolbarAction())
            ? 'visible'
            : (count($this->getRenderToolbarActions()) ? 'selection' : 'hidden');
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function hasRenderHeaderToolbar(): bool
    {
        return $this->isReorderable() || $this->areRenderGroupingSettingsVisible() || $this->isSearchable() || $this->hasRenderFiltersTrigger() || $this->hasColumnManager();
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function getRenderHeaderToolbarVisibilityMode(): string
    {
        return ($this->hasRenderHeaderToolbar() || $this->hasRenderNonBulkToolbarAction())
            ? 'visible'
            : (count($this->getRenderToolbarActions()) ? 'selection' : 'hidden');
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function generateHeaderHtml(): string
    {
        $livewire = $this->getLivewire();

        ob_start(); ?>
        <div
            <?php if (! $this->hasRenderHeader()) { ?>
                x-cloak
            <?php } ?>
            x-show="<?= Js::from($this->hasRenderHeader())->toHtml() ?> || <?= Js::from($this->hasRenderNonBulkToolbarAction())->toHtml() ?> || (getSelectedRecordsCount() && <?= Js::from(count($this->getRenderToolbarActions()))->toHtml() ?>)"
            <?php $this->setRenderKey('' . e($this->getLivewire()->getId()) . '.table.header.' . e($this->getRenderHeaderVisibilityMode()) . ''); ?>
            wire:key="<?= e($this->getLivewire()->getId()); ?>.table.header.<?= e($this->getRenderHeaderVisibilityMode()); ?>"
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
        <?= $this->generateMorphMarkerHtml(); ?>
        <?php if ($header) { ?>
            <?= e($header); ?>
        <?php } elseif ($heading || $description || ($this->getRenderHeaderActions() && (! $this->isReordering()))) { ?>
            <div
                class="<?= Arr::toCssClasses([
                    'fi-ta-header',
                    'fi-ta-header-adaptive-actions-position' => $this->getRenderHeaderActions() && (! $this->isReordering()) && ($this->getHeaderActionsPosition() === HeaderActionsPosition::Adaptive),
                ]); ?>"
            >
                <?= $this->generateMorphMarkerHtml(); ?>
                <?php if ($heading || $description) { ?>
                    <div>
                        <?= $this->generateMorphMarkerHtml(); ?>
                        <?php if ($heading) { ?>
                            <<?= e($this->getHeadingTag()); ?>
                                class="fi-ta-header-heading"
                            >
                                <?= e($heading); ?>
                            </<?= e($this->getHeadingTag()); ?>>
                        <?php } ?>
                        <?= $this->generateMorphMarkerHtml(isEnd: true); ?>

                        <?php if ($description) { ?>
                            <p class="fi-ta-header-description">
                                <?= e($description); ?>
                            </p>
                        <?php } ?>
                    </div>
                <?php } ?>
                <?= $this->generateMorphMarkerHtml(isEnd: true); ?>

                <?php if ((! $this->isReordering()) && $this->getRenderHeaderActions()) { ?>
                    <div
                        class="fi-ta-actions fi-align-start fi-wrapped"
                    >
                        <?php foreach ($this->iterateRenderLoop($this->getRenderHeaderActions()) as $action) { ?>
                            <?= e($action); ?>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
        <?= $this->generateMorphMarkerHtml(isEnd: true); ?>
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
            <?php if (! $this->hasRenderHeaderToolbar()) { ?>
                x-cloak
            <?php } ?>
            x-show="<?= Js::from($this->hasRenderHeaderToolbar())->toHtml() ?> || <?= Js::from($this->hasRenderNonBulkToolbarAction())->toHtml() ?> || (getSelectedRecordsCount() && <?= Js::from(count($this->getRenderToolbarActions()))->toHtml() ?>)"
            <?php $this->setRenderKey('' . e($this->getLivewire()->getId()) . '.table.header-toolbar.' . e($this->getRenderHeaderToolbarVisibilityMode()) . ''); ?>
            wire:key="<?= e($this->getLivewire()->getId()); ?>.table.header-toolbar.<?= e($this->getRenderHeaderToolbarVisibilityMode()); ?>"
            class="fi-ta-header-toolbar"
        >
            <?= e(FilamentView::renderHook(TablesRenderHook::TOOLBAR_START, scopes: $livewire::class)); ?>

            <?= $this->generateToolbarActionsHtml(); ?>

            <?= $this->generateMorphMarkerHtml(); ?>
            <?php if ($this->isSearchable() || $this->hasRenderFiltersTrigger() || $this->hasColumnManager()) { ?>
                <div>
                    <?= e(FilamentView::renderHook(TablesRenderHook::TOOLBAR_SEARCH_BEFORE, scopes: $livewire::class)); ?>

                    <?= $this->generateMorphMarkerHtml(); ?>
                    <?= $this->generateGlobalSearchHtml(); ?>
                    <?= $this->generateMorphMarkerHtml(isEnd: true); ?>

                    <?= e(FilamentView::renderHook(TablesRenderHook::TOOLBAR_SEARCH_AFTER, scopes: $livewire::class)); ?>

                    <?php if ($this->hasRenderFiltersTrigger() || $this->hasColumnManager()) { ?>
                        <?= $this->generateMorphMarkerHtml(); ?>
                        <?= $this->generateFiltersTriggerHtml(); ?>
                        <?= $this->generateMorphMarkerHtml(isEnd: true); ?>

                        <?= e(FilamentView::renderHook(TablesRenderHook::TOOLBAR_COLUMN_MANAGER_TRIGGER_BEFORE, scopes: $livewire::class)); ?>

                        <?= $this->generateMorphMarkerHtml(); ?>
                        <?= $this->generateColumnManagerTriggerHtml(); ?>
                        <?= $this->generateMorphMarkerHtml(isEnd: true); ?>

                        <?= e(FilamentView::renderHook(TablesRenderHook::TOOLBAR_COLUMN_MANAGER_TRIGGER_AFTER, scopes: $livewire::class)); ?>
                    <?php } ?>
                </div>
            <?php } ?>
            <?= $this->generateMorphMarkerHtml(isEnd: true); ?>

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

            <?= $this->generateMorphMarkerHtml(); ?>
            <?php if ($this->isReorderable()) { ?>
                <?= e($this->getReorderRecordsTriggerAction($this->isReordering())); ?>
            <?php } ?>
            <?= $this->generateMorphMarkerHtml(isEnd: true); ?>

            <?= e(FilamentView::renderHook(TablesRenderHook::TOOLBAR_REORDER_TRIGGER_AFTER, scopes: $livewire::class)); ?>

            <?= $this->generateMorphMarkerHtml(); ?>
            <?php if ((! $this->isReordering()) && count($this->getRenderToolbarActions())) { ?>
                <?php foreach ($this->iterateRenderLoop($this->getRenderToolbarActions()) as $action) { ?>
                    <?= e($action); ?>
                <?php } ?>
            <?php } ?>
            <?= $this->generateMorphMarkerHtml(isEnd: true); ?>

            <?= e(FilamentView::renderHook(TablesRenderHook::TOOLBAR_GROUPING_SELECTOR_BEFORE, scopes: $livewire::class)); ?>

            <?= $this->generateGroupingSettingsHtml(); ?>

            <?= e(FilamentView::renderHook(TablesRenderHook::TOOLBAR_GROUPING_SELECTOR_AFTER, scopes: $livewire::class)); ?>
        </div>
        <?php return ob_get_clean();
    }
}
