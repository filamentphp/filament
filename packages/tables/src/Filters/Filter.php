<?php

namespace Filament\Tables\Filters;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Toggle;

class Filter extends BaseFilter
{
    public string $formComponent = Checkbox::class;

    protected function setUp(): void
    {
        parent::setUp();

        $this->checkbox();

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

        $this->default(state: false);

        return $this;
    }

    public function checkbox(): static
    {
        $this->formComponent(Checkbox::class);

        $this->default(state: false);

        return $this;
    }

    public function formComponent(string $component): static
    {
        $this->formComponent = $component;

        $this->default(state: null);

        return $this;
    }

    public function getFormSchema(): array
    {
        return $this->evaluate($this->formSchema) ?? [
            $this->formComponent::make('isActive')
                ->label($this->getLabel())
                ->default($this->getDefaultState())
                ->columnSpan($this->getColumnSpan()),
        ];
    }
}
