<?php

namespace Filament\Forms\Components\Concerns;

use BackedEnum;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Icon;
use Filament\Schemas\Components\Text;
use Illuminate\Contracts\Support\Htmlable;

trait HasHint
{
    protected string | Htmlable | Closure | null $hint = null;

    /**
     * @var array<Action | Closure>
     */
    protected array $hintActions = [];

    /**
     * @var string | array<int | string, string | int> | Closure | null
     */
    protected string | array | Closure | null $hintColor = null;

    protected string | BackedEnum | Closure | null $hintIcon = null;

    protected string | Closure | null $hintIconTooltip = null;

    protected function setUpHint(): void
    {
        $this->afterLabel(function (Field | Placeholder $component): array {
            $components = [];

            $hint = $component->getHint();

            if (filled($hint)) {
                $components[] = Text::make($hint)
                    ->color($component->getHintColor());
            }

            $hintIcon = $component->getHintIcon();

            if (filled($hintIcon)) {
                $components[] = Icon::make($hintIcon)
                    ->tooltip($component->getHintIconTooltip());
            }

            return [
                ...$components,
                ...$component->getHintActions(),
            ];
        });
    }

    public function hint(string | Htmlable | Closure | null $hint): static
    {
        $this->hint = $hint;

        return $this;
    }

    /**
     * @param  string | array<int | string, string | int> | Closure | null  $color
     */
    public function hintColor(string | array | Closure | null $color): static
    {
        $this->hintColor = $color;

        return $this;
    }

    public function hintIcon(string | BackedEnum | Closure | null $icon, string | Closure | null $tooltip = null): static
    {
        $this->hintIcon = $icon;
        $this->hintIconTooltip($tooltip);

        return $this;
    }

    public function hintIconTooltip(string | Closure | null $tooltip): static
    {
        $this->hintIconTooltip = $tooltip;

        return $this;
    }

    public function hintAction(Action | Closure $action): static
    {
        $this->hintActions([$action]);

        return $this;
    }

    /**
     * @param  array<Action | Closure>  $actions
     */
    public function hintActions(array $actions): static
    {
        $this->hintActions = [
            ...$this->hintActions,
            ...$actions,
        ];

        return $this;
    }

    public function getHint(): string | Htmlable | null
    {
        return $this->evaluate($this->hint);
    }

    /**
     * @return string | array<int | string, string | int> | null
     */
    public function getHintColor(): string | array | null
    {
        return $this->evaluate($this->hintColor);
    }

    public function getHintIcon(): string | BackedEnum | null
    {
        return $this->evaluate($this->hintIcon);
    }

    public function getHintIconTooltip(): ?string
    {
        return $this->evaluate($this->hintIconTooltip);
    }

    /**
     * @return array<Action>
     */
    public function getHintActions(): array
    {
        return $this->evaluate($this->hintActions);
    }
}
