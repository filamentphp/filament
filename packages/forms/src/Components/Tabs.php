<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Support\Concerns\HasExtraAlpineAttributes;

class Tabs extends Component
{
    use HasExtraAlpineAttributes;

    /**
     * @var view-string
     */
    protected string $view = 'filament-forms::components.tabs';

    public int | Closure $activeTab = 1;

    final public function __construct(string $label)
    {
        $this->label($label);
    }

    public static function make(string $label): static
    {
        $static = app(static::class, ['label' => $label]);
        $static->configure();

        return $static;
    }

    /**
     * @param array<Tab> $tabs
     */
    public function tabs(array $tabs): static
    {
        $this->childComponents($tabs);

        return $this;
    }

    public function activeTab(int | Closure $activeTab): static
    {
        $this->activeTab = $activeTab;

        return $this;
    }

    public function getActiveTab(): int
    {
        return $this->evaluate($this->activeTab);
    }
}
