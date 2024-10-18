<?php

namespace Filament\Tables\Columns;

use Filament\Forms\Components\Concerns\HasExtraInputAttributes;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentView;
use Filament\Tables\Columns\Contracts\Editable;
use Illuminate\Support\Js;

class CheckboxColumn extends Column implements Editable, HasEmbeddedView
{
    use Concerns\CanBeValidated;
    use Concerns\CanUpdateState;
    use HasExtraInputAttributes;

    protected function setUp(): void
    {
        parent::setUp();

        $this->disabledClick();

        $this->rules(['boolean']);
    }

    public function toEmbeddedHtml(): string
    {
        $isDisabled = $this->isDisabled();
        $state = (bool) $this->getState();

        $attributes = $this->getExtraAttributeBag()
            ->merge([
                'ax-load' => FilamentView::hasSpaMode()
                    ? 'visible || event (ax-modal-opened)'
                    : true,
                'ax-load-src' => FilamentAsset::getAlpineComponentSrc('columns/checkbox', 'filament/tables'),
                'x-data' => 'checkboxTableColumn({
                    name: ' . Js::from($this->getName()) . ',
                    recordKey: ' . Js::from($this->getRecordKey()) . ',
                    state: ' . Js::from($state) . ',
                })',
            ], escape: false)
            ->class([
                'fi-ta-checkbox',
                'fi-inline' => $this->isInline(),
            ]);

        $inputAttributes = $this->getExtraInputAttributeBag()
            ->merge([
                'disabled' => $isDisabled,
                'x-bind:disabled' => $isDisabled ? null : 'isLoading',
            ], escape: false)
            ->class([
                'fi-checkbox-input',
            ]);

        ob_start(); ?>

        <div
            x-ignore
            wire:ignore.self
            <?= $attributes->toHtml() ?>
        >
            <input type="hidden" value="<?= $state ? 1 : 0 ?>" x-ref="serverState" />

            <input
                type="checkbox"
                x-bind:class="{
                    'fi-valid': ! error,
                    'fi-invalid': error,
                }"
                x-model="state"
                x-tooltip="
                    error === undefined
                        ? false
                        : {
                            content: error,
                            theme: $store.theme,
                        }
                "
                x-on:click.stop=""
                <?= $inputAttributes->toHtml() ?>
            />
        </div>

        <?php return ob_get_clean();
    }
}
