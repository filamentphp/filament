<?php

namespace Filament\Fields;

use Filament\FieldConcerns;

class Date extends InputField
{
    use FieldConcerns\CanBeAutofocused;
    use FieldConcerns\CanBeCompared;
    use FieldConcerns\CanBeDisabled;
    use FieldConcerns\CanBeUnique;
    use FieldConcerns\HasPlaceholder;

    public $displayFormat = 'MMM D, YYYY';

    public $format = 'YYYY-MM-DD';

    public $max;

    public $min;

    public $time = false;
}
