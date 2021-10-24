<?php

namespace Filament\Tables\Filters\Concerns;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Component;

trait HasFormSchema
{
    protected ?array $formSchema = null;

    public function form(array $schema): static
    {
        $this->formSchema = $schema;

        return $this;
    }

    public function getFormSchema(): array
    {
        return $this->formSchema ?? [
            Checkbox::make('isActive')
                ->label($this->getLabel())
                ->default(false),
        ];
    }
}
