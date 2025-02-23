<?php

namespace Filament\Forms\Components\Builder;

use BackedEnum;
use Closure;
use Exception;
use Filament\Forms\Components\Concerns;
use Filament\Schemas\Components\Component;
use Illuminate\Contracts\Support\Htmlable;

class Block extends Component
{
    use Concerns\HasName {
        getLabel as getDefaultLabel;
    }
    use Concerns\HasPreview;

    protected string | BackedEnum | Closure | null $icon = null;

    protected int | Closure | null $maxItems = null;

    final public function __construct(string $name)
    {
        $this->name($name);
    }

    public static function make(?string $name = null): static
    {
        $blockClass = static::class;

        $name ??= static::getDefaultName();

        if (blank($name)) {
            throw new Exception("Block of class [$blockClass] must have a unique name, passed to the [make()] method.");
        }

        $static = app($blockClass, ['name' => $name]);
        $static->configure();

        return $static;
    }

    public static function getDefaultName(): ?string
    {
        return null;
    }

    public function icon(string | BackedEnum | Closure | null $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function getIcon(): string | BackedEnum
    {
        return $this->evaluate($this->icon);
    }

    public function maxItems(int | Closure | null $maxItems): static
    {
        $this->maxItems = $maxItems;

        return $this;
    }

    public function getMaxItems(): ?int
    {
        return $this->evaluate($this->maxItems);
    }

    /**
     * @param  array<string, mixed> | null  $state
     */
    public function getLabel(?array $state = null, ?string $uuid = null): string | Htmlable
    {
        return $this->evaluate(
            $this->label,
            ['state' => $state, 'uuid' => $uuid],
        ) ?? $this->getDefaultLabel();
    }
}
