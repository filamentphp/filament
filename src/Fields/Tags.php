<?php

namespace Filament\Fields;

use Filament\FieldConcerns;

class Tags extends InputField
{
    use FieldConcerns\CanBeAutofocused;
    use FieldConcerns\CanBeCompared;
    use FieldConcerns\CanBeDisabled;
    use FieldConcerns\CanBeUnique;
    use FieldConcerns\CanBeLengthConstrained;
    use FieldConcerns\HasPlaceholder;

    public $separator = ',';

    public function __construct($name)
    {
        parent::__construct($name);

        $this->placeholder = 'New tag';
    }

    public function separator($separator)
    {
        $this->separator = $separator;

        return $this;
    }
}
