<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Forms\View\FormsIconAlias;
use Filament\Schemas\Components\StateCasts\BooleanStateCast;
use Filament\Schemas\Components\StateCasts\Contracts\StateCast;
use Filament\Schemas\Components\StateCasts\EnumArrayStateCast;
use Filament\Schemas\Components\StateCasts\EnumStateCast;
use Filament\Schemas\Components\StateCasts\OptionsArrayStateCast;
use Filament\Schemas\Components\StateCasts\OptionStateCast;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Enums\GridDirection;
use Filament\Support\Enums\Size;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Filament\Support\View\Components\ButtonComponent;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Js;
use Illuminate\View\ComponentAttributeBag;

use function Filament\Support\generate_icon_html;

class ToggleButtons extends Field implements Contracts\CanDisableOptions, HasEmbeddedView
{
    use Concerns\CanDisableOptions;
    use Concerns\CanDisableOptionsWhenSelectedInSiblingRepeaterItems;
    use Concerns\CanFixIndistinctState;
    use Concerns\HasColors;
    use Concerns\HasExtraInputAttributes;
    use Concerns\HasGridDirection;
    use Concerns\HasIcons;
    use Concerns\HasNestedRecursiveValidationRules;
    use Concerns\HasOptions;
    use Concerns\HasTooltips;

    public const GROUPED_VIEW = 'filament-forms::components.toggle-buttons.grouped';

    protected bool | Closure $isMultiple = false;

    protected bool | Closure $isInline = false;

    protected bool | Closure $areButtonLabelsHidden = false;

    public function grouped(): static
    {
        return $this->view(static::GROUPED_VIEW);
    }

    public function toEmbeddedHtml(): string
    {
        $gridDirection = $this->getGridDirection() ?? GridDirection::Column;
        $id = $this->getId();
        $isDisabled = $this->isDisabled();
        $isInline = $this->isInline();
        $isMultiple = $this->isMultiple();
        $statePath = $this->getStatePath();
        $areButtonLabelsHidden = $this->areButtonLabelsHidden();
        $wireModelAttribute = $this->applyStateBindingModifiers('wire:model');
        $extraInputAttributeBag = $this->getExtraInputAttributeBag()->class(['fi-fo-toggle-buttons-input']);
        $isAutofocused = $this->isAutofocused();

        $containerAttributes = $this->getExtraAttributeBag();

        if (! $isInline) {
            $containerAttributes = $containerAttributes->grid($this->getColumns(), $gridDirection);
        }

        $containerAttributes = $containerAttributes->class([
            'fi-fo-toggle-buttons',
            'fi-inline' => $isInline,
        ]);

        $first = true;

        ob_start(); ?>

        <div <?= $containerAttributes->toHtml() ?>>
            <?php foreach ($this->getOptions() as $value => $label) { ?>
                <?php
                    $inputId = "{$id}-{$value}";
                    $shouldOptionBeDisabled = $isDisabled || $this->isOptionDisabled($value, $label);
                    $color = $this->getColor($value);
                    $icon = $this->getIcon($value);
                    $tooltip = $this->getTooltip($value);

                    // Simplified button rendering for label tags
                    $buttonAttributes = (new ComponentAttributeBag)
                        ->merge([
                            'aria-disabled' => $shouldOptionBeDisabled ? 'true' : null,
                            'aria-label' => $areButtonLabelsHidden ? trim(strip_tags($label)) : null,
                            'disabled' => $shouldOptionBeDisabled,
                            'for' => $inputId,
                        ], escape: false)
                        ->class([
                            'fi-btn',
                            'fi-size-md',
                            'fi-disabled' => $shouldOptionBeDisabled,
                        ])
                        ->color(app(ButtonComponent::class, ['isOutlined' => false]), $color);
                ?>

                <div class="fi-fo-toggle-buttons-btn-ctn">
                    <input
                        <?php if ($first && $isAutofocused) { ?> autofocus <?php } ?>
                        <?php if ($shouldOptionBeDisabled) { ?> disabled <?php } ?>
                        id="<?= e($inputId) ?>"
                        <?php if (! $isMultiple) { ?>
                            name="<?= e($id) ?>"
                        <?php } ?>
                        type="<?= $isMultiple ? 'checkbox' : 'radio' ?>"
                        value="<?= e($value) ?>"
                        <?= $wireModelAttribute ?>="<?= e($statePath) ?>"
                        <?= $extraInputAttributeBag->toHtml() ?>
                    />

                    <label
                        <?php if (filled($tooltip)) { ?>
                            x-tooltip="{ content: <?= Js::from($tooltip) ?>, theme: $store.theme, allowHTML: <?= Js::from($tooltip instanceof Htmlable) ?> }"
                        <?php } ?>
                        <?= $buttonAttributes->toHtml() ?>
                    >
                        <?php if (filled($icon)) { ?>
                            <?= generate_icon_html($icon)?->toHtml() ?>
                        <?php } ?>

                        <?php if (! $areButtonLabelsHidden) { ?>
                            <?= e($label) ?>
                        <?php } ?>
                    </label>
                </div>
                <?php $first = false; ?>
            <?php } ?>
        </div>

        <?php return $this->wrapEmbeddedHtml(ob_get_clean(), extraWrapperAttributes: ['class' => 'fi-fo-toggle-buttons-wrp', 'tabindex' => '-1']);
    }

