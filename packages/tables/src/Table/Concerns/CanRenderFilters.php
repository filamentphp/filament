<?php

namespace Filament\Tables\Table\Concerns;

use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Support\Components\ViewComponent;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\Width;
use Filament\Support\Facades\FilamentView;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\FiltersResetActionPosition;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\View\TablesRenderHook;
use Illuminate\Support\Arr;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\ComponentSlot;

trait CanRenderFilters
{
    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function hasRenderFiltersInLayout(FiltersLayout ...$layouts): bool
    {
        return $this->isFilterable() && in_array($this->getFiltersLayout(), $layouts);
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function hasRenderCollapsibleFilters(): bool
    {
        return $this->hasRenderFiltersInLayout(FiltersLayout::AboveContentCollapsible, FiltersLayout::BeforeContentCollapsible, FiltersLayout::AfterContentCollapsible);
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
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

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function getRenderFiltersFormWidth(): Width | string | null
    {
        $width = $this->getFiltersFormWidth();

        return is_string($width) ? (Width::tryFrom($width) ?? $width) : $width;
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
}
