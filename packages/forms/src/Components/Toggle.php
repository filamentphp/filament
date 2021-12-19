<?php

namespace Filament\Forms\Components;

class Toggle extends Field
{
    use Concerns\CanBeAccepted;
    use Concerns\CanBeInline;
    use Concerns\HasExtraAlpineAttributes;

    protected string $view = 'forms::components.toggle';

    protected $offIcon = null;

    protected $onIcon = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->default(false);

        $this->rule('boolean');
    }

    public function offIcon(string | callable $icon): static
    {
        $this->offIcon = $icon;

        return $this;
    }

    public function onIcon(string | callable $icon): static
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
