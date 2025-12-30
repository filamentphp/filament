<?php

namespace Filament\Forms\Components\Builder;

use BackedEnum;
use Closure;
use Filament\Forms\Components\Concerns;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Concerns\HasLabel;
use Filament\Schemas\Components\Concerns\HasName;
use Illuminate\Contracts\Support\Htmlable;
use InvalidArgumentException;

class Block extends Component
{
    use Concerns\HasPreview;
    use HasLabel {
        getLabel as getBaseLabel;
    }
    use HasName;

    protected string | BackedEnum | Htmlable | Closure | null $icon = null;

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
            throw new InvalidArgumentException("Block of class [$blockClass] must have a unique name, passed to the [make()] method.");
        }

        $static = app($blockClass, ['name' => $name]);
        $static->configure();

        return $static;
    }

    public static function getDefaultName(): ?string
    {
        return null;
    }

    public function icon(string | BackedEnum | Htmlable | Closure | null $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function getIcon(): string | BackedEnum | Htmlable | null
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
    public function getLabel(?array $state = null, ?string $key = null): string | Htmlable
    {
        $label = $this->evaluate(
            $this->label,
            ['key' => $key, 'state' => $state, 'uuid' => $key],
        );

        if (blank($label) && filled($label = $this->getBaseLabel())) {
            return $label;
        }

        if (blank($label)) {
            $label = (string) str($this->getName())
                ->afterLast('.')
                ->kebab()
                ->replace(['-', '_'], ' ')
                ->ucfirst();
        }

        return (is_string($label) && $this->shouldTranslateLabel) ?
            __($label) :
            $label;
    }
}
