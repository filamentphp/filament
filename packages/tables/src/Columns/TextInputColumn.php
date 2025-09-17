<?php

namespace Filament\Tables\Columns;

use Closure;
use Filament\Forms\Components\Concerns\HasExtraInputAttributes;
use Filament\Forms\Components\Concerns\HasInputMode;
use Filament\Forms\Components\Concerns\HasStep;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Enums\Alignment;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\RawJs;
use Filament\Tables\Columns\Concerns\HasExtraContent;
use Filament\Tables\Columns\Contracts\Editable;
use Filament\Tables\Table;
use Illuminate\Support\Js;

class TextInputColumn extends Column implements Editable, HasEmbeddedView
{
    use Concerns\CanBeValidated;
    use Concerns\CanUpdateState;
    use HasExtraContent;
    use HasExtraInputAttributes;
    use HasInputMode;
    use HasStep;

    protected string | RawJs | Closure | null $mask = null;

    protected string | Closure | null $type = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->disabledClick();
    }

    public function type(string | Closure | null $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getType(): string
    {
        return $this->evaluate($this->type) ?? 'text';
    }

    public function mask(string | RawJs | Closure | null $mask): static
    {
        $this->mask = $mask;

        return $this;
    }

    public function getMask(): string | RawJs | null
    {
        return $this->evaluate($this->mask);
    }

    public function toEmbeddedHtml(): string
    {
        $isDisabled = $this->isDisabled();
        $state = $this->getState();
        $mask = $this->getMask();

        $alignment = $this->getAlignment() ?? Alignment::Start;

        if (! $alignment instanceof Alignment) {
            $alignment = filled($alignment) ? (Alignment::tryFrom($alignment) ?? $alignment) : null;
        }

        $type = filled($mask) ? 'text' : $this->getType();

        $attributes = $this->getExtraAttributeBag()
            ->merge([
                'x-load' => true,
                'x-load-src' => FilamentAsset::getAlpineComponentSrc('columns/text-input', 'filament/tables'),
                'x-data' => 'textInputTableColumn({
                    name: ' . Js::from($this->getName()) . ',
                    recordKey: ' . Js::from($this->getRecordKey()) . ',
                    state: ' . Js::from($state) . ',
                })',
            ], escape: false)
            ->class([
                'fi-ta-text-input',
                'fi-inline' => $this->isInline(),
            ]);

        $inputAttributes = $this->getExtraInputAttributeBag()
            ->merge([
                'disabled' => $isDisabled,
                'wire:loading.attr' => 'disabled',
                'wire:target' => implode(',', Table::LOADING_TARGETS),
                'x-bind:disabled' => $isDisabled ? null : 'isLoading',
                'inputmode' => $this->getInputMode(),
                'placeholder' => $this->getPlaceholder(),
                'step' => $this->getStep(),
                'type' => $type,
                'x-mask' . ($mask instanceof RawJs ? ':dynamic' : '') => filled($mask) ? $mask : null,
                'x-tooltip' => filled($tooltip = $this->getTooltip($state))
                    ? '{
                        content: ' . Js::from($tooltip) . ',
                        theme: $store.theme,
                    }'
                    : null,
            ], escape: false)
            ->class([
                'fi-input',
                ($alignment instanceof Alignment) ? "fi-align-{$alignment->value}" : (is_string($alignment) ? $alignment : ''),
            ]);

        ob_start(); ?>

        <div
            wire:ignore.self
            <?= $attributes->toHtml() ?>
        >
            <input type="hidden" value="<?= str($state)->replace('"', '\\"') ?>" x-ref="serverState" />

            <?php if ($this->hasExtraContent('above_label')) { ?>
                <div class="fi-ta-text-input-above-label mb-1">
                    <?= $this->renderExtraContent('above_label') ?>
                </div>
            <?php } ?>

            <?php if ($this->hasExtraContent('before_label')) { ?>
                <div class="fi-ta-text-input-before-label">
                    <?= $this->renderExtraContent('before_label') ?>
                </div>
            <?php } ?>

            <?php if ($this->getLabel()) { ?>
                <label class="fi-ta-text-input-label">
                    <?= $this->getLabel() ?>
                </label>
            <?php } ?>

            <?php if ($this->hasExtraContent('after_label')) { ?>
                <div class="fi-ta-text-input-after-label">
                    <?= $this->renderExtraContent('after_label') ?>
                </div>
            <?php } ?>

            <?php if ($this->hasExtraContent('below_label')) { ?>
                <div class="fi-ta-text-input-below-label mb-1">
                    <?= $this->renderExtraContent('below_label') ?>
                </div>
            <?php } ?>

            <?php if ($this->hasExtraContent('above_content')) { ?>
                <div class="fi-ta-text-input-above-content mb-1">
                    <?= $this->renderExtraContent('above_content') ?>
                </div>
            <?php } ?>

            <?php if ($this->hasExtraContent('before_content')) { ?>
                <div class="fi-ta-text-input-before-content">
                    <?= $this->renderExtraContent('before_content') ?>
                </div>
            <?php } ?>

            <div
                x-bind:class="{
                    'fi-disabled': isLoading || <?= Js::from($isDisabled) ?>,
                    'fi-invalid': error !== undefined,
                }"
                x-tooltip="
                    error === undefined
                        ? false
                        : {
                            content: error,
                            theme: $store.theme,
                        }
                "
                x-on:click.stop
                class="fi-input-wrp"
            >
                <input
                    x-model.lazy="state"
                    <?= $inputAttributes->toHtml() ?>
                />
            </div>

            <?php if ($this->hasExtraContent('after_content')) { ?>
                <div class="fi-ta-text-input-after-content">
                    <?= $this->renderExtraContent('after_content') ?>
                </div>
            <?php } ?>

            <?php if ($this->hasExtraContent('below_content')) { ?>
                <div class="fi-ta-text-input-below-content mt-1">
                    <?= $this->renderExtraContent('below_content') ?>
                </div>
            <?php } ?>

            <?php if ($this->hasExtraContent('above_error')) { ?>
                <div class="fi-ta-text-input-above-error text-xs text-red-500 mb-1" x-show="error !== undefined">
                    <?= $this->renderExtraContent('above_error') ?>
                </div>
            <?php } ?>

            <div x-show="error !== undefined" class="fi-ta-text-input-error text-xs text-red-500">
                <span x-text="error"></span>
            </div>

            <?php if ($this->hasExtraContent('below_error')) { ?>
                <div class="fi-ta-text-input-below-error text-xs text-gray-500 mt-1" x-show="error !== undefined">
                    <?= $this->renderExtraContent('below_error') ?>
                </div>
            <?php } ?>
        </div>

        <?php return ob_get_clean();
    }
}
