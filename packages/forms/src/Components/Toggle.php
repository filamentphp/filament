<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Support\Concerns\HasExtraAlpineAttributes;

class Toggle extends Field
{
    use Concerns\CanBeAccepted;
    use Concerns\CanBeInline;
    use HasExtraAlpineAttributes;

    protected string $view = 'forms::components.toggle';

    protected string | Closure | null $offIcon = null;

    protected string | Closure | null $onIcon = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->default(false);

        $this->afterStateHydrated(static function (Toggle $component, $state): void {
            $component->state((bool) $state);
        });

        $this->rule('boolean');
    }

    public function offIcon(string | Closure | null $icon): static
    {
        $this->offIcon = $icon;

        return $this;
    }

    public function onIcon(string | Closure | null $icon): static
    {
        $this->onIcon = $icon;

        return $this;
    }

    public function getOffIcon(): ?string
    {
        return $this->evaluate($this->offIcon);
    }

    public function getOnIcon(): ?string
    {
        return $this->evaluate($this->onIcon);
    }

    public function hasOffIcon(): bool
    {
        return (bool) $this->getOffIcon();
    }

    public function hasOnIcon(): bool
    {
        return (bool) $this->getOnIcon();
    }
}
