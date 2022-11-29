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

    /**
     * @param  array<string>  $buttonsToDisable
     */
    public function disableToolbarButtons(array $buttonsToDisable = []): static
    {
        $this->toolbarButtons = array_filter(
            $this->getToolbarButtons(),
            static fn ($button) => ! in_array($button, $buttonsToDisable),
        );

        return $this;
    }

    /**
     * @param  array<string>  $buttonsToEnable
     */
    public function enableToolbarButtons(array $buttonsToEnable = []): static
    {
        $this->toolbarButtons = array_merge($this->getToolbarButtons(), $buttonsToEnable);

        return $this;
    }

    /**
     * @param  array<string> | Closure  $buttons
     */
    public function toolbarButtons(array | Closure $buttons = []): static
    {
        $this->toolbarButtons = $buttons;

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getToolbarButtons(): array
    {
        return $this->evaluate($this->toolbarButtons);
    }

    /**
     * @param  string | array<string>  $button
     */
    public function hasToolbarButton(string | array $button): bool
    {
        if (is_array($button)) {
            $buttons = $button;

            return (bool) count(array_intersect($buttons, $this->getToolbarButtons()));
        }

        return in_array($button, $this->getToolbarButtons());
    }
}
