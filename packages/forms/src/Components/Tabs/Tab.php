<?php

namespace Filament\Forms\Components\Tabs;

use Closure;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Contracts\CanConcealComponents;
use Filament\Support\Concerns\HasIcon;
use Illuminate\Support\Str;

class Tab extends Component implements CanConcealComponents
{
    use HasIcon;

    /**
     * @var view-string
     */
    protected string $view = 'filament-forms::components.tabs.tab';

    protected string | Closure | null $badge = null;

    final public function __construct(string $label)
    {
        $this->label($label);
        $this->id(Str::slug($label));
    }

    public static function make(string $label): static
    {
        $static = app(static::class, ['label' => $label]);
        $static->configure();

        return $static;
    }

    public function badge(string | Closure | null $badge): static
    {
        $this->badge = $badge;

        return $this;
    }

    public function getId(): string
    {
        return $this->getContainer()->getParentComponent()->getId() . '-' . parent::getId() . '-tab';
    }

    /**
     * @return array<string, int | null>
     */
    public function getColumnsConfig(): array
    {
        return $this->columns ?? $this->getContainer()->getColumnsConfig();
    }

    public function getBadge(): ?string
    {
        return $this->evaluate($this->badge);
    }

    public function canConcealComponents(): bool
    {
        return true;
    }
}
