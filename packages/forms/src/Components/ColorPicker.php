<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Schemas\Components\Contracts\HasAffixActions;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\View\Components\InputComponent\WrapperComponent\IconComponent;
use Illuminate\Support\Js;
use Illuminate\View\ComponentAttributeBag;

use function Filament\Support\generate_icon_html;

class ColorPicker extends Field implements HasAffixActions, HasEmbeddedView
{
    use Concerns\HasAffixes;
    use Concerns\HasExtraInputAttributes;
    use Concerns\HasPlaceholder;
    use HasExtraAlpineAttributes;

    protected string | Closure $format = 'hex';

    public function format(string | Closure $format): static
    {
        $this->format = $format;

        return $this;
    }

    public function hex(): static
    {
        $this->format('hex');

        return $this;
    }

    public function hsl(): static
    {
        $this->format('hsl');

        return $this;
    }

    public function rgb(): static
    {
        $this->format('rgb');

        return $this;
    }

    public function rgba(): static
    {
        $this->format('rgba');

        return $this;
    }

    public function getFormat(): string
    {
        return $this->evaluate($this->format);
    }

    public function toEmbeddedHtml(): string
    {
        $extraAttributeBag = $this->getExtraAttributeBag();
        $id = $this->getId();
        $isAutofocused = $this->isAutofocused();
        $isConcealed = $this->isConcealed();
        $isDisabled = $this->isDisabled();
        $isLive = $this->isLive();
        $isLiveOnBlur = $this->isLiveOnBlur();
        $isLiveDebounced = $this->isLiveDebounced();
        $isPrefixInline = $this->isPrefixInline();
        $isSuffixInline = $this->isSuffixInline();
        $liveDebounce = $this->getLiveDebounce();
        $prefixActions = $this->getPrefixActions();
        $prefixIcon = $this->getPrefixIcon();
        $prefixIconColor = $this->getPrefixIconColor();
        $prefixLabel = $this->getPrefixLabel();
        $suffixActions = $this->getSuffixActions();
        $suffixIcon = $this->getSuffixIcon();
        $suffixIconColor = $this->getSuffixIconColor();
        $suffixLabel = $this->getSuffixLabel();
        $statePath = $this->getStatePath();
        $placeholder = $this->getPlaceholder();
        $livewireKey = $this->getLivewireKey();
        $format = $this->getFormat();

        // Filter visible prefix/suffix actions
        $prefixActions = array_filter(
            $prefixActions,
            static fn (\Filament\Actions\Action $action): bool => $action->isVisible(),
        );
        $suffixActions = array_filter(
            $suffixActions,
            static fn (\Filament\Actions\Action $action): bool => $action->isVisible(),
        );

        $hasPrefix = count($prefixActions) || $prefixIcon || filled($prefixLabel);
        $hasSuffix = count($suffixActions) || $suffixIcon || filled($suffixLabel);

        $tag = match ($format) {
            'hsl' => 'hsl-string',
            'rgb' => 'rgb-string',
            'rgba' => 'rgba-string',
            default => 'hex',
        } . '-color-picker';

        $inputAttributes = $this->getExtraInputAttributeBag()
            ->merge([
                'autocomplete' => 'off',
                'disabled' => $isDisabled,
                'id' => $id,
                'placeholder' => filled($placeholder) ? e($placeholder) : null,
                'required' => $this->isRequired() && (! $isConcealed),
                'type' => 'text',
                'x-model' . ($isLiveDebounced ? '.debounce.' . $liveDebounce : null) => 'state',
                'x-on:blur' => $isLiveOnBlur ? 'isOpen() ? null : commitState()' : null,
            ], escape: false)
            ->class([
                'fi-input',
                'fi-input-has-inline-prefix' => $isPrefixInline && (count($prefixActions) || $prefixIcon || filled($prefixLabel)),
                'fi-input-has-inline-suffix' => $isSuffixInline && (count($suffixActions) || $suffixIcon || filled($suffixLabel)),
            ]);

        $wrapperAttributes = \Filament\Support\prepare_inherited_attributes($extraAttributeBag)
            ->merge([
                'x-on:focus-input.stop' => "\$el.querySelector('input')?.focus()",
            ], escape: false)
            ->class([
                'fi-input-wrp',
                'fi-fo-color-picker',
                'fi-disabled' => $isDisabled,
                'fi-invalid' => filled($statePath) && view()->shared('errors')?->has($statePath),
            ]);

        $alpineComponentSrc = FilamentAsset::getAlpineComponentSrc('color-picker', 'filament/forms');

        ob_start(); ?>

        <div <?= $wrapperAttributes->toHtml() ?>>
            <?php if ($hasPrefix) { ?>
                <div
                    <?= (new ComponentAttributeBag)->class([
                        'fi-input-wrp-prefix',
                        'fi-input-wrp-prefix-has-content' => true,
                        'fi-inline' => $isPrefixInline,
                        'fi-input-wrp-prefix-has-label' => filled($prefixLabel),
                    ])->toHtml() ?>
                >
                    <?php if (count($prefixActions)) { ?>
                        <div class="fi-input-wrp-actions">
                            <?php foreach ($prefixActions as $prefixAction) { ?>
                                <?= $prefixAction->toHtml() ?>
                            <?php } ?>
                        </div>
                    <?php } ?>

                    <?= generate_icon_html($prefixIcon, null, (new ComponentAttributeBag)->color(IconComponent::class, $prefixIconColor))?->toHtml() ?>

                    <?php if (filled($prefixLabel)) { ?>
                        <span class="fi-input-wrp-label">
                            <?= e($prefixLabel) ?>
                        </span>
                    <?php } ?>
                </div>
            <?php } ?>

            <div
                x-load
                x-load-src="<?= e($alpineComponentSrc) ?>"
                x-data="colorPickerFormComponent({
                            isAutofocused: <?= Js::from($isAutofocused) ?>,
                            isDisabled: <?= Js::from($isDisabled) ?>,
                            isLive: <?= Js::from($isLive) ?>,
                            isLiveDebounced: <?= Js::from($isLiveDebounced) ?>,
                            isLiveOnBlur: <?= Js::from($isLiveOnBlur) ?>,
                            liveDebounce: <?= Js::from($liveDebounce) ?>,
                            state: $wire.$entangle('<?= e($statePath) ?>'),
                        })"
                x-on:keydown.esc="isOpen() && $event.stopPropagation()"
                x-on:focusout="if (isOpen() && ! $el.contains($event.relatedTarget)) $refs.panel.close()"
                <?= $this->getExtraAlpineAttributeBag()->class(['fi-input-wrp-content'])->toHtml() ?>
            >
                <input
                    x-on:focus="$refs.panel.open($refs.input)"
                    x-on:keydown.enter.prevent.stop="togglePanelVisibility()"
                    x-ref="input"
                    <?= $inputAttributes->toHtml() ?>
                />

                <div
                    class="fi-fo-color-picker-preview my-auto me-3 size-5 shrink-0 rounded-full select-none"
                    x-on:click="togglePanelVisibility()"
                    x-bind:class="{
                        'fi-empty': ! state,
                    }"
                    x-bind:style="{ 'background-color': state }"
                ></div>

                <div
                    wire:ignore.self
                    wire:key="<?= e($livewireKey) ?>.panel"
                    x-cloak
                    x-float.placement.bottom-start.offset.flip.shift="{ offset: 8 }"
                    x-ref="panel"
                    class="fi-fo-color-picker-panel"
                >
                    <<?= $tag ?> x-ref="picker" color="<?= e($this->getState()) ?>" />
                </div>
            </div>

            <?php if ($hasSuffix) { ?>
                <div
                    <?= (new ComponentAttributeBag)->class([
                        'fi-input-wrp-suffix',
                        'fi-inline' => $isSuffixInline,
                        'fi-input-wrp-suffix-has-label' => filled($suffixLabel),
                    ])->toHtml() ?>
                >
                    <?php if (filled($suffixLabel)) { ?>
                        <span class="fi-input-wrp-label">
                            <?= e($suffixLabel) ?>
                        </span>
                    <?php } ?>

                    <?= generate_icon_html($suffixIcon, null, (new ComponentAttributeBag)->color(IconComponent::class, $suffixIconColor))?->toHtml() ?>

                    <?php if (count($suffixActions)) { ?>
                        <div class="fi-input-wrp-actions">
                            <?php foreach ($suffixActions as $suffixAction) { ?>
                                <?= $suffixAction->toHtml() ?>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>

        <?php return $this->wrapEmbeddedHtml(ob_get_clean(), inlineLabelVerticalAlignment: \Filament\Support\Enums\VerticalAlignment::Center);
    }
}
