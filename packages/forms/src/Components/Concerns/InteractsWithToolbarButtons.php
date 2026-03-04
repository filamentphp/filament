<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Exception;
use Filament\Forms\Components\RichEditor\ToolbarButtonGroup;
use LogicException;

trait InteractsWithToolbarButtons
{
    /**
     * @var array<int, string | ToolbarButtonGroup | array<int, string | ToolbarButtonGroup>> | Closure | null
     */
    protected array | Closure | null $toolbarButtons = null;

    /**
     * @var array<array{type: string, buttons?: array<string>}>
     */
    protected array $toolbarButtonsModifications = [];

    /**
     * @var array<int, string | ToolbarButtonGroup | array<int, string | ToolbarButtonGroup>> | null
     */
    protected ?array $cachedModifiedToolbarButtons = null;

    public function disableAllToolbarButtons(bool $condition = true): static
    {
        if ($condition) {
            $this->toolbarButtonsModifications[] = ['type' => 'disableAll'];
            $this->cachedModifiedToolbarButtons = null;
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

        $this->cachedModifiedToolbarButtons = null;

        return $this;
    }

    /**
     * @param  array<string | ToolbarButtonGroup | array<string | ToolbarButtonGroup>>  $buttonsToEnable
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

        $this->cachedModifiedToolbarButtons = null;

        return $this;
    }

    /**
     * @param  array<int, string | ToolbarButtonGroup | array<int, string | ToolbarButtonGroup>> | Closure | null  $buttons
     */
    public function toolbarButtons(array | Closure | null $buttons): static
    {
        $this->toolbarButtons = $buttons;
        $this->toolbarButtonsModifications = [];
        $this->cachedModifiedToolbarButtons = null;

        return $this;
    }

    /**
     * Apply all queued modifications to the toolbar buttons and return the cached result.
     *
     * @return array<int, string | ToolbarButtonGroup | array<int, string | ToolbarButtonGroup>>
     */
    protected function getModifiedToolbarButtons(): array
    {
        if ($this->cachedModifiedToolbarButtons !== null) {
            return $this->cachedModifiedToolbarButtons;
        }

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

        return $this->cachedModifiedToolbarButtons = $buttons;
    }

    /**
     * @return array<array<string>>
     */
    public function getToolbarButtons(): array
    {
        $buttons = $this->getModifiedToolbarButtons();

        // Group the buttons, replacing `ToolbarButtonGroup` instances with synthetic names
        $toolbar = [];
        $newButtonGroup = [];

        foreach ($buttons as $buttonGroup) {
            if (blank($buttonGroup)) {
                continue;
            }

            if ($buttonGroup instanceof ToolbarButtonGroup) {
                $newButtonGroup[] = $buttonGroup->getSyntheticName();

                continue;
            }

            if (! is_array($buttonGroup)) {
                $newButtonGroup[] = $buttonGroup;

                continue;
            }

            // Process group array, replacing `ToolbarButtonGroup` instances within groups
            $processedGroup = [];

            foreach ($buttonGroup as $item) {
                if ($item instanceof ToolbarButtonGroup) {
                    $processedGroup[] = $item->getSyntheticName();
                } else {
                    $processedGroup[] = $item;
                }
            }

            if (filled($newButtonGroup)) {
                $toolbar[] = $newButtonGroup;
                $newButtonGroup = [];
            }

            if (filled($processedGroup)) {
                $toolbar[] = $processedGroup;
            }
        }

        if (filled($newButtonGroup)) {
            $toolbar[] = $newButtonGroup;
        }

        return $toolbar;
    }

    /**
     * Resolve all `ToolbarButtonGroup` instances from the (modified) toolbar buttons.
     *
     * @return array<string, ToolbarButtonGroup>
     */
    public function resolveToolbarButtonGroups(): array
    {
        $groups = [];

        foreach ($this->getModifiedToolbarButtons() as $buttonGroup) {
            if ($buttonGroup instanceof ToolbarButtonGroup) {
                $groups[$buttonGroup->getSyntheticName()] = $buttonGroup;

                continue;
            }

            if (is_array($buttonGroup)) {
                foreach ($buttonGroup as $item) {
                    if ($item instanceof ToolbarButtonGroup) {
                        $groups[$item->getSyntheticName()] = $item;
                    }
                }
            }
        }

        return $groups;
    }

    /**
     * @param  array<int, string | ToolbarButtonGroup | array<int, string | ToolbarButtonGroup>>  $buttons
     * @param  array<string>  $buttonsToDisable
     * @return array<int, string | ToolbarButtonGroup | array<int, string | ToolbarButtonGroup>>
     */
    protected function applyDisableToolbarButtonsModification(array $buttons, array $buttonsToDisable): array
    {
        $modified = [];

        foreach ($buttons as $button) {
            // Handle `ToolbarButtonGroup` at top level
            if ($button instanceof ToolbarButtonGroup) {
                $filtered = $this->filterToolbarButtonGroup($button, $buttonsToDisable);

                if ($filtered !== null) {
                    $modified[] = $filtered;
                }

                continue;
            }

            if (is_array($button)) {
                $filteredGroup = [];

                foreach ($button as $item) {
                    // Handle `ToolbarButtonGroup` inside a group
                    if ($item instanceof ToolbarButtonGroup) {
                        $filtered = $this->filterToolbarButtonGroup($item, $buttonsToDisable);

                        if ($filtered !== null) {
                            $filteredGroup[] = $filtered;
                        }

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
     * Filter a `ToolbarButtonGroup` by removing disabled buttons.
     *
     * Returns `null` if the group should be removed entirely, a plain `string` if
     * only one option remains, or a cloned `ToolbarButtonGroup` with filtered buttons.
     *
     * @param  array<string>  $buttonsToDisable
     */
    protected function filterToolbarButtonGroup(ToolbarButtonGroup $group, array $buttonsToDisable): ToolbarButtonGroup | string | null
    {
        // If the trigger name is disabled, remove the whole group
        if (in_array($group->getName(), $buttonsToDisable)) {
            return null;
        }

        $filteredButtons = array_values(array_filter(
            $group->getButtons(),
            static fn (string $button): bool => ! in_array($button, $buttonsToDisable),
        ));

        if (count($filteredButtons) === 0) {
            return null;
        }

        if (count($filteredButtons) === 1) {
            return $filteredButtons[0];
        }

        $newGroup = clone $group;
        $newGroup->buttons($filteredButtons);

        return $newGroup;
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

        foreach ($this->getModifiedToolbarButtons() as $item) {
            if ($this->toolbarItemMatchesButtons($item, $buttonsToCheck)) {
                return true;
            }

            if (is_array($item)) {
                foreach ($item as $innerItem) {
                    if ($this->toolbarItemMatchesButtons($innerItem, $buttonsToCheck)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Check if a single toolbar item (string or `ToolbarButtonGroup`) matches any of the given button names.
     *
     * @param  string | ToolbarButtonGroup | array<int, string | ToolbarButtonGroup>  $item
     * @param  array<string>  $buttonsToCheck
     */
    protected function toolbarItemMatchesButtons(string | ToolbarButtonGroup | array $item, array $buttonsToCheck): bool
    {
        if ($item instanceof ToolbarButtonGroup) {
            return in_array($item->getName(), $buttonsToCheck)
                || (bool) array_intersect($buttonsToCheck, $item->getButtons());
        }

        if (is_string($item)) {
            return in_array($item, $buttonsToCheck);
        }

        return false;
    }

    public function hasCustomToolbarButtons(): bool
    {
        return $this->evaluate($this->toolbarButtons) !== null;
    }
}
