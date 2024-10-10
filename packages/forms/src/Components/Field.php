<?php

namespace Filament\Forms\Components;

class Field extends Component implements Contracts\HasHintActions, Contracts\HasValidationRules
{
    use Concerns\CanBeAutofocused;
    use Concerns\CanBeMarkedAsRequired;
    use Concerns\CanBeValidated;
    use Concerns\CanDisableGrammarly;
    use Concerns\HasExtraFieldWrapperAttributes;
    use Concerns\HasHelperText;
    use Concerns\HasHint;
    use Concerns\HasName;

    protected string $viewIdentifier = 'field';

    final public function __construct(string $name, ?string $label = null)
    {
        $this->name($name);

        if (! is_null($label)) {
            $this->label($label);
        }

        $this->statePath($name);
    }

    public static function make(string $name, ?string $label = null): static
    {
        $static = app(static::class, ['name' => $name, 'label' => $label]);
        $static->configure();

        return $static;
    }

    public function getId(): string
    {
        return parent::getId() ?? $this->getStatePath();
    }

    public function getKey(): string
    {
        return parent::getKey() ?? $this->getStatePath();
    }
}
