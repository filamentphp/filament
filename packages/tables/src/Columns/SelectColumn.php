<?php

namespace Filament\Tables\Columns;

use Filament\Forms\Components\Concerns\CanDisableOptions;
use Filament\Forms\Components\Concerns\CanSelectPlaceholder;
use Filament\Forms\Components\Concerns\HasEnum;
use Filament\Forms\Components\Concerns\HasExtraInputAttributes;
use Filament\Forms\Components\Concerns\HasOptions;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentView;
use Filament\Tables\Columns\Contracts\Editable;
use Filament\Tables\Table;
use Illuminate\Support\Js;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class SelectColumn extends Column implements Editable, HasEmbeddedView
{
    use CanDisableOptions;
    use CanSelectPlaceholder;
    use Concerns\CanBeValidated {
        getRules as getBaseRules;
    }
    use Concerns\CanUpdateState;
    use HasEnum;
    use HasExtraInputAttributes;
    use HasOptions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->disabledClick();

        $this->placeholder(__('filament-forms::components.select.placeholder'));
    }

    /**
     * @return array<array-key>
     */
    public function getRules(): array
    {
        return [
            ...$this->getBaseRules(),
            (filled($enum = $this->getEnum()) ?
                new Enum($enum) :
                Rule::in(array_keys($this->getEnabledOptions()))),
        ];
    }

    public function toEmbeddedHtml(): string
    {
        $isDisabled = $this->isDisabled();
        $state = $this->getState();

        $attributes = $this->getExtraAttributeBag()
            ->merge([
                'x-load' => FilamentView::hasSpaMode()
                    ? 'visible || event (x-modal-opened)'
                    : true,
                'x-load-src' => FilamentAsset::getAlpineComponentSrc('columns/select', 'filament/tables'),
                'x-data' => 'selectTableColumn({
                    name: ' . Js::from($this->getName()) . ',
                    recordKey: ' . Js::from($this->getRecordKey()) . ',
                    state: ' . Js::from($state) . ',
                })',
            ], escape: false)
            ->class([
                'fi-ta-select',
                'fi-inline' => $this->isInline(),
            ]);

        $inputAttributes = $this->getExtraInputAttributeBag()
            ->merge([
                'disabled' => $isDisabled,
                'wire:loading.attr' => 'disabled',
                'wire:target' => implode(',', Table::LOADING_TARGETS),
                'x-bind:disabled' => $isDisabled ? null : 'isLoading',
                'x-tooltip' => filled($tooltip = $this->getTooltip($state))
                    ? '{
                        content: ' . Js::from($tooltip) . ',
                        theme: $store.theme,
                    }'
                    : null,
            ], escape: false)
            ->class([
                'fi-select-input',
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
                x-on:click.stop.prevent=""
                class="fi-input-wrp"
            >
                <select
                    x-model="state"
                    <?= $inputAttributes->toHtml() ?>
                >
                    <?php if ($this->canSelectPlaceholder()) { ?>
                        <option value=""><?= $this->getPlaceholder() ?></option>
                    <?php } ?>

                    <?php foreach ($this->getOptions() as $value => $label) { ?>
                        <option
                            <?= $this->isOptionDisabled($value, $label) ? 'disabled' : null ?>
                            value="<?= $value ?>"
                        >
                            <?= $label ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <?php return ob_get_clean();
    }
}
