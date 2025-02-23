<?php

namespace Filament\Tables\Columns;

use Filament\Forms\Components\Concerns\HasToggleColors;
use Filament\Forms\Components\Concerns\HasToggleIcons;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Enums\IconSize;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentView;
use Filament\Support\View\Components\ToggleComponent;
use Filament\Tables\Columns\Contracts\Editable;
use Filament\Tables\Table;
use Illuminate\Support\Arr;
use Illuminate\Support\Js;
use Illuminate\View\ComponentAttributeBag;

use function Filament\Support\generate_icon_html;
use function Filament\Support\get_component_color_classes;

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
                'x-load' => FilamentView::hasSpaMode()
                    ? 'visible || event (x-modal-opened)'
                    : true,
                'x-load-src' => FilamentAsset::getAlpineComponentSrc('columns/toggle', 'filament/tables'),
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
                'wire:loading.attr' => 'disabled',
                'wire:target' => implode(',', Table::LOADING_TARGETS),
                'x-tooltip' => filled($tooltip = $this->getTooltip($state))
                    ? '{
                        content: ' . Js::from($tooltip) . ',
                        theme: $store.theme,
                    }'
                    : null,
            ], escape: false)
            ->class(['fi-toggle']);

        ob_start(); ?>

        <div
            wire:ignore.self
            <?= $attributes->toHtml() ?>
        >
            <input type="hidden" value="<?= $state ? 1 : 0 ?>" x-ref="serverState" />

            <div
                x-bind:aria-checked="state?.toString()"
                x-on:click="if (! $el.hasAttribute('disabled')) state = ! state"
                x-bind:class="state ? '<?= Arr::toCssClasses([
                    'fi-toggle-on',
                    ...get_component_color_classes(ToggleComponent::class, $onColor),
                ]) ?>' : '<?= Arr::toCssClasses([
                    'fi-toggle-off',
                    ...get_component_color_classes(ToggleComponent::class, $offColor),
                ]) ?>'"
                x-tooltip="
                    error === undefined
                        ? false
                        : {
                            content: error,
                            theme: $store.theme,
                        }
                "
                role="switch"
                <?= $buttonAttributes->toHtml() ?>
            >
                <div>
                    <div aria-hidden="true">
                        <?= generate_icon_html($offIcon, size: IconSize::ExtraSmall)?->toHtml() ?>
                    </div>

                    <div aria-hidden="true">
                        <?= generate_icon_html(
                            $onIcon,
                            attributes: (new ComponentAttributeBag)->merge(['x-cloak' => 'x-cloak'], escape: false),
                            size: IconSize::ExtraSmall,
                        )?->toHtml() ?>
                    </div>
                </div>
            </div>
        </div>

        <?php return ob_get_clean();
    }
}
