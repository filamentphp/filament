<?php

namespace Filament\Fields;

use Filament\Traits\FieldConcerns;

class Trix extends InputField
{
    use FieldConcerns\CanBeAutofocused;
    use FieldConcerns\CanBeCompared;
    use FieldConcerns\CanBeDisabled;
    use FieldConcerns\CanBeUnique;
    use FieldConcerns\CanHavePlaceholder;
}
