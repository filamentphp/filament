<?php

namespace Filament\Forms\Components;

class Field extends Component implements Contracts\HasValidationRules
{
    use Concerns\CanBeAutofocused;
    use Concerns\CanBeValidated;
    use Concerns\HasHelperText;
    use Concerns\HasHint;
    use Concerns\HasName;

    protected string $viewIdentifier = 'field';

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
}
