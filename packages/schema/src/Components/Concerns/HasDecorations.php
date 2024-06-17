<?php

namespace Filament\Schema\Components\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Schema\Components\Decorations\Decoration;
use Filament\Schema\Components\Decorations\Layouts\AlignDecorations;
use Filament\Schema\Components\Decorations\Layouts\Layout;
use Filament\Schema\Components\Decorations\TextDecoration;
use Filament\Support\Enums\ActionSize;
use Illuminate\Support\Arr;

trait HasDecorations
{
    /**
     * @var array<string, array{array<Decoration | Action> | Layout | Decoration | Action | string | Closure, class-string<Layout>}>
     */
    protected array $decorations = [];

    /**
     * @var array<string, Layout> | null
     */
    protected ?array $cachedDecorations = null;

    /**
     * @var array<Action> | null
     */
    protected ?array $cachedDecorationActions = null;

    /**
     * @param  array<Decoration | Action> | Layout | Decoration | Action | string | Closure | null  $decorations
     */
    public function decorations(string $position, array | Layout | Decoration | Action | string | Closure | null $decorations, ?Closure $makeDefaultLayoutUsing = null): static
    {
        if (blank($decorations)) {
            unset($this->decorations[$position]);

            return $this;
        }

        $this->decorations[$position] = [$decorations, $makeDefaultLayoutUsing];

        return $this;
    }

    public function getDecorations(string $position): ?Layout
    {
        return ($this->cachedDecorations ?? $this->cacheDecorations())[$position] ?? null;
    }

    /**
     * @return array<Layout>
     */
    public function cacheDecorations(): array
    {
        $this->cachedDecorations = [];

        foreach ($this->decorations as $position => [$decorations, $makeDefaultLayout]) {
            $decorations = $this->evaluate($decorations);

            if ($decorations instanceof Layout && $decorations->hasDecorations()) {
                $this->cachedDecorations[$position] = $decorations;

                continue;
            }

            if ($decorations instanceof Layout) {
                continue;
            }

            if (is_string($decorations)) {
                $decorations = [TextDecoration::make($decorations)];
            }

            $decorations = Arr::wrap($decorations);

            if (blank($decorations)) {
                continue;
            }

            if (is_array($decorations)) {
                $decorations = $makeDefaultLayout ?
                    $makeDefaultLayout($decorations) :
                    AlignDecorations::start($decorations);
            }

            if ($decorations->hasDecorations()) {
                $this->cachedDecorations[$position] = $decorations;

                continue;
            }

            $this->cachedDecorations[$position] = $decorations;
        }

        $this->cacheDecorationActions();

        return $this->cachedDecorations;
    }

    /**
     * @return array<Action>
     */
    public function getDecorationActions(): array
    {
        return $this->cachedDecorationActions ?? $this->cacheDecorationActions();
    }

    /**
     * @return array<Action>
     */
    public function cacheDecorationActions(): array
    {
        $this->cachedDecorationActions = [];

        foreach (array_keys($this->decorations) as $position) {
            $decorations = $this->getDecorations($position);

            if (! $decorations) {
                continue;
            }

            foreach ($decorations->getActions() as $action) {
                $this->cachedDecorationActions[$action->getName()] = $this->prepareAction(
                    $action
                        ->defaultSize(ActionSize::Small)
                        ->defaultView(Action::LINK_VIEW),
                );
            }
        }

        return $this->cachedDecorationActions;
    }
}
