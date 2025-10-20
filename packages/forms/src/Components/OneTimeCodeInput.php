<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Support\Concerns\HasExtraAlpineAttributes;

class OneTimeCodeInput extends Field
{
    use Concerns\CanBeReadOnly;
    use Concerns\HasExtraInputAttributes;
    use Concerns\HasPlaceholder;
    use HasExtraAlpineAttributes;

    /**
     * @var view-string
     */
    protected string $view = 'filament-forms::components.one-time-code-input';

    protected int | Closure $length = 6;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rule('numeric'); // Integer validation does not allow leading zeros.
        $this->rule(static fn (OneTimeCodeInput $component): string => "digits:{$component->getLength()}");
    }

    public function length(int | Closure $length): static
    {
        $this->length = $length;

        return $this;
    }

    public function getLength(): int
    {
        return $this->evaluate($this->length);
    }
}
