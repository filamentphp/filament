<?php

namespace Filament\Forms\Components;

use Illuminate\Contracts\Support\Htmlable;

class Placeholder extends Component implements Contracts\HasHintActions
{
    use Concerns\HasHelperText;
    use Concerns\HasHint;
    use Concerns\HasName;

    /**
     * @var view-string
     */
    protected string $view = 'filament-forms::components.placeholder';

    protected mixed $content = null;

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

    public function content(mixed $content): static
    {
        $this->content = $content;

        return $this;
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

    public function getContent(): mixed
    {
        return $this->evaluate(
            $this->content,
            exceptParameters: ['state'],
        );
    }
}
