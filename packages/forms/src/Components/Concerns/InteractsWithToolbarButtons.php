<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Exception;
use LogicException;

trait InteractsWithToolbarButtons
{
    /**
     * @var array<string | array<string>> | Closure | null
     */
    protected array | Closure | null $toolbarButtons = null;

    /**
     * @var array<array{type: string, buttons?: array<string>}>
     */
    protected array $toolbarButtonsModifications = [];

    public function disableAllToolbarButtons(bool $condition = true): static
    {
        if ($condition) {
            $this->toolbarButtonsModifications[] = ['type' => 'disableAll'];
        }

        return $this;
    }

    /**
     * @param  array<string | array<string>>  $buttonsToDisable
     */
    public function disableToolbarButtons(array $buttonsToDisable = []): static
    {
        if ($this->toolbarButtons instanceof Closure) {
            throw new LogicException('You cannot use the `disableToolbarButtons()` method when the toolbar buttons are dynamically returned from a function. Instead, do not return the disabled buttons from the function.');
        }

        $this->toolbarButtonsModifications[] = [
            'type' => 'disable',
            'buttons' => $buttonsToDisable,
        ];

        return $this;
    }

    /**
     * @param  array<string | array<string | array<string>>>  $buttonsToEnable
     */
    public function enableToolbarButtons(array $buttonsToEnable = []): static
    {
        if ($this->toolbarButtons instanceof Closure) {
            throw new LogicException('You cannot use the `enableToolbarButtons()` method when the toolbar buttons are dynamically returned from a function. Instead, return the enabled buttons from the function.');
        }

        $this->toolbarButtonsModifications[] = [
            'type' => 'enable',
            'buttons' => $buttonsToEnable,
        ];

        return $this;
    }

    /**
     * @param  array<string | array<string>> | Closure | null  $buttons
     */
    public function toolbarButtons(array | Closure | null $buttons): static
    {
        $this->toolbarButtons = $buttons;
        $this->toolbarButtonsModifications = [];

        return $this;
    }

    /**
     * @return array<array<string>>
     */
    public function getToolbarButtons(): array
    {
        // Start with either custom buttons or default buttons
        $buttons = $this->evaluate($this->toolbarButtons) ?? $this->getDefaultToolbarButtons(); /** @phpstan-ignore method.notFound */

        // Apply all queued modifications in order
        foreach ($this->toolbarButtonsModifications as $modification) {
            $buttons = match ($modification['type']) {
                'disableAll' => [],
                'disable' => $this->applyDisableToolbarButtonsModification($buttons, $modification['buttons']),
                'enable' => [...$buttons, ...$modification['buttons']],
                default => throw new Exception('Unknown toolbar buttons modification type: [' . $modification['type'] . '].'),
            };
        }

        // Now group the buttons
        $toolbar = [];
        $newButtonGroup = [];

        foreach ($buttons as $buttonGroup) {
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
     * @param  array<string | array<string>>  $buttons
     * @param  array<string>  $buttonsToDisable
     * @return array<string | array<string>>
     */
    protected function applyDisableToolbarButtonsModification(array $buttons, array $buttonsToDisable): array
    {
        return array_reduce(
            $buttons,
            function ($carry, $button) use ($buttonsToDisable) {
                if (is_array($button)) {
                    $filtered = array_values(array_filter(
                        $button,
                        static fn ($button) => ! in_array($button, $buttonsToDisable),
                    ));

                    if (filled($filtered)) {
                        $carry[] = $filtered;
                    }
                } elseif (! in_array($button, $buttonsToDisable)) {
                    $carry[] = $button;
                }

                return $carry;
            },
            initial: [],
        );
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

    public function hasCustomToolbarButtons(): bool
    {
        return $this->evaluate($this->toolbarButtons) !== null;
    }
}
