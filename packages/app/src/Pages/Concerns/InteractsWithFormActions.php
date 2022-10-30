<?php

namespace Filament\Pages\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;

trait InteractsWithFormActions
{
    protected array $cachedFormActions = [];

    public static string $formActionsAlignment = 'left';

    public function bootedInteractsWithFormActions(): void
    {
        $this->cacheFormActions();
    }

    protected function cacheFormActions(): void
    {
        $actions = Action::configureUsing(
            Closure::fromCallable([$this, 'configureAction']),
            fn (): array => $this->getFormActions(),
        );

        foreach ($actions as $index => $action) {
            if ($action instanceof ActionGroup) {
                foreach ($action->getActions() as $groupedAction) {
                    $groupedAction->livewire($this);

                    $this->cachedFormActions[$groupedAction->getName()] = $groupedAction;
                }

                $this->cachedActions[$index] = $action;

                continue;
            }

            $this->cacheAction($action);
            $this->cachedFormActions[$action->getName()] = $action;
        }
    }

    public function getCachedFormActions(): array
    {
        return $this->cachedFormActions;
    }

    public function getFormActions(): array
    {
        return [];
    }

    protected function hasFullWidthFormActions(): bool
    {
        return false;
    }

    public static function alignFormActionsLeft(): void
    {
        static::$formActionsAlignment = 'left';
    }

    public static function alignFormActionsCenter(): void
    {
        static::$formActionsAlignment = 'center';
    }

    public static function alignFormActionsRight(): void
    {
        static::$formActionsAlignment = 'right';
    }

    public static function getFormActionsAlignment(): string
    {
        return static::$formActionsAlignment;
    }
}
