<?php

namespace Filament\Forms\Components;

use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class Field extends Component implements Contracts\HasValidationRules
{
    use Concerns\CanBeAutofocused;
    use Concerns\CanBeValidated;
    use Concerns\HasHelperText;
    use Concerns\HasHint;
    use Concerns\HasName;

    final public function __construct(string $name)
    {
        $this->name($name);
        $this->statePath($name);
    }

    public static function make(string $name): static
    {
        $static = app(static::class, ['name' => $name]);
        $static->setUp();

        return $static;
    }

    public function getId(): string
    {
        return parent::getId() ?? $this->getStatePath();
    }

    public function getLabel(): string | HtmlString | null
    {
        return parent::getLabel() ?? (string) Str::of($this->getName())
            ->afterLast('.')
            ->kebab()
            ->replace(['-', '_'], ' ')
            ->ucfirst();
    }

    protected function hasDefaultState(): bool
    {
        return true;
    }
}
