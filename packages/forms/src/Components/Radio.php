<?php

namespace Filament\Forms\Components;

use Closure;

class Radio extends Field implements Contracts\CanDisableOptions
{
    use Concerns\CanDisableOptions;
    use Concerns\CanDisableOptionsWhenSelectedInSiblingRepeaterItems;
    use Concerns\CanFixIndistinctState;
    use Concerns\HasColors;
    use Concerns\HasDescriptions;
    use Concerns\HasExtraInputAttributes;
    use Concerns\HasGridDirection;
    use Concerns\HasIcons;
    use Concerns\HasOptions;

    public const RADIOS_VIEW = 'filament-forms::components.radio.index';

    public const BUTTONS_VIEW = 'filament-forms::components.radio.buttons';

    public const BUTTON_GROUP_VIEW = 'filament-forms::components.radio.button-group';

    /**
     * @var view-string
     */
    protected string $view = self::RADIOS_VIEW;

    protected bool | Closure $isInline = false;

    protected bool | Closure | null $isOptionDisabled = null;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function radios(): static
    {
        return $this->view(static::RADIOS_VIEW);
    }

    public function isRadios(): bool
    {
        return $this->getView() === static::RADIOS_VIEW;
    }

    public function buttons(): static
    {
        return $this->view(static::BUTTONS_VIEW);
    }

    public function isButtons(): bool
    {
        return $this->getView() === static::BUTTONS_VIEW;
    }

    public function buttonGroup(): static
    {
        return $this->view(static::BUTTON_GROUP_VIEW);
    }

    public function isButtonGroup(): bool
    {
        return $this->getView() === static::BUTTON_GROUP_VIEW;
    }

    public function boolean(?string $trueLabel = null, ?string $falseLabel = null): static
    {
        $this->options([
            1 => $trueLabel ?? __('filament-forms::components.radio.boolean.true'),
            0 => $falseLabel ?? __('filament-forms::components.radio.boolean.false'),
        ]);

        return $this;
    }

    public function inline(bool | Closure $condition = true): static
    {
        $this->isInline = $condition;
        $this->inlineLabel(fn (Radio $component): ?bool => $component->evaluate($condition) ? true : null);

        return $this;
    }

    public function isInline(): bool
    {
        return (bool) $this->evaluate($this->isInline);
    }

    public function getDefaultState(): mixed
    {
        $state = parent::getDefaultState();

        if (is_bool($state)) {
            return $state ? 1 : 0;
        }

        return $state;
    }
}
