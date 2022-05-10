<?php

namespace Filament\Tables\Filters;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Toggle;

class Filter extends BaseFilter
{
    public string $filterType = Checkbox::class;

    public function toggle(): static
    {
        $this->filterType = Toggle::class;
        return $this;
    }

    public function checkbox(): static
    {
        $this->filterType = Checkbox::class;
        return $this;
    }

    public function getFormSchema(): array
    {
        return $this->evaluate($this->formSchema) ?? [
            $this->filterType::make('isActive')
                ->label($this->getLabel())
                ->default($this->getDefaultState())
                ->columnSpan($this->getColumnSpan()),
        ];
    }
}
