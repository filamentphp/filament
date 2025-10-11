<?php

namespace Filament\Tables\Columns;

use Filament\Forms\Components\Concerns\HasToggleColors;
use Filament\Forms\Components\Concerns\HasToggleIcons;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\IconSize;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\View\Components\ToggleComponent;
use Filament\Tables\Columns\Contracts\Editable;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
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

        $attributes = $this->getExtraAttributeBag()
            ->merge([
                'x-load' => true,
                'x-load-src' => FilamentAsset::getAlpineComponentSrc('columns/toggle', 'filament/tables'),
                'x-data' => 'toggleTableColumn({
                    name: ' . Js::from($this->getName()) . ',
                    recordKey: ' . Js::from($this->getRecordKey()) . ',
                    state: ' . Js::from($state) . ',
                })',
                'x-tooltip' => filled($tooltip = $this->getTooltip($state))
                    ? '{
                        content: ' . Js::from($tooltip) . ',
                        theme: $store.theme,
                        allowHTML: ' . Js::from($tooltip instanceof Htmlable) . ',
                    }'
                    : null,
            ], escape: false)
            ->class([
                'fi-ta-toggle',
                ((($alignment = $this->getAlignment()) instanceof Alignment) ? "fi-align-{$alignment->value}" : (is_string($alignment) ? $alignment : '')),
                'fi-inline' => $this->isInline(),
            ]);

        $buttonAttributes = (new ComponentAttributeBag)
            ->merge([
                'disabled' => $this->isDisabled(),
                'wire:loading.attr' => 'disabled',
                'wire:target' => implode(',', Table::LOADING_TARGETS),
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
                x-on:click.prevent.stop="if (! $el.hasAttribute('disabled')) state = ! state"
                x-bind:class="state ? '<?= Arr::toCssClasses([
                    'fi-toggle-on',
                    ...get_component_color_classes(ToggleComponent::class, $onColor),
                ]) ?>' : '<?= Arr::toCssClasses([
                    'fi-toggle-off',
                    ...get_component_color_classes(ToggleComponent::class, $offColor),
                ]) ?>'"
                <?php if ($state) { ?> x-cloak <?php } ?>
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
                        <?= generate_icon_html($onIcon, size: IconSize::ExtraSmall)?->toHtml() ?>
                    </div>
                </div>
            </div>

            <?php if ($state) { ?>
                <div
                    x-cloak="inline-flex"
                    wire:ignore
                    class="<?= Arr::toCssClasses([
                        'fi-toggle fi-toggle-on fi-hidden',
                        ...get_component_color_classes(ToggleComponent::class, $onColor),
                    ]) ?>"
                >
                    <div>
                        <div aria-hidden="true"></div>

                        <div aria-hidden="true">
                            <?= generate_icon_html($onIcon, size: IconSize::ExtraSmall)?->toHtml() ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>

        <?php return ob_get_clean();
    }
}
