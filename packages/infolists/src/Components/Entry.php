<?php

namespace Filament\Infolists\Components;

use Filament\Infolists\Components\Contracts\HasHintActions;
use Filament\Support\Concerns\HasAlignment;
use Filament\Support\Concerns\HasPlaceholder;
use Illuminate\Contracts\Support\Htmlable;

class Entry extends Component implements HasHintActions
{
    use Concerns\CanOpenUrl;
    use Concerns\HasHelperText;
    use Concerns\HasHint;
    use Concerns\HasName;
    use Concerns\HasTooltip;
    use HasAlignment;
    use HasPlaceholder;

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

    public function getKey(): string
    {
        return parent::getKey() ?? $this->getStatePath();
    }
}
