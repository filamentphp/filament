<?php

namespace Filament\Support\View\Concerns;

use BackedEnum;
use Filament\Support\Components\ViewComponent;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\IconSize;
use Filament\Support\Enums\SlideOverPosition;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Support\View\ComponentAttributeBag;
use Filament\Support\View\Components\ModalComponent;
use Filament\Support\View\Components\ModalComponent\IconComponent;
use Filament\Support\View\SupportIconAlias;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Js;
use Illuminate\View\ComponentAttributeBag as BaseComponentAttributeBag;
use Illuminate\View\ComponentSlot;

use function Filament\Support\generate_icon_html;
use function Filament\Support\is_slot_empty;

trait CanGenerateModalHtml
{
    use CanGenerateIconButtonHtml;
    use InteractsWithLivewireRendering;

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     *
     * @param  array<string | Htmlable> | Htmlable | null  $footerActions
     * @param  string | array<string> | null  $iconAlias
     * @param  string | array<int, string> | null  $iconColor
     */
    public function generateModalHtml(
        string | Htmlable $slot = '',
        ?BaseComponentAttributeBag $attributes = null,
        bool $alert = false,
        Alignment | string | null $alignment = null,
        ?string $ariaLabelledby = null,
        ?bool $autofocus = null,
        bool $clickThrough = false,
        ?bool $closeButton = null,
        ?bool $closeByClickingAway = null,
        ?bool $closeByEscaping = null,
        string $closeEventName = 'close-modal',
        string $closeQuietlyEventName = 'close-modal-quietly',
        string | Htmlable | null $description = null,
        string | Htmlable | null $eyebrow = null,
        bool $restoresFocus = true,
        ?BaseComponentAttributeBag $extraModalWindowAttributeBag = null,
        ?BaseComponentAttributeBag $extraModalOverlayAttributeBag = null,
        ?Htmlable $footer = null,
        array | Htmlable | null $footerActions = [],
        Alignment | string | null $footerActionsAlignment = null,
        string | Htmlable | null $header = null,
        string | Htmlable | null $heading = null,
        string | BackedEnum | Htmlable | null $icon = null,
        string | array | null $iconAlias = null,
        string | array | null $iconColor = 'primary',
        ?string $id = null,
        string $openEventName = 'open-modal',
        bool $slideOver = false,
        SlideOverPosition | string | null $slideOverPosition = null,
        bool $stickyFooter = false,
        bool $stickyHeader = false,
        ?string $teleport = null,
        ?ComponentSlot $trigger = null,
        bool $visible = true,
        Width | string | null $width = 'sm',
        ?string $livewireId = null,
    ): string {
        $attributes ??= new ComponentAttributeBag;
        $slot = $slot instanceof ComponentSlot ? $slot : new ComponentSlot(e($slot));

        if (ViewComponent::hasPublishedEmbeddedViewOverride('filament::components.modal.index')) {
            return view('filament::components.modal.index', get_defined_vars())->render();
        }

        $alignment ??= Alignment::Start;
        $autofocus ??= ModalComponent::$isAutofocused;
        $closeButton ??= ModalComponent::$hasCloseButton;
        $closeByClickingAway ??= ModalComponent::$isClosedByClickingAway;
        $closeByEscaping ??= ModalComponent::$isClosedByEscaping;
        $footerActionsAlignment ??= Alignment::Start;
        $slideOverPosition ??= SlideOverPosition::End;
        $width ??= 'sm';
        $hasContent = ! is_slot_empty($slot);
        $hasDescription = filled($description);
        $hasEyebrow = filled($eyebrow);
        $hasFooter = (! is_slot_empty($footer)) || (is_array($footerActions) ? count($footerActions) : (! is_slot_empty($footerActions)));
        $hasHeading = filled($heading);
        $iconHtml = ($icon || $iconAlias) ? generate_icon_html($icon, $iconAlias, size: IconSize::Large) : null;
        $hasIcon = $iconHtml !== null;
        $headingId = filled($id) ? "{$id}.heading" : null;
        $descriptionId = ($hasDescription && $hasHeading && (! $header) && filled($id)) ? "{$id}.description" : null;
        $alignment = is_string($alignment) ? (filled($alignment) ? (Alignment::tryFrom($alignment) ?? $alignment) : null) : $alignment;
        $footerActionsAlignment = is_string($footerActionsAlignment) ? (filled($footerActionsAlignment) ? (Alignment::tryFrom($footerActionsAlignment) ?? $footerActionsAlignment) : null) : $footerActionsAlignment;
        $width = is_string($width) ? (Width::tryFrom($width) ?? $width) : $width;
        $closeEventHandler = filled($id) ? '$dispatch(' . Js::from($closeEventName) . ', { id: ' . Js::from($id) . ' })' : 'close()';
        $wireSubmitHandler = $attributes->get('wire:submit.prevent');
        $attributes = $attributes->except(['wire:submit.prevent']);
        $isClickThrough = $clickThrough;

        if ($isClickThrough) {
            $closeByClickingAway = false;
        }

        $isModalWindowAutofocusedByDefault = $closeButton && (! $closeByEscaping) && ($heading || $header);
        $modalWindowAttributeBag = ($extraModalWindowAttributeBag ?? new ComponentAttributeBag)->merge(['autofocus' => $isModalWindowAutofocusedByDefault]);
        $shouldTrapFocusOnModalWindow = (! $isClickThrough)
            && ($slideOver || (($width !== Width::Screen) && ($stickyHeader || $stickyFooter)))
            && in_array($modalWindowAttributeBag->get('autofocus'), [false, null], true)
            && (! $modalWindowAttributeBag->has('tabindex'));
        $modalWindowAttributeBag = $modalWindowAttributeBag->merge(['tabindex' => $shouldTrapFocusOnModalWindow ? '0' : ($isModalWindowAutofocusedByDefault ? '-1' : null)]);
        $wireKeyPrefix = filled($livewireId) ? "{$livewireId}." : '';
        $windowTag = filled($wireSubmitHandler) ? 'form' : 'div';

        ob_start();
        if ($trigger) { ?>
            <div <?php if ((! $id) && (! $trigger->attributes->get('disabled'))) { ?> x-data="filamentModalTrigger({ openEventName: <?= e(Js::from($openEventName)) ?> })" <?php } ?>><div
                <?php if (! $trigger->attributes->get('disabled')) { ?>
                    <?php if ($id) { ?> x-on:click="$dispatch(<?= e(Js::from($openEventName)) ?>, { id: <?= e(Js::from($id)) ?> })"
                    <?php } else { ?> x-on:click="openModalFromTrigger($el)" <?php } ?>
                <?php } ?>
                <?= $trigger->attributes->except(['disabled'])->class(['fi-modal-trigger']) ?>
            ><?= e($trigger) ?></div>
        <?php }
        if (filled($teleport)) {
            echo "<template x-teleport=\"{$teleport}\">";
        } ?>
        <div
            <?php if ($descriptionId) { ?> aria-describedby="<?= e($descriptionId) ?>" <?php } ?>
            <?php if ($ariaLabelledby) { ?> aria-labelledby="<?= e($ariaLabelledby) ?>"
            <?php } elseif ($hasHeading && $headingId) { ?> aria-labelledby="<?= e($headingId) ?>"
            <?php } elseif ($hasHeading) { ?> aria-label="<?= e(trim(strip_tags($heading instanceof Htmlable ? $heading->toHtml() : $heading))) ?>" <?php } ?>
            aria-modal="<?= $isClickThrough ? 'false' : 'true' ?>" id="<?= e($id) ?>" role="<?= $alert ? 'alertdialog' : 'dialog' ?>" tabindex="-1"
            x-data="filamentModal({ id: <?= e(Js::from($id)) ?>, isScrollLocked: <?= e(Js::from(! $isClickThrough)) ?>, shouldRestoreFocus: <?= e(Js::from($restoresFocus && (! $isClickThrough))) ?>, })"
            <?php if ($id) { ?>
                data-fi-modal-id="<?= e($id) ?>"
                x-on:<?= e($closeEventName) ?>.window="handleCloseEvent($event)"
                x-on:<?= e($closeQuietlyEventName) ?>.window="handleCloseQuietlyEvent($event)"
                x-on:<?= e($openEventName) ?>.window="handleOpenEvent($event)"
            <?php } else { ?>
                x-on:<?= e($closeEventName) ?>.stop="isOpen && close()"
                x-on:<?= e($closeQuietlyEventName) ?>.stop="isOpen && closeQuietly()"
                x-on:<?= e($openEventName) ?>.stop="! isOpen && open()"
            <?php } ?>
            x-bind:class="{ 'fi-modal-open': isOpen, }" x-cloak x-show="isOpen"
            <?= $attributes->class([
                'fi-modal', 'fi-absolute-positioning-context',
                'fi-modal-slide-over' => $slideOver,
                'fi-modal-slide-over-from-start' => $slideOver && $slideOverPosition === SlideOverPosition::Start,
                'fi-modal-slide-over-from-end' => $slideOver && $slideOverPosition === SlideOverPosition::End,
                'fi-modal-has-sticky-header' => $stickyHeader,
                'fi-modal-has-sticky-footer' => $stickyFooter,
                'fi-width-screen' => $width === Width::Screen,
                'fi-modal-click-through' => $isClickThrough,
            ]) ?>
        >
            <?= $this->renderMorphMarkerHtml() ?>
            <?php if (! $isClickThrough) { ?><div aria-hidden="true" x-show="isOpen" x-transition.duration.300ms.opacity <?= ($extraModalOverlayAttributeBag ?? new ComponentAttributeBag)->class(['fi-modal-close-overlay']) ?>></div><?php } ?>
            <?= $this->renderMorphMarkerHtml(isEnd: true) ?>
            <div
                <?php if (! $shouldTrapFocusOnModalWindow) { ?> tabindex="-1" <?php } ?>
                <?php if ($closeByClickingAway) { ?> x-on:click.self="<?= e($closeEventHandler) ?>" <?php } ?>
                <?php if ((! $isClickThrough) && (! $shouldTrapFocusOnModalWindow)) { ?> x-trap.noreturn<?= $autofocus ? '' : '.noautofocus' ?>="isTrapActive" <?php } ?>
                <?= (new ComponentAttributeBag)->class(['fi-modal-window-ctn', 'fi-clickable' => $closeByClickingAway]) ?>
            >
                <<?= $windowTag ?>
                    <?php if ($closeByEscaping) { ?> x-on:keydown.window.escape="isTopmost() && <?= e($closeEventHandler) ?>" <?php } ?>
                    x-show="isWindowVisible" x-transition:enter="fi-transition-enter" x-transition:leave="fi-transition-leave"
                    <?php if ($width !== Width::Screen) { ?> x-transition:enter-start="fi-transition-enter-start" x-transition:enter-end="fi-transition-enter-end" x-transition:leave-start="fi-transition-leave-start" x-transition:leave-end="fi-transition-leave-end" <?php } ?>
                    <?php if (filled($wireSubmitHandler)) { ?> wire:submit.prevent="<?= $wireSubmitHandler ?>" <?php } ?>
                    <?php if (filled($id)) { ?> wire:key="<?= e("{$wireKeyPrefix}modal.{$id}.window") ?>" <?php } ?>
                    <?php if ($shouldTrapFocusOnModalWindow) { ?> x-trap.noreturn<?= $autofocus ? '' : '.noautofocus' ?>="isTrapActive" <?php } ?>
                    <?= $modalWindowAttributeBag->class([
                        'fi-modal-window',
                        'fi-modal-window-has-close-btn' => $closeButton,
                        'fi-modal-window-has-content' => $hasContent,
                        'fi-modal-window-has-footer' => $hasFooter,
                        'fi-modal-window-has-icon' => $hasIcon,
                        'fi-hidden' => ! $visible,
                        $alignment instanceof Alignment ? "fi-align-{$alignment->value}" : null,
                        $width instanceof Width ? "fi-width-{$width->value}" : $width,
                    ]) ?>
                >
                    <?= filled($id) ? '' : $this->renderMorphMarkerHtml() ?>
                    <?php if ($heading || $header) { ?>
                        <div
                            <?php if (filled($id)) { ?> wire:key="<?= e("{$wireKeyPrefix}modal.{$id}.header") ?>" <?php } ?>
                            <?= (new ComponentAttributeBag)->class(['fi-modal-header', 'fi-vertical-align-center' => $hasIcon && $hasHeading && (! $hasEyebrow) && (! $hasDescription) && in_array($alignment, [Alignment::Start, Alignment::Left])]) ?>
                        >
                            <?= $this->renderMorphMarkerHtml() ?>
                            <?php if ($closeButton) {
                                echo $this->generateIconButtonHtml(attributes: new ComponentAttributeBag(['tabindex' => $closeByEscaping ? '-1' : null, 'x-on:click' => e($closeEventHandler), 'class' => 'fi-modal-close-btn']), color: 'gray', icon: Heroicon::OutlinedXMark, iconAlias: SupportIconAlias::MODAL_CLOSE_BUTTON, iconSize: 'lg', label: __('filament::components/modal.actions.close.label'), shouldCheckForPublishedView: true);
                            } ?>
                            <?= $this->renderMorphMarkerHtml(isEnd: true) ?>
                            <?php if ($header) {
                                echo e($header);
                            } else { ?>
                                <?= $this->renderMorphMarkerHtml() ?>
                                <?php if ($hasIcon) { ?><div class="fi-modal-icon-ctn"><div <?= (new ComponentAttributeBag)->color(IconComponent::class, $iconColor)->class(['fi-modal-icon-bg']) ?>><?= e($iconHtml) ?></div></div><?php } ?>
                                <?= $this->renderMorphMarkerHtml(isEnd: true) ?>
                                <div>
                                    <?= filled($id) ? '' : $this->renderMorphMarkerHtml() ?>
                                    <?php if ($hasHeading && $hasEyebrow) { ?><p class="fi-modal-eyebrow"><?= e($eyebrow) ?></p><?php } ?>
                                    <?= filled($id) ? '' : $this->renderMorphMarkerHtml(isEnd: true) ?>
                                    <h2 <?php if ($headingId) { ?> id="<?= e($headingId) ?>" <?php } ?> class="fi-modal-heading"><?= e($heading) ?></h2>
                                    <?php if ($hasDescription) { ?><p <?php if ($descriptionId) { ?> id="<?= e($descriptionId) ?>" <?php } ?> class="fi-modal-description"><?= e($description) ?></p><?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                    <?= filled($id) ? '' : $this->renderMorphMarkerHtml(isEnd: true) ?>
                    <?= filled($id) ? '' : $this->renderMorphMarkerHtml() ?>
                    <?php if ($hasContent) { ?><div <?php if (filled($id)) { ?> wire:key="<?= e("{$wireKeyPrefix}modal.{$id}.content") ?>" <?php } ?> class="fi-modal-content"><?= e($slot) ?></div><?php } ?>
                    <?= filled($id) ? '' : $this->renderMorphMarkerHtml(isEnd: true) ?>
                    <?php if ($hasFooter) { ?>
                        <div <?php if (filled($id)) { ?> wire:key="<?= e("{$wireKeyPrefix}modal.{$id}.footer") ?>" <?php } ?> <?= (new ComponentAttributeBag)->class(['fi-modal-footer', $footerActionsAlignment instanceof Alignment ? "fi-align-{$footerActionsAlignment->value}" : null]) ?>>
                            <?php if (! is_slot_empty($footer)) {
                                echo e($footer);
                            } else { ?>
                                <div class="fi-modal-footer-actions"><?php if (is_array($footerActions)) {
                                    foreach ($this->iterateRenderLoop($footerActions) as $action) {
                                        echo e($action);
                                    }
                                } else {
                                    echo e($footerActions);
                                } ?></div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </<?= $windowTag ?>>
            </div>
        </div>
        <?php
        if (filled($teleport)) {
            echo '</template>';
        }
        if ($trigger) {
            echo '</div>';
        }

        return ob_get_clean();
    }
}
