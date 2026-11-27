<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Actions\Action;
use Filament\Forms\View\FormsIconAlias;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Js;

use function Filament\Support\generate_icon_html;

class ColorPicker extends Field implements Contracts\HasAffixes, HasEmbeddedView
{
    use Concerns\HasAffixes;
    use Concerns\HasExtraInputAttributes;
    use Concerns\HasLoadingPlaceholder;
    use Concerns\HasPlaceholder;
    use HasExtraAlpineAttributes;

    protected ?string $publishedViewOverrideCheckPath = 'filament-forms::components.color-picker';

    protected string | Closure $format = 'hex';

    protected bool | Closure $hasEyeDropper = false;

    protected bool | Closure $isCopyable = false;

    /**
     * @var array<string> | Arrayable | Closure | null
     */
    protected array | Arrayable | Closure | null $presetColors = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->string();
    }

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

    public function copyable(
        bool | Closure $condition = true,
        string | Closure | null $copyMessage = null,
        int | Closure | null $copyMessageDuration = null
    ): static {
        $this->isCopyable = $condition;

        $this->suffixAction(
            TextInput\Actions\CopyAction::make()
                ->copyMessage($copyMessage)
                ->copyMessageDuration($copyMessageDuration)
                ->visible($condition),
        );

        return $this;
    }

    public function eyeDropper(bool | Closure $condition = true): static
    {
        $this->hasEyeDropper = $condition;

        return $this;
    }

    /**
     * @param  array<string> | Arrayable | Closure | null  $colors
     */
    public function presetColors(array | Arrayable | Closure | null $colors): static
    {
        $this->presetColors = $colors;

        return $this;
    }

    public function getFormat(): string
    {
        return $this->evaluate($this->format);
    }

    /**
     * @return array<string> | null
     */
    public function getPresetColors(): ?array
    {
        $colors = $this->evaluate($this->presetColors);

        if ($colors instanceof Arrayable) {
            $colors = $colors->toArray();
        }

        return $colors;
    }

    public function hasEyeDropper(): bool
    {
        return (bool) $this->evaluate($this->hasEyeDropper);
    }

    public function isCopyable(): bool
    {
        return (bool) $this->evaluate($this->isCopyable);
    }

    public function toEmbeddedHtml(): string
    {
        $extraAttributeBag = $this->getExtraAttributeBag();
        $hasEyeDropper = $this->hasEyeDropper();
        $id = $this->getId();
        $isAutofocused = $this->isAutofocused();
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
        $presetColors = $this->getPresetColors();

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
            ->merge($this->getAccessibilityAttributes(), escape: false)
            ->merge([
                'aria-controls' => "{$id}-panel",
                'aria-expanded' => 'false',
                'aria-haspopup' => 'dialog',
                'autocomplete' => 'off',
                'disabled' => $isDisabled,
                'id' => $id,
                'placeholder' => filled($placeholder) ? e($placeholder) : null,
                'required' => $this->isRequired(),
                'role' => 'combobox',
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
            x-bind:inert="isLoading"
            x-bind:aria-hidden="isLoading"
            inert
            aria-hidden="true"
            x-load
            x-load-src="<?= e($alpineComponentSrc) ?>"
            x-data="colorPickerFormComponent({
                        format: <?= Js::from($format) ?>,
                        isAutofocused: <?= Js::from($isAutofocused) ?>,
                        isDisabled: <?= Js::from($isDisabled) ?>,
                        isLive: <?= Js::from($isLive) ?>,
                        isLiveDebounced: <?= Js::from($isLiveDebounced) ?>,
                        isLiveOnBlur: <?= Js::from($isLiveOnBlur) ?>,
                        liveDebounce: <?= Js::from($liveDebounce) ?>,
                        state: $wire.$entangle('<?= e($statePath) ?>'),
                    })"
            x-on:dropdown-escape="if (isOpen()) { closePanelAndRestoreFocus(); $event.preventDefault() }"
            x-on:keydown.esc="isOpen() && $event.stopPropagation()"
            x-on:focusout="if (isOpen() && ! $el.contains($event.relatedTarget)) $refs.panel.close()"
            wire:key="<?= e($livewireKey) ?>.<?= substr(md5(serialize([$isDisabled])), 0, 64) ?>"
            <?= $this->getExtraAlpineAttributeBag()->class(['fi-input-wrp-content', 'fi-async-component-content'])->toHtml() ?>
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
                tabindex="-1"
                class="<?= e(($hasEyeDropper || $presetColors) ? 'fi-fo-color-picker-panel fi-fo-color-picker-panel-has-controls' : 'fi-fo-color-picker-panel') ?>"
            >
                <<?= $tag ?> x-ref="picker" color="<?= e($this->getState()) ?>"></<?= $tag ?>>

                <?php if ($hasEyeDropper || $presetColors) { ?>
                    <div class="fi-fo-color-picker-controls">
                        <?php if ($hasEyeDropper) { ?>
                            <button
                                type="button"
                                aria-label="<?= e(__('filament-forms::components.color_picker.actions.eye_dropper.label')) ?>"
                                x-show="isEyeDropperSupported()"
                                x-on:click="openEyeDropper()"
                                class="fi-fo-color-picker-eye-dropper-btn"
                            >
                                <?= generate_icon_html(Heroicon::EyeDropper, alias: FormsIconAlias::COMPONENTS_COLOR_PICKER_ACTIONS_EYE_DROPPER)?->toHtml() ?>
                            </button>
                        <?php } ?>

                        <?php if ($presetColors) { ?>
                            <div role="group" aria-label="<?= e(__('filament-forms::components.color_picker.preset_colors.label')) ?>" class="fi-fo-color-picker-preset-colors">
                                <?php foreach ($presetColors as $presetColor => $presetColorLabel) { ?>
                                    <?php $presetColor = is_int($presetColor) ? $presetColorLabel : $presetColor; ?>
                                    <button
                                        type="button"
                                        aria-label="<?= e($presetColorLabel) ?>"
                                        title="<?= e($presetColorLabel) ?>"
                                        style="background-color: <?= e($presetColor) ?>"
                                        x-on:click="selectColor(<?= e(Js::from($presetColor)) ?>)"
                                        x-bind:aria-pressed="(state === <?= e(Js::from($presetColor)) ?>).toString()"
                                        x-bind:class="{
                                            'fi-active': state === <?= e(Js::from($presetColor)) ?>,
                                        }"
                                        class="fi-fo-color-picker-preset-color"
                                    ></button>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>

        <?php $slotHtml = $this->wrapAsyncComponentHtml(
            ob_get_clean(),
            key: $livewireKey . '.' . md5(serialize([$isDisabled])),
        );

        return $this->wrapEmbeddedHtml(
            $this->wrapInputHtml(
                $slotHtml,
                attributes: $wrapperAttributes,
            ),
            inlineLabelVerticalAlignment: VerticalAlignment::Center,
        );
    }
}
