<?php

namespace Filament\Schema\Components;

use Closure;
use Filament\Schema\Components\Concerns\CanPersistTab;
use Filament\Schema\Components\Tabs\Tab;
use Filament\Support\Concerns;
use Illuminate\Support\Str;

class Tabs extends Component
{
    use CanPersistTab;
    use Concerns\CanBeContained;
    use Concerns\HasExtraAlpineAttributes;

    /**
     * @var view-string
     */
    protected string $view = 'filament-schema::components.tabs';

    protected int | Closure $activeTab = 1;

    protected string | Closure | null $tabQueryStringKey = null;

    final public function __construct(?string $label = null)
    {
        $this->label($label);
    }

    public static function make(?string $label = null): static
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

            return Str::slug($label);
        });
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
}
