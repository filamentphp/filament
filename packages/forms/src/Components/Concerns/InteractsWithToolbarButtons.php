<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Exception;
use LogicException;

trait InteractsWithToolbarButtons
{
    /**
     * @var array<int, string | object | array<int, string | object>> | Closure | null
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
     * @param  array<string | object | array<string | object>>  $buttonsToEnable
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
     * @param  array<int, string | object | array<int, string | object>> | Closure | null  $buttons
     */
    public function toolbarButtons(array | Closure | null $buttons): static
    {
        $this->toolbarButtons = $buttons;
        $this->toolbarButtonsModifications = [];

        return $this;
    }

    /**
     * @return array<array<string | object>>
     */
    public function getToolbarButtons(): array
    {
        $buttons = $this->evaluate($this->toolbarButtons) ?? $this->getDefaultToolbarButtons(); /** @phpstan-ignore method.notFound */

        // Extra modifications (e.g. from plugins) are applied first,
        // so that user-level modifications always take precedence.
        $modifications = [...$this->getExtraToolbarButtonsModifications(), ...$this->toolbarButtonsModifications];

        foreach ($modifications as $modification) {
            $buttons = match ($modification['type']) {
                'disableAll' => [],
                'disable' => $this->applyDisableToolbarButtonsModification($buttons, $modification['buttons']),
                'enable' => [...$buttons, ...$modification['buttons']],
                default => throw new Exception('Unknown toolbar buttons modification type: [' . $modification['type'] . '].'),
            };
        }

        // Group consecutive non-array items together; arrays become their own groups
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
            }

            if (filled($buttonGroup)) {
                $toolbar[] = $buttonGroup;
            }
        }

        if (filled($newButtonGroup)) {
            $toolbar[] = $newButtonGroup;
        }

        return $toolbar;
    }

    /**
     * @param  array<int, string | object | array<int, string | object>>  $buttons
     * @param  array<string>  $buttonsToDisable
     * @return array<int, string | object | array<int, string | object>>
     */
    protected function applyDisableToolbarButtonsModification(array $buttons, array $buttonsToDisable): array
    {
        $modified = [];

        foreach ($buttons as $button) {
            if (is_object($button)) {
                $modified[] = $button;

                continue;
            }

            if (is_array($button)) {
                $filteredGroup = [];

                foreach ($button as $item) {
                    if (is_object($item)) {
                        $filteredGroup[] = $item;

                        continue;
                    }

                    if (! in_array($item, $buttonsToDisable)) {
                        $filteredGroup[] = $item;
                    }
                }

                if (filled($filteredGroup)) {
                    $modified[] = $filteredGroup;
                }

                continue;
            }

            if (! in_array($button, $buttonsToDisable)) {
                $modified[] = $button;
            }
        }

        return $modified;
    }

    /**
     * @return array<array{type: string, buttons?: array<string | array<string | array<string>>>}>
     */
    protected function getExtraToolbarButtonsModifications(): array
    {
        return [];
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
        $buttonsToCheck = is_array($button) ? $button : [$button];

        foreach ($this->getToolbarButtons() as $buttonGroup) {
            foreach ($buttonGroup as $item) {
                if (is_string($item) && in_array($item, $buttonsToCheck)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function hasCustomToolbarButtons(): bool
    {
        return $this->evaluate($this->toolbarButtons) !== null;
    }
}
