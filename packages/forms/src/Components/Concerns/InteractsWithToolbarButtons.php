<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait InteractsWithToolbarButtons
{
    public function disableAllToolbarButtons(bool $condition = true): static
    {
        if ($condition) {
            $this->toolbarButtons = [];
        }

        return $this;
    }

    public function disableToolbarButtons(array $buttonsToDisable = []): static
    {
        $this->toolbarButtons = array_filter(
            $this->getToolbarButtons(),
            static fn ($button) => ! in_array($button, $buttonsToDisable),
        );

        return $this;
    }

    public function enableToolbarButtons(array $buttonsToEnable = []): static
    {
        $this->toolbarButtons = array_merge($this->getToolbarButtons(), $buttonsToEnable);

        return $this;
    }

    public function toolbarButtons(array | Closure $buttons = []): static
    {
        $this->toolbarButtons = $buttons;

        return $this;
    }

    public function getToolbarButtons(): array
    {
        return $this->evaluate($this->toolbarButtons);
    }

    public function hasToolbarButton(string | array $button): bool
    {
        if (is_array($button)) {
            $buttons = $button;

            return (bool) count(array_intersect($buttons, $this->getToolbarButtons()));
        }

        return in_array($button, $this->getToolbarButtons());
    }
}
