<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Support\Concerns;
use Illuminate\Support\Facades\Session;

class Tabs extends Component
{
    use Concerns\CanBeContained;
    use Concerns\HasExtraAlpineAttributes;

    /**
     * @var view-string
     */
    protected string $view = 'filament-forms::components.tabs';

    protected int | Closure $activeTab = 1;

    protected string | Closure | null $tabQueryStringKey = null;

    protected bool $persitTabInSession = false;

    final public function __construct(?string $label = null)
    {
        $this->label($label);
    }

    protected function setUp(): void
    {
        $this->registerListeners([
            'tab::selectTab' => [
                function (Tabs $component, string $statePath, int $tab): void {
                    if ($statePath !== $component->getStatePath()) {
                        return;
                    }
                    Session::put($this->getTabSessionName(), $tab);
                },
            ],
        ]);
    }

    public static function make(?string $label = null): static
    {
        $static = app(static::class, ['label' => $label]);
        $static->configure();

        return $static;
    }

    /**
     * @param  array<Tab> | Closure  $tabs
     */
    public function tabs(array | Closure $tabs): static
    {
        $this->childComponents($tabs);

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

    public function persistTabInSession(): static
    {
        $this->persitTabInSession = true;

        return $this;
    }

    public function getActiveTab(): int
    {
        if ($this->isTabPersistedInQueryString()) {
            $queryStringTab = request()->query($this->getTabQueryStringKey());

            foreach ($this->getChildComponentContainer()->getComponents() as $index => $tab) {
                if ($tab->getId() !== $queryStringTab) {
                    continue;
                }

                return $index + 1;
            }
        }

        if ($this->isTabPersistedInSession()) {
            return Session::get($this->getTabSessionName()) + 1;
        }

        return $this->evaluate($this->activeTab);
    }

    protected function getTabSessionName(): string
    {
        return str_slug(class_basename($this->getLivewire()).'-'.$this->getModelInstance()->getTable().'-'.$this->getModelInstance()->getKey().'-activeTab');
    }

    public function getTabQueryStringKey(): ?string
    {
        return $this->evaluate($this->tabQueryStringKey);
    }

    public function isTabPersistedInQueryString(): bool
    {
        return filled($this->getTabQueryStringKey());
    }

    public function isTabPersistedInSession(): bool
    {
        return $this->persitTabInSession && Session::has($this->getTabSessionName());
    }
}
