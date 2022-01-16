<?php

namespace Filament\Pages\Actions\Concerns;

trait HasFormSchema
{
    protected array $formSchema = [];

    public function form(array $schema): static
    {
        $this->formSchema = $schema;

        return $this;
    }

    public function getFormSchema(): array
    {
        return $this->formSchema;
    }

    public function hasFormSchema(): bool
    {
        return (bool) count($this->getFormSchema());
    }
}
