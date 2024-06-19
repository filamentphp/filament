<?php

namespace Filament\Schema\Components\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Schema\Components\Section;
use Illuminate\Support\Arr;

trait HasHeaderActions
{
    /**
     * @var array<Action> | null
     */
    protected ?array $cachedHeaderActions = null;

    /**
     * @var array<Action | Closure>
     */
    protected array $headerActions = [];

    protected function setUpHeaderActions(): void
    {
        $this->afterHeader(fn (Section $component): array => $component->getHeaderActions());
    }

    /**
     * @param  array<Action | Closure>  $actions
     */
    public function headerActions(array $actions): static
    {
        $this->headerActions = [
            ...$this->headerActions,
            ...$actions,
        ];

        return $this;
    }

    /**
     * @return array<Action>
     */
    public function getHeaderActions(): array
    {
        return $this->cachedHeaderActions ?? $this->cacheHeaderActions();
    }

    /**
     * @return array<Action>
     */
    public function cacheHeaderActions(): array
    {
        $this->cachedHeaderActions = [];

        foreach ($this->headerActions as $headerAction) {
            foreach (Arr::wrap($this->evaluate($headerAction)) as $action) {
                $this->cachedHeaderActions[$action->getName()] = $this->prepareAction($action);
            }
        }

        return $this->cachedHeaderActions;
    }
}
