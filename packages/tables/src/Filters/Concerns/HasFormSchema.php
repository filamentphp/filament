<?php

namespace Filament\Tables\Filters\Concerns;

use Closure;

trait HasFormSchema
{
    protected array | Closure | null $formSchema = null;

    public function form(array | Closure | null $schema): static
    {
        $this->formSchema = $schema;

        return $this;
    }

    public function getFormSchema(): array
    {
        return $this->evaluate($this->formSchema) ?? [];
    }
}
