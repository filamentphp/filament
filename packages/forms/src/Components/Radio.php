<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Schemas\Components\StateCasts\BooleanStateCast;
use Filament\Schemas\Components\StateCasts\Contracts\StateCast;
use Filament\Schemas\Components\StateCasts\OptionStateCast;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Enums\GridDirection;

class Radio extends Field implements Contracts\CanDisableOptions, HasEmbeddedView
{
    use Concerns\CanDisableOptions;
    use Concerns\CanDisableOptionsWhenSelectedInSiblingRepeaterItems;
    use Concerns\CanFixIndistinctState;
    use Concerns\HasDescriptions;
    use Concerns\HasExtraInputAttributes;
    use Concerns\HasGridDirection;
    use Concerns\HasOptions;

    protected ?string $publishedViewOverrideCheckPath = 'filament-forms::components.radio';

    protected bool | Closure $isInline = false;

    public function boolean(?string $trueLabel = null, ?string $falseLabel = null): static
    {
        $this->options([
            1 => $trueLabel ?? __('filament-forms::components.radio.boolean.true'),
            0 => $falseLabel ?? __('filament-forms::components.radio.boolean.false'),
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

    public function toEmbeddedHtml(): string
    {
        $extraInputAttributeBag = $this->getExtraInputAttributeBag();
        $gridDirection = $this->getGridDirection() ?? GridDirection::Column;
        $id = $this->getId();
        $isDisabled = $this->isDisabled();
        $isInline = $this->isInline();
        $statePath = $this->getStatePath();
        $wireModelAttribute = $this->applyStateBindingModifiers('wire:model');
        $isAutofocused = $this->isAutofocused();
        $hasError = $this->hasErrorForPath($statePath);

        $containerAttributes = $this->getExtraAttributeBag();

        if (! $isInline) {
            $containerAttributes = $containerAttributes->grid($this->getColumns(), $gridDirection);
        }

        $containerAttributes = $containerAttributes
            ->merge([
                'aria-labelledby' => "{$id}-label",
                'role' => 'radiogroup',
            ], escape: false)
            ->class([
                'fi-fo-radio',
                'fi-inline' => $isInline,
            ]);

        ob_start(); ?>

        <div <?= $containerAttributes->toHtml() ?>>
            <?php $first = true; ?>
            <?php foreach ($this->getOptions() as $value => $label) { ?>
                <?php
                    $inputAttributes = $extraInputAttributeBag
                        ->merge([
                            'autofocus' => $first && $isAutofocused,
                            'disabled' => $isDisabled || $this->isOptionDisabled($value, $label),
                            'id' => e($id . '-' . $value),
                            'name' => $id,
                            'value' => e($value),
                            $wireModelAttribute => $statePath,
                        ], escape: false)
                        ->class([
                            'fi-radio-input',
                            'fi-valid' => ! $hasError,
                            'fi-invalid' => $hasError,
                        ]);
                $first = false;
                ?>

                <label class="fi-fo-radio-label">
                    <input type="radio" <?= $inputAttributes->toHtml() ?> />

                    <div class="fi-fo-radio-label-text">
                        <p><?= e($label) ?></p>

                        <?php if ($this->hasDescription($value)) { ?>
                            <p class="fi-fo-radio-label-description">
                                <?= e($this->getDescription($value)) ?>
                            </p>
                        <?php } ?>
                    </div>
                </label>
            <?php } ?>
        </div>

        <?php return $this->wrapEmbeddedHtml(ob_get_clean(), labelTag: 'div');
    }

    public function getDefaultState(): mixed
    {
        $state = parent::getDefaultState();

        if (is_bool($state)) {
            return $state ? 1 : 0;
        }

        return $state;
    }

    /**
     * @return array<StateCast>
     */
    public function getDefaultStateCasts(): array
    {
        if ($this->hasCustomStateCasts() || filled($this->getEnum())) {
            return parent::getDefaultStateCasts();
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

    public function hasNullableBooleanState(): bool
    {
        return true;
    }
}
