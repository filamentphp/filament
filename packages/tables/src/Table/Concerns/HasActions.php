<?php

namespace Filament\Tables\Table\Concerns;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;

trait HasActions
{
    /**
     * @var array<string, Action>
     */
    protected array $flatActions = [];

    /**
     * @var array<string, BulkAction>
     */
    protected array $flatBulkActions = [];

    public function getAction(string $name): ?Action
    {
        return $this->getFlatActions()[$name] ?? null;
    }

    public function getBulkAction(string $name): ?BulkAction
    {
        return $this->getFlatBulkActions()[$name] ?? null;
    }

    /**
     * @return array<string, Action>
     */
    public function getFlatActions(): array
    {
        return $this->flatActions;
    }

    /**
     * @return array<string, BulkAction>
     */
    public function getFlatBulkActions(): array
    {
        return $this->flatBulkActions;
    }

    public function hasAction(string $name): bool
    {
        return array_key_exists($name, $this->getFlatActions());
    }

    public function hasBulkAction(string $name): bool
    {
        return array_key_exists($name, $this->getFlatBulkActions());
    }

    protected function cacheAction(Action $action, bool $shouldOverwriteExistingAction = true): void
    {
        if (! $shouldOverwriteExistingAction) {
            if ($action instanceof BulkAction) {
                $this->flatBulkActions[$action->getName()] ??= $action;
            } else {
                $this->flatActions[$action->getName()] ??= $action;
            }

            return;
        }

        if ($action instanceof BulkAction) {
            $this->flatBulkActions[$action->getName()] = $action;

            return;
        }

        $this->flatActions[$action->getName()] = $action;
    }

    /**
     * @param  array<string, Action>  $actions
     */
    protected function mergeCachedFlatActions(array $actions, bool $shouldOverwriteExistingActions = true): void
    {
        foreach ($actions as $action) {
            $this->cacheAction($action, $shouldOverwriteExistingActions);
        }
    }

    /**
     * @param  array<Action>  $actions
     */
    protected function removeCachedActions(array $actions): void
    {
        $this->flatActions = array_filter(
            $this->flatActions,
            fn (Action $existingAction): bool => ! in_array($existingAction, $actions, true),
        );

        $this->flatBulkActions = array_filter(
            $this->flatBulkActions,
            fn (Action $existingAction): bool => ! in_array($existingAction, $actions, true),
        );
    }
}
