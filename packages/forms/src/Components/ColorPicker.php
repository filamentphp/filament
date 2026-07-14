<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Actions\Action;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Js;

class ColorPicker extends Field implements Contracts\HasAffixes, HasEmbeddedView
{
    use Concerns\HasAffixes;
    use Concerns\HasExtraInputAttributes;
    use Concerns\HasPlaceholder;
    use HasExtraAlpineAttributes;

    protected ?string $publishedViewOverrideCheckPath = 'filament-forms::components.color-picker';

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
            static fn (Action $action): bool => $action->isVisible(),
        );
        $suffixActions = array_filter(
            $suffixActions,
            static fn (Action $action): bool => $action->isVisible(),
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
                // `aria-expanded` / `aria-haspopup` are not supported on a plain textbox, so only
                // `aria-controls` (a global ARIA attribute) associates the input with its panel.
                'aria-controls' => "{$id}-panel",
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
                'fi-input-has-inline-prefix' => $isPrefixInline && $hasPrefix,
                'fi-input-has-inline-suffix' => $isSuffixInline && $hasSuffix,
            ]);

        $wrapperAttributes = $extraAttributeBag
            ->merge([
                'x-on:focus-input.stop' => "\$el.querySelector('input')?.focus()",
            ], escape: false)
            ->class(['fi-fo-color-picker']);

        $alpineComponentSrc = FilamentAsset::getAlpineComponentSrc('color-picker', 'filament/forms');

        ob_start(); ?>

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
                aria-hidden="true"
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
                id="<?= e($id) ?>-panel"
                role="dialog"
                aria-label="<?= e(__('filament-forms::components.color_picker.panel_label')) ?>"
                class="fi-fo-color-picker-panel"
            >
                <<?= $tag ?> x-ref="picker" color="<?= e($this->getState()) ?>" />
            </div>
        </div>

        <?php $slotHtml = ob_get_clean();

        return $this->wrapEmbeddedHtml(
            $this->wrapInputHtml(
                $slotHtml,
                attributes: $wrapperAttributes,
            ),
            inlineLabelVerticalAlignment: VerticalAlignment::Center,
        );
    }
}
