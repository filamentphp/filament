<?php

namespace Filament\Forms\Components;

use Exception;

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

    public static function make(?string $name = null): static
    {
        $placeholderClass = static::class;

        $name ??= static::getDefaultName();

        if (blank($name)) {
            throw new Exception("Placeholder of class [$placeholderClass] must have a unique name, passed to the [make()] method.");
        }

        $static = app($placeholderClass, ['name' => $name]);
        $static->configure();

        return $static;
    }

    public static function getDefaultName(): ?string
    {
        return null;
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

    public function getContent(): mixed
    {
        return $this->evaluate($this->content);
    }
}
