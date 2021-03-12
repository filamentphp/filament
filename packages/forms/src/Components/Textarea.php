<?php

namespace Filament\Forms\Components;

class Textarea extends Field
{
    use Concerns\CanBeAutocompleted;
    use Concerns\CanBeAutofocused;
    use Concerns\CanBeCompared;
    use Concerns\CanBeUnique;
    use Concerns\CanBeLengthConstrained;
    use Concerns\HasPlaceholder;

    protected $cols;

    protected $rows;

    public function cols($cols)
    {
        $this->configure(function () use ($cols) {
            $this->cols = $cols;
        });

        return $this;
    }

    public function getCols()
    {
        return $this->cols;
    }

    public function getRows()
    {
        return $this->rows;
    }

    public function rows($rows)
    {
        $this->configure(function () use ($rows) {
            $this->rows = $rows;
        });

        return $this;
    }
}
