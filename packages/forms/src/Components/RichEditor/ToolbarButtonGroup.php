<?php

namespace Filament\Forms\Components\RichEditor;

use Closure;
use Filament\Schemas\Components\Concerns\HasName;
use Filament\Support\Components\Component;

class ToolbarButtonGroup extends Component
{
    use HasName;

    public const SYNTHETIC_PREFIX = 'dropdown::';

    /**
     * @var array<string> | Closure
     */
    protected array | Closure $buttons = [];

    protected bool | Closure $hasTextualButtons = false;

    protected string $evaluationIdentifier = 'toolbarButtonGroup';

    final public function __construct(string $name)
    {
        $this->name($name);
    }

    public static function make(string $name): static
    {
        $static = app(static::class, ['name' => $name]);
        $static->configure();

        return $static;
    }

    public function getSyntheticName(): string
    {
        return static::SYNTHETIC_PREFIX . $this->getName();
    }

    /**
     * @param  array<string> | Closure  $buttons
     */
    public function buttons(array | Closure $buttons): static
    {
        $this->buttons = $buttons;

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getButtons(): array
    {
        return $this->evaluate($this->buttons);
    }

    public function textualButtons(bool | Closure $condition = true): static
    {
        $this->hasTextualButtons = $condition;

        return $this;
    }

    public function hasTextualButtons(): bool
    {
        return (bool) $this->evaluate($this->hasTextualButtons);
    }
}
