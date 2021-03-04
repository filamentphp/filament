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

    public $cols;

    public $rows;

    public function cols($cols)
    {
        $this->cols = $cols;

        return $this;
    }

    public function rows($rows)
    {
        $this->rows = $rows;

        return $this;
    }
}
