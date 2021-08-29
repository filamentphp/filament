<?php

namespace Filament\Forms\Components;

class Textarea extends Field
{
    use Concerns\CanBeAutocompleted;
    use Concerns\CanBeLengthConstrained;
    use Concerns\HasPlaceholder;

    protected string $view = 'forms::components.textarea';

    protected $cols = null;

    protected $rows = null;

    public function cols(int | callable $cols): static
    {
        $this->cols = $cols;

        return $this;
    }

    public function rows(int | callable $rows): static
    {
        $this->rows = $rows;

        return $this;
    }

    public function getCols(): ?int
    {
        return $this->evaluate($this->cols);
    }

    public function getRows(): ?int
    {
        return $this->evaluate($this->rows);
    }
}
