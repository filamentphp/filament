<?php

namespace Filament\Forms\Components;

use Illuminate\Contracts\Support\Htmlable;

class Placeholder extends Component
{
    use Concerns\HasHelperText;
    use Concerns\HasHint;
    use Concerns\HasName;

    /**
     * @var view-string $view
     */
    protected string $view = 'filament-forms::components.placeholder';

    protected $content = null;

    final public function __construct(string $name)
    {
        $this->name($name);
        $this->statePath($name);
    }

    public static function make(string $name): static
    {
        $static = app(static::class, ['name' => $name]);
        $static->configure();

        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->dehydrated(false);
    }

    public function content($content): static
    {
        $this->content = $content;

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

    public function getLabel(): string | Htmlable | null
    {
        return parent::getLabel() ?? (string) str($this->getName())
            ->kebab()
            ->replace(['-', '_'], ' ')
            ->ucfirst();
    }

    public function getContent()
    {
        return $this->evaluate($this->content);
    }
}
