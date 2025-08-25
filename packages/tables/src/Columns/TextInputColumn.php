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
use Filament\Tables\Columns\Contracts\Editable;
use Filament\Tables\Table;
use Illuminate\Support\Js;

class TextInputColumn extends Column implements Editable, HasEmbeddedView
{
    use Concerns\CanBeValidated;
    use Concerns\CanUpdateState;
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
        </div>

        <?php return ob_get_clean();
    }
}
