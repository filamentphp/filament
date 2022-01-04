<?php

namespace Filament\Tables\Actions\Concerns;

use Closure;

trait HasFormSchema
{
    protected array | Closure $formSchema = [];

    public function form(array | Closure $schema): static
    {
        $this->formSchema = $schema;

        return $this;
    }

    public function getFormSchema(): array
    {
        return $this->evaluate($this->formSchema);
    }

    public function hasFormSchema(): bool
    {
        return (bool) count($this->getFormSchema());
    }
}
