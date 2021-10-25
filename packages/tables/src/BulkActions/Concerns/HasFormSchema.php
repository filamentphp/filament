<?php

namespace Filament\Tables\BulkActions\Concerns;

trait HasFormSchema
{
    protected ?array $formSchema = null;

    public function form(array $schema): static
    {
        $this->formSchema = $schema;

        return $this;
    }

    public function getFormSchema(): ?array
    {
        return $this->formSchema;
    }
}
