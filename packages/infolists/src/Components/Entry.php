<?php

namespace Filament\Infolists\Components;

use Exception;
use Filament\Schema\Components\Component;
use Filament\Schema\Components\Contracts\HasHintActions;
use Filament\Support\Concerns\HasAlignment;
use Filament\Support\Concerns\HasPlaceholder;
use Illuminate\Contracts\Support\Htmlable;

class Entry extends Component implements HasHintActions
{
    use Concerns\CanOpenUrl;
    use Concerns\HasExtraEntryWrapperAttributes;
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

    public static function make(?string $name = null): static
    {
        $entryClass = static::class;

        $name ??= static::getDefaultName();

        if (blank($name)) {
            throw new Exception("Entry of class [$entryClass] must have a unique name, passed to the [make()] method.");
        }

        $static = app($entryClass, ['name' => $name]);
        $static->configure();

        return $static;
    }

    public static function getDefaultName(): ?string
    {
        return null;
    }

    public function getState(): mixed
    {
        return $this->getConstantState();
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

    public function state(mixed $state): static
    {
        $this->constantState($state);

        return $this;
    }
}
