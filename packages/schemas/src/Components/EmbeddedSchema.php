<?php

namespace Filament\Schemas\Components;

use Closure;
use Filament\Support\Components\Contracts\HasEmbeddedView;

class EmbeddedSchema extends Component implements HasEmbeddedView
{
    protected string | Closure $name;

    final public function __construct(string | Closure $name)
    {
        $this->name($name);
    }

    public static function make(string $name): static
    {
        $static = app(static::class, ['name' => $name]);
        $static->configure();

        return $static;
    }

    public function name(string | Closure $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->evaluate($this->name);
    }

    public function toEmbeddedHtml(): string
    {
        return $this->getLivewire()->getSchema($this->getName())->toHtml();
    }
}
