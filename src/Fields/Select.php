<?php

namespace Filament\Fields;

use Filament\FieldConcerns;

class Select extends InputField
{
    use FieldConcerns\CanBeAutofocused;
    use FieldConcerns\CanBeCompared;
    use FieldConcerns\CanBeDisabled;
    use FieldConcerns\CanBeUnique;
    use FieldConcerns\CanBeLengthConstrained;
    use FieldConcerns\HasPlaceholder;

    public $emptyOptionLabel = 'None';

    public $emptyOptionsMessage = 'No options match your search.';

    public $options = [];

    public function emptyOptionLabel($label)
    {
        $this->emptyOptionLabel = $label;

        return $this;
    }

    public function emptyOptionsMessage($message)
    {
        $this->emptyOptionsMessage = $message;

        return $this;
    }

    public function options($options)
    {
        $this->options = $options;

        return $this;
    }
}
