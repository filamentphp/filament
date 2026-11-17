<?php

namespace Filament\Tables\Table\Concerns;

use Filament\Support\Facades\FilamentView;
use Filament\Tables\View\TablesRenderHook;
use Illuminate\Support\Js;

trait HasToolbar
{
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
}
