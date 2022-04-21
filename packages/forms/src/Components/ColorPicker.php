<?php

namespace Filament\Forms\Components;

use Closure;

class ColorPicker extends Field
{
    use Concerns\HasAffixes;
    use Concerns\HasExtraAlpineAttributes;
    use Concerns\HasExtraInputAttributes;
    use Concerns\HasPlaceholder;

    protected string $view = 'forms::components.color-picker';

    protected string | Closure $format = 'hex';

    public function format(string | Closure $format): static
    {
        $this->format = $format;

        return $this;
    }

    public function hex(): static
    {
        $this->format('hex');

        return $this;
    }

    public function hsl(): static
    {
        $this->format('hsl');

        return $this;
    }

    public function rgb(): static
    {
        $this->format('rgb');

        return $this;
    }

    public function rgba(): static
    {
        $this->format('rgba');

        return $this;
    }

    public function getFormat(): string
    {
        return $this->evaluate($this->format);
    }
}
