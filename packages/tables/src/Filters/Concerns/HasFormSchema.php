<?php

namespace Filament\Tables\Filters\Concerns;

use Closure;
use Filament\Forms\Components\Field;

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
        $schema = $this->evaluate($this->formSchema);

        if ($schema !== null) {
            return $schema;
        }

        $field = $this->getFormField();

        if ($field === null) {
            return [];
        }

        return [$field];
    }

    protected function getFormField(): ?Field
    {
        return null;
    }
}