    public function boolean(?string $trueLabel = null, ?string $falseLabel = null): static
    {
        $this->options([
            1 => $trueLabel ?? __('filament-forms::components.toggle_buttons.boolean.true'),
            0 => $falseLabel ?? __('filament-forms::components.toggle_buttons.boolean.false'),
        ]);

        $this->colors([
            1 => 'success',
            0 => 'danger',
        ]);

        $this->icons([
            1 => FilamentIcon::resolve(FormsIconAlias::COMPONENTS_TOGGLE_BUTTONS_BOOLEAN_TRUE) ?? Heroicon::Check,
            0 => FilamentIcon::resolve(FormsIconAlias::COMPONENTS_TOGGLE_BUTTONS_BOOLEAN_FALSE) ?? Heroicon::XMark,
        ]);

        $this->stateCast(app(BooleanStateCast::class, ['isStoredAsInt' => true]));

        return $this;
    }

    public function inline(bool | Closure $condition = true): static
    {
        $this->isInline = $condition;

        return $this;
    }

    public function isInline(): bool
    {
        return (bool) $this->evaluate($this->isInline);
    }

    public function hiddenButtonLabels(bool | Closure $condition = true): static
    {
        $this->areButtonLabelsHidden = $condition;

        return $this;
    }

    public function areButtonLabelsHidden(): bool
    {
        return (bool) $this->evaluate($this->areButtonLabelsHidden);
    }

    public function multiple(bool | Closure $condition = true): static
    {
        $this->isMultiple = $condition;

        return $this;
    }

    public function isMultiple(): bool
    {
        return (bool) $this->evaluate($this->isMultiple);
    }

    public function getDefaultState(): mixed
    {
        $state = parent::getDefaultState();

        if (is_bool($state)) {
            return $state ? 1 : 0;
        }

        return $state;
    }

    public function getEnumDefaultStateCast(): ?StateCast
    {
        $enum = $this->getEnum();

        if (blank($enum)) {
            return null;
        }

        return app(
            $this->isMultiple() ? EnumArrayStateCast::class : EnumStateCast::class,
            ['enum' => $enum],
        );
    }

    /**
     * @return array<StateCast>
     */
    public function getDefaultStateCasts(): array
    {
        if ($this->hasCustomStateCasts() || filled($this->getEnum())) {
            return parent::getDefaultStateCasts();
        }

        if ($this->isMultiple()) {
            return [app(OptionsArrayStateCast::class)];
        }

        return [app(OptionStateCast::class, ['isNullable' => true])];
    }

    /**
     * @return ?array<string>
     */
    public function getInValidationRuleValues(): ?array
    {
        $values = parent::getInValidationRuleValues();

        if ($values !== null) {
            return $values;
        }

        return array_keys($this->getEnabledOptions());
    }

    public function hasInValidationOnMultipleValues(): bool
    {
        return $this->isMultiple();
    }

    public function hasNullableBooleanState(): bool
    {
        return true;
    }
}
