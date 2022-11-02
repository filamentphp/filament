<?php

namespace Filament\Tables\Filters;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Toggle;

class Filter extends BaseFilter
{
    public string $formComponent = Checkbox::class;

    protected function setUp(): void
    {
        parent::setUp();

        $this->indicateUsing(function (array $state): array {
            if (! ($state['isActive'] ?? false)) {
                return [];
            }

            return [$this->getIndicator()];
        });
    }

    public function toggle(): static
    {
        $this->formComponent(Toggle::class);

        return $this;
    }

    public function checkbox(): static
    {
        $this->formComponent(Checkbox::class);

        return $this;
    }

    public function formComponent(string $component): static
    {
        $this->formComponent = $component;

        return $this;
    }

    protected function getFormField(): Field
    {
        $field = $this->formComponent::make('isActive')
            ->label($this->getLabel());

        if (filled($defaultState = $this->getDefaultState())) {
            $field->default($defaultState);
        }

        return $field;
    }
}
