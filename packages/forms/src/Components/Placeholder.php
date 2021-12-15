<?php

namespace Filament\Forms\Components;

use Illuminate\Support\Str;

class Placeholder extends Component
{
    use Concerns\HasHelperText;
    use Concerns\HasHint;
    use Concerns\HasName;

    protected string $view = 'forms::components.placeholder';

    protected $state = null;

    final public function __construct(string $name)
    {
        $this->name($name);
        $this->statePath($name);
    }

    public static function make(string $name): static
    {
        $static = new static($name);
        $static->setUp();

        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->dehydrated(false);
    }

    public function state($state): static
    {
        $this->state = $state;

        return $this;
    }

    protected function shouldEvaluateWithState(): bool
    {
        return false;
    }

    public function getId(): string
    {
        return parent::getId() ?? $this->getStatePath();
    }

    public function getLabel(): string
    {
        return parent::getLabel() ?? (string) Str::of($this->getName())
            ->kebab()
            ->replace(['-', '_'], ' ')
            ->ucfirst();
    }

    public function getState()
    {
        return $this->evaluate($this->state);
    }
}
