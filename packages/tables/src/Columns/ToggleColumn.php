<?php

namespace Filament\Tables\Columns;

use Filament\Forms\Components\Concerns\HasToggleColors;
use Filament\Forms\Components\Concerns\HasToggleIcons;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentView;
use Filament\Tables\Columns\Contracts\Editable;
use Illuminate\Support\Arr;
use Illuminate\Support\Js;
use Illuminate\View\ComponentAttributeBag;

use function Filament\Support\generate_icon_html;
use function Filament\Support\get_color_css_variables;

class ToggleColumn extends Column implements Editable, HasEmbeddedView
{
    use Concerns\CanBeValidated;
    use Concerns\CanUpdateState;
    use HasToggleColors;
    use HasToggleIcons;

    protected function setUp(): void
    {
        parent::setUp();

        $this->disabledClick();

        $this->rules(['boolean']);
    }

    public function toEmbeddedHtml(): string
    {
        $offColor = $this->getOffColor() ?? 'gray';
        $offIcon = $this->getOffIcon();
        $onColor = $this->getOnColor() ?? 'primary';
        $onIcon = $this->getOnIcon();
        $state = (bool) $this->getState();

        $attributes = (new ComponentAttributeBag)
            ->merge([
                'ax-load' => FilamentView::hasSpaMode()
                    ? 'visible || event (ax-modal-opened)'
                    : true,
                'ax-load-src' => FilamentAsset::getAlpineComponentSrc('columns/toggle', 'filament/tables'),
                'disabled' => $this->isDisabled(),
                'x-data' => 'toggleTableColumn({
                    name: ' . Js::from($this->getName()) . ',
                    recordKey: ' . Js::from($this->getRecordKey()) . ',
                    state: ' . Js::from($state) . ',
                })',
            ], escape: false)
            ->class([
                'fi-ta-toggle',
                'fi-inline' => $this->isInline(),
            ]);

        $buttonAttributes = (new ComponentAttributeBag)
            ->merge([
                'disabled' => $this->isDisabled(),
            ], escape: false)
            ->class(['fi-toggle']);

        ob_start(); ?>

        <div
            x-ignore
            wire:ignore.self
            <?= $attributes->toHtml() ?>
        >
            <input type="hidden" value="<?= $state ? 1 : 0 ?>" x-ref="serverState" />

            <button
                x-bind:aria-checked="state?.toString()"
                x-on:click="state = ! state"
                x-bind:class="state ? '<?= Arr::toCssClasses([
                    'fi-toggle-on',
                    match ($onColor) {
                        'gray' => null,
                        default => 'fi-color-custom',
                    },
                    is_string($onColor) ? "fi-color-{$onColor}" : null,
                ]) ?>' : '<?= Arr::toCssClasses([
                    'fi-toggle-off',
                    match ($offColor) {
                        'gray' => null,
                        default => 'fi-color-custom bg-custom-600',
                    },
                    is_string($offColor) ? "fi-color-{$offColor}" : null,
                ]) ?>'"
                x-bind:style="state ? '<?= get_color_css_variables(
                    $onColor,
                    shades: [600],
                    alias: 'toggle.on',
                ) ?>' : '<?= get_color_css_variables(
                    $offColor,
                    shades: [600],
                    alias: 'toggle.off',
                ) ?>'"
                x-tooltip="
                    error === undefined
                        ? false
                        : {
                            content: error,
                            theme: $store.theme,
                        }
                "
                role="switch"
                type="button"
                <?= $buttonAttributes->toHtml() ?>
            >
                <div>
                    <div aria-hidden="true">
                        <?= generate_icon_html($offIcon)?->toHtml() ?>
                    </div>

                    <div aria-hidden="true">
                        <?= generate_icon_html(
                            $onIcon,
                            attributes: (new ComponentattributeBag)->merge(['x-cloak' => true], escape: false),
                        )?->toHtml() ?>
                    </div>
                </div>
            </button>
        </div>

        <?php return ob_get_clean();
    }
}
