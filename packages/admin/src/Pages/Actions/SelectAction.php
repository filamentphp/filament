<?php

namespace Filament\Pages\Actions;

class SelectAction extends Action
{
    use Concerns\HasId;

    protected string $view = 'filament::components.actions.select-action';

    protected array $options = [];

    protected ?string $placeholder = null;

    public function options(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function placeholder(string $placeholder): static
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getPlaceholder(): ?string
    {
        return $this->placeholder;
    }
}
