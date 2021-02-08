<?php

namespace Filament\Fields;

use Filament\FieldConcerns;

class Textarea extends InputField
{
    use FieldConcerns\CanBeAutocompleted;
    use FieldConcerns\CanBeAutofocused;
    use FieldConcerns\CanBeCompared;
    use FieldConcerns\CanBeDisabled;
    use FieldConcerns\CanBeUnique;
    use FieldConcerns\CanBeLengthConstrained;
    use FieldConcerns\HasPlaceholder;
}
