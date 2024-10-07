<?php

namespace Filament\Infolists\Components\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Infolists\Components\Entry;
use Filament\Schema\Components\Decorations\IconDecoration;
use Filament\Schema\Components\Decorations\TextDecoration;
use Illuminate\Contracts\Support\Htmlable;

trait HasHint
{
    protected string | Htmlable | Closure | null $hint = null;

    /**
     * @var array<Action | Closure>
     */
    protected array $hintActions = [];

    /**
     * @var string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | Closure | null
     */
    protected string | array | Closure | null $hintColor = null;

    protected string | Closure | null $hintIcon = null;

    protected string | Closure | null $hintIconTooltip = null;

    protected function setUpHint(): void
    {
        $this->afterLabel(function (Entry $component): array {
            $decorations = [];

            $hint = $component->getHint();

            if (filled($hint)) {
                $decorations[] = TextDecoration::make($hint)
                    ->color($component->getHintColor());
            }

            $hintIcon = $component->getHintIcon();

            if (filled($hintIcon)) {
                $decorations[] = IconDecoration::make($hintIcon)
                    ->tooltip($component->getHintIconTooltip());
            }

            return [
                ...$decorations,
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
     * @param  string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | Closure | null  $color
     */
    public function hintColor(string | array | Closure | null $color): static
    {
        $this->hintColor = $color;

        return $this;
    }

    public function hintIcon(string | Closure | null $icon, string | Closure | null $tooltip = null): static
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
     * @return string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null
     */
    public function getHintColor(): string | array | null
    {
        return $this->evaluate($this->hintColor);
    }

    public function getHintIcon(): ?string
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
