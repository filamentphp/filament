<?php

namespace Filament\Schemas\Components;

use Closure;
use Filament\Schemas\Components\Concerns\CanPersistTab;
use Filament\Schemas\Components\Concerns\HasLabel;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Contracts\HasRenderHookScopes;
use Filament\Support\Concerns;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class Tabs extends Component
{
    use CanPersistTab;
    use Concerns\CanBeContained;
    use Concerns\HasExtraAlpineAttributes;
    use HasLabel;

    /**
     * @var view-string
     */
    protected string $view = 'filament-schemas::components.tabs';

    protected int | Closure $activeTab = 1;

    protected string | Closure | null $tabQueryStringKey = null;

    /**
     * @var array<string>
     */
    protected array $startRenderHooks = [];

    /**
     * @var array<string>
     */
    protected array $endRenderHooks = [];

    protected string | Closure | null $livewireProperty = null;

    protected bool | Closure $isScrollable = true;

    protected bool | Closure $isVertical = false;

    final public function __construct(string | Htmlable | Closure | null $label = null)
    {
        $this->label($label);
    }

    public static function make(string | Htmlable | Closure | null $label = null): static
    {
        $static = app(static::class, ['label' => $label]);
        $static->configure();

        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->key(function (Tabs $component): ?string {
            $label = $component->getLabel();

            if (blank($label)) {
                return null;
            }

            $statePath = $component->getStatePath();

            return Str::slug(Str::transliterate($label, strict: true)) . '::' . (filled($statePath) ? "{$statePath}::tabs" : 'tabs');
        }, isInheritable: false);
    }

    /**
     * @param  array<Tab> | Closure  $tabs
     */
    public function tabs(array | Closure $tabs): static
    {
        $this->components($tabs);

        return $this;
    }

    public function activeTab(int | Closure $activeTab): static
    {
        $this->activeTab = $activeTab;

        return $this;
    }

    public function persistTabInQueryString(string | Closure | null $key = 'tab'): static
    {
        $this->tabQueryStringKey = $key;

        return $this;
    }

    public function getActiveTab(): int
    {
        if ($this->isTabPersistedInQueryString()) {
            $queryStringTab = request()->query($this->getTabQueryStringKey());

            foreach ($this->getChildSchema()->getComponents() as $index => $tab) {
                if ($tab->getId() !== $queryStringTab) {
                    continue;
                }

                return $index + 1;
            }
        }

        return $this->evaluate($this->activeTab);
    }

    public function getTabQueryStringKey(): ?string
    {
        return $this->evaluate($this->tabQueryStringKey);
    }

    public function isTabPersistedInQueryString(): bool
    {
        return filled($this->getTabQueryStringKey());
    }

    /**
     * @param  array<string>  $hooks
     */
    public function startRenderHooks(array $hooks): static
    {
        $this->startRenderHooks = $hooks;

        return $this;
    }

    /**
     * @param  array<string>  $hooks
     */
    public function endRenderHooks(array $hooks): static
    {
        $this->endRenderHooks = $hooks;

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getStartRenderHooks(): array
    {
        return $this->startRenderHooks;
    }

    /**
     * @return array<string>
     */
    public function getEndRenderHooks(): array
    {
        return $this->endRenderHooks;
    }

    /**
     * @return array<string>
     */
    public function getRenderHookScopes(): array
    {
        $livewire = $this->getLivewire();

        if (! ($livewire instanceof HasRenderHookScopes)) {
            return [];
        }

        return $livewire->getRenderHookScopes();
    }

    public function livewireProperty(string | Closure | null $property): static
    {
        $this->livewireProperty = $property;

        return $this;
    }

    public function getLivewireProperty(): ?string
    {
        return $this->evaluate($this->livewireProperty);
    }

    public function scrollable(bool | Closure $condition = true): static
    {
        $this->isScrollable = $condition;

        return $this;
    }

    public function isScrollable(): bool
    {
        return (bool) $this->evaluate($this->isScrollable);
    }

    public function vertical(bool | Closure $condition = true): static
    {
        $this->isVertical = $condition;

        return $this;
    }

    public function isVertical(): bool
    {
        return (bool) $this->evaluate($this->isVertical);
    }
}
