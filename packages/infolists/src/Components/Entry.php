<?php

namespace Filament\Infolists\Components;

use Illuminate\Contracts\Support\Htmlable;

class Entry extends Component
{
    use Concerns\CanOpenUrl;
    use Concerns\HasAlignment;
    use Concerns\HasHelperText;
    use Concerns\HasHint;
    use Concerns\HasName;
    use Concerns\HasTooltip;

    protected string $viewIdentifier = 'entry';

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

    public function getId(): string
    {
        return parent::getId() ?? $this->getStatePath();
    }

    public function getLabel(): string | Htmlable | null
    {
        $label = parent::getLabel() ?? (string) str($this->getName())
            ->before('.')
            ->kebab()
            ->replace(['-', '_'], ' ')
            ->ucfirst();

        return (is_string($label) && $this->shouldTranslateLabel) ?
            __($label) :
            $label;
    }
}
