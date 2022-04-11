<?php

namespace Filament\Forms\Components;

use Closure;

class ColorPicker extends Field
{
    use Concerns\HasAffixes;
    use Concerns\HasExtraAlpineAttributes;
    use Concerns\HasExtraInputAttributes;
    use Concerns\HasPlaceholder;
    use Concerns\CanBeInline;

    protected string $view = 'forms::components.color-picker';

    protected string | Closure $format = 'hex';

    protected bool | Closure $preview = true;

    protected function setUp(): void
    {
        parent::setUp();

        $this->inline(false);
    }

    public function format(string | Closure $format): static
    {
        $this->format = $format;

        return $this;
    }

    public function preview(bool | Closure $preview = true): static
    {
        $this->preview = $preview;

        return $this;
    }

    public function hex(): static
    {
        return $this->format('hex');
    }

    public function hsl(): static
    {
        return $this->format('hsl');
    }

    public function rgb(): static
    {
        return $this->format('rgb');
    }

    public function rgba(): static
    {
        return $this->format('rgba');
    }

    public function getFormat(): string
    {
        return $this->evaluate($this->format);
    }

    public function hasPreview(): bool
    {
        return (bool) $this->evaluate($this->preview);
    }
}
