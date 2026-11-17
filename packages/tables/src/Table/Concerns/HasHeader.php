<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Filament\Support\Facades\FilamentView;
use Filament\Tables\Enums\HeaderActionsPosition;
use Filament\Tables\View\TablesRenderHook;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Js;

trait HasHeader
{
    protected string | Htmlable | Closure | null $heading = null;

    protected View | Htmlable | Closure | null $header = null;

    protected string | Htmlable | Closure | null $description = null;

    public function description(string | Htmlable | Closure | null $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function header(View | Htmlable | Closure | null $header): static
    {
        $this->header = $header;

        return $this;
    }

    public function heading(string | Htmlable | Closure | null $heading): static
    {
        $this->heading = $heading;

        return $this;
    }

    public function getHeader(): View | Htmlable | null
    {
        return $this->evaluate($this->header);
    }

    public function getHeading(): string | Htmlable | null
    {
        return $this->evaluate($this->heading);
    }

    public function getDescription(): string | Htmlable | null
    {
        return $this->evaluate($this->description);
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
}
