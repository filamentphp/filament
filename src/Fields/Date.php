<?php

namespace Filament\Fields;

use Filament\FieldConcerns;

class Date extends InputField
{
    use FieldConcerns\CanBeAutofocused;
    use FieldConcerns\CanBeCompared;
    use FieldConcerns\CanBeDateConstrained;
    use FieldConcerns\CanBeDisabled;
    use FieldConcerns\CanBeUnique;
    use FieldConcerns\HasDateFormats;
    use FieldConcerns\HasPlaceholder;

    public function __construct($name)
    {
        parent::__construct($name);

        $this->displayFormat('F j, Y');
        $this->format('Y-m-d');
    }
}
