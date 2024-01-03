<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Support\Facades\FilamentIcon;

class ToggleButtons extends Field implements Contracts\CanDisableOptions
{
    use Concerns\CanDisableOptions;
    use Concerns\CanDisableOptionsWhenSelectedInSiblingRepeaterItems;
    use Concerns\CanFixIndistinctState;
    use Concerns\HasColors;
    use Concerns\HasExtraInputAttributes;
    use Concerns\HasGridDirection;
    use Concerns\HasIcons;
    use Concerns\HasOptions;

    public const GROUPED_VIEW = 'filament-forms::components.toggle-buttons.grouped';

    /**
     * @var view-string
     */
    protected string $view = 'filament-forms::components.toggle-buttons.index';

    protected bool | Closure $isInline = false;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function grouped(): static
    {
        return $this->view(static::GROUPED_VIEW);
    }

    public function isGrouped(): bool
    {
        return $this->getView() === static::GROUPED_VIEW;
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
            1 => FilamentIcon::resolve('forms::components.toggle-buttons.true') ?? 'heroicon-m-check',
            0 => FilamentIcon::resolve('forms::components.toggle-buttons.false') ?? 'heroicon-m-x-mark',
        ]);

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

    public function getDefaultState(): mixed
    {
        $state = parent::getDefaultState();

        if (is_bool($state)) {
            return $state ? 1 : 0;
        }

        return $state;
    }
}
