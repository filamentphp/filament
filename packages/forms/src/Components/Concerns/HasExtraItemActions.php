<?php

namespace Filament\Forms\Components\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Support\Enums\Size;
use Illuminate\Support\Arr;

trait HasExtraItemActions
{
    /**
     * @var array<Action | Closure>
     */
    protected array $extraItemActions = [];

    /**
     * @var array<Action> | null
     */
    protected ?array $cachedExtraItemActions = null;

    /**
     * @param  array<Action | Closure>  $actions
     */
    public function extraItemActions(array $actions): static
    {
        $this->extraItemActions = [
            ...$this->extraItemActions,
            ...$actions,
        ];

        return $this;
    }

    /**
     * @return array<Action>
     */
    public function getExtraItemActions(): array
    {
        return $this->cachedExtraItemActions ?? $this->cacheExtraItemActions();
    }

    /**
     * @return array<Action>
     */
    public function cacheExtraItemActions(): array
    {
        $this->cachedExtraItemActions = [];

        foreach ($this->extraItemActions as $extraItemAction) {
            foreach (Arr::wrap($this->evaluate($extraItemAction)) as $action) {
                $this->cachedExtraItemActions[$action->getName()] = $this->prepareAction(
                    (clone $action)
                        ->defaultColor('gray')
                        ->defaultSize(Size::Small)
                        ->defaultView(Action::ICON_BUTTON_VIEW)
                        ->extraAttributes(fn (array $arguments): array => $this->getItemActionAttributes($arguments), merge: true),
                );
            }
        }

        return $this->cachedExtraItemActions;
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     *
     * @param  array<string, mixed>  $arguments
     * @return array<string, mixed>
     */
    protected function getItemActionAttributes(array $arguments): array
    {
        if (! isset($arguments['item'])) {
            return [];
        }

        return [
            'aria-describedby' => $this->getId() . '-item-' . md5((string) $arguments['item']),
        ];
    }
}
