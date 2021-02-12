<?php

namespace Filament\Fields;

use Filament\Fields\Concerns;

class Date extends InputField
{
    use Concerns\CanBeAutofocused;
    use Concerns\CanBeCompared;
    use Concerns\CanBeDateConstrained;
    use Concerns\CanBeDisabled;
    use Concerns\CanBeUnique;
    use Concerns\HasDateFormats;
    use Concerns\HasPlaceholder;

    public function __construct($name)
    {
        parent::__construct($name);

        $this->displayFormat('F j, Y');
        $this->format('Y-m-d');
    }
}
