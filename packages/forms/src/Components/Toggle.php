<?php

namespace Filament\Forms\Components;

use Filament\Schemas\Components\StateCasts\BooleanStateCast;
use Filament\Schemas\Components\StateCasts\Contracts\StateCast;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Filament\Support\Enums\IconSize;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Support\View\Components\ToggleComponent;
use Illuminate\Support\Arr;
use Illuminate\Support\Js;
use Illuminate\View\ComponentAttributeBag;

use function Filament\Support\generate_icon_html;
use function Filament\Support\get_component_color_classes;

class Toggle extends Field implements HasEmbeddedView
{
    use Concerns\CanBeAccepted;
    use Concerns\CanBeInline;
    use Concerns\CanFixIndistinctState;
    use Concerns\HasToggleColors;
    use Concerns\HasToggleIcons;
    use HasExtraAlpineAttributes;

    protected function setUp(): void
    {
        parent::setUp();

        $this->default(false);

        $this->rule('boolean');
    }

    public function toEmbeddedHtml(): string
    {
        $statePath = $this->getStatePath();
        $offColor = $this->getOffColor() ?? 'gray';
        $offIcon = $this->getOffIcon();
        $onColor = $this->getOnColor() ?? 'primary';
        $onIcon = $this->getOnIcon();

        $stateExpression = '$wire.' . $this->applyStateBindingModifiers("\$entangle('{$statePath}')");

        $toggleAttributes = \Filament\Support\prepare_inherited_attributes(
            (new ComponentAttributeBag)
                ->merge([
                    'aria-checked' => 'false',
                    'autofocus' => $this->isAutofocused(),
                    'disabled' => $this->isDisabled(),
                    'id' => $this->getId(),
                    'wire:loading.attr' => 'disabled',
                    'wire:target' => $statePath,
                ], escape: false)
                ->merge($this->getExtraAttributes(), escape: false)
                ->merge($this->getExtraAlpineAttributes(), escape: false)
                ->class(['fi-fo-toggle'])
        );

        $onClasses = Js::from(Arr::toCssClasses([
            'fi-toggle-on',
            ...get_component_color_classes(ToggleComponent::class, $onColor),
        ]));

        $offClasses = Js::from(Arr::toCssClasses([
            'fi-toggle-off',
            ...get_component_color_classes(ToggleComponent::class, $offColor),
        ]));

        $offIconHtml = generate_icon_html($offIcon, size: IconSize::ExtraSmall)?->toHtml();
        $onIconHtml = generate_icon_html($onIcon, size: IconSize::ExtraSmall)?->toHtml();

        ob_start(); ?>

        <button
            x-data="{ state: <?= $stateExpression ?> }"
            x-bind:aria-checked="state?.toString()"
            x-on:click="state = ! state"
            x-bind:class="state ? <?= $onClasses ?> : <?= $offClasses ?>"
            <?php if ($stateExpression) { ?>x-cloak<?php } ?>
            role="switch"
            type="button"
            <?= $toggleAttributes->class(['fi-toggle'])->toHtml() ?>
        >
            <div>
                <div aria-hidden="true"><?= $offIconHtml ?></div>
                <div aria-hidden="true"><?= $onIconHtml ?></div>
            </div>
        </button>

        <?php if ($stateExpression) { ?>
            <div
                x-cloak="inline-flex"
                wire:ignore
                <?= (new ComponentAttributeBag)->class([
                    'fi-toggle fi-toggle-on fi-hidden',
                    ...get_component_color_classes(ToggleComponent::class, $onColor),
                ])->toHtml() ?>
            >
                <div>
                    <div aria-hidden="true"></div>
                    <div aria-hidden="true"><?= $onIconHtml ?></div>
                </div>
            </div>
        <?php } ?>

        <?php $toggleHtml = ob_get_clean();

        if ($this->isInline()) {
            return $this->wrapEmbeddedHtml(
                '',
                labelPrefix: $toggleHtml,
                inlineLabelVerticalAlignment: VerticalAlignment::Center,
            );
        }

        return $this->wrapEmbeddedHtml(
            $toggleHtml,
            inlineLabelVerticalAlignment: VerticalAlignment::Center,
        );
    }

    /**
     * @return array<StateCast>
     */
    public function getDefaultStateCasts(): array
    {
        return [
            ...parent::getDefaultStateCasts(),
            app(BooleanStateCast::class, ['isNullable' => false]),
        ];
    }
}
