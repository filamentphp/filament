<?php

namespace Filament\Forms\Components\Builder;

use Closure;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Concerns;
use Filament\Support\Concerns\HasIcon;
use Filament\Support\Enums\IconSize;

class BlockGroup extends Component
{
    use Concerns\HasName;
    use HasIcon;

    protected string | Closure | null $description = null;


    final public function __construct(string $name)
    {
        $this->name($name);
    }

    protected function setUp(): void
    {
        $this->iconSize(IconSize::Large);
    }

    public function description(string | Closure | null $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->evaluate($this->description);
    }

    public static function make(string $name): static
    {
        $static = app(static::class, ['name' => $name]);
        $static->configure();

        return $static;
    }
}
