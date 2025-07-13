<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Exception;

trait InteractsWithToolbarButtons
{
    /**
     * @var array<string | array<string>> | Closure | null
     */
    protected array | Closure | null $toolbarButtons = null;

    public function disableAllToolbarButtons(bool $condition = true): static
    {
        if ($condition) {
            $this->toolbarButtons = [];
        }

        return $this;
    }

    /**
     * @param  array<string | array<string>>  $buttonsToDisable
     */
    public function disableToolbarButtons(array $buttonsToDisable = []): static
    {
        if ($this->toolbarButtons instanceof Closure) {
            throw new Exception('You cannot use the `disableToolbarButtons()` method when the toolbar buttons are dynamically returned from a function. Instead, do not return the disabled buttons from the function.');
        }

        $this->toolbarButtons = array_reduce(
            $this->toolbarButtons,
            function ($carry, $button) use ($buttonsToDisable) {
                if (is_array($button)) {
                    $carry[] = array_values(array_filter(
                        $button,
                        static fn ($button) => ! in_array($button, $buttonsToDisable),
                    ));
                } elseif (! in_array($button, $buttonsToDisable)) {
                    $carry[] = $button;
                }

                return $carry;
            },
            initial: [],
        );

        return $this;
    }

    /**
     * @param  array<string | array<string | array<string>>>  $buttonsToEnable
     */
    public function enableToolbarButtons(array $buttonsToEnable = []): static
    {
        if ($this->toolbarButtons instanceof Closure) {
            throw new Exception('You cannot use the `enableToolbarButtons()` method when the toolbar buttons are dynamically returned from a function. Instead, return the enabled buttons from the function.');
        }

        $this->toolbarButtons = [
            ...$this->toolbarButtons,
            ...$buttonsToEnable,
        ];

        return $this;
    }

    /**
     * @param  array<string | array<string>> | Closure | null  $buttons
     */
    public function toolbarButtons(array | Closure | null $buttons): static
    {
        $this->toolbarButtons = $buttons;

        return $this;
    }

    /**
     * @return array<array<string>>
     */
    public function getToolbarButtons(): array
    {
        $toolbar = [];
        $newButtonGroup = [];

        foreach ($this->evaluate($this->toolbarButtons) ?? $this->getDefaultToolbarButtons() as $buttonGroup) {
            if (blank($buttonGroup)) {
                continue;
            }

            if (! is_array($buttonGroup)) {
                $newButtonGroup[] = $buttonGroup;

                continue;
            }

            if (filled($newButtonGroup)) {
                $toolbar[] = $newButtonGroup;

                $newButtonGroup = [];

                continue;
            }

            $toolbar[] = $buttonGroup;
        }

        if (filled($newButtonGroup)) {
            $toolbar[] = $newButtonGroup;
        }

        return $toolbar;
    }

    /**
     * @return array<string | array<string>>
     */
    public function getDefaultToolbarButtons(): array
    {
        return [];
    }

    /**
     * @param  string | array<string>  $button
     */
    public function hasToolbarButton(string | array $button): bool
    {
        foreach ($this->getToolbarButtons() as $buttonGroup) {
            if (is_array($button)) {
                foreach ($button as $singleButton) {
                    if (in_array($singleButton, $buttonGroup)) {
                        return true;
                    }
                }
            } elseif (in_array($button, $buttonGroup)) {
                return true;
            }
        }

        return false;
    }
}
