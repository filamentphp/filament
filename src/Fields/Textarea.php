<?php

namespace Filament\Fields;

class Textarea extends InputField
{
    use FieldConcerns\CanBeCompared;
    use FieldConcerns\CanBeDisabled;
    use FieldConcerns\CanBeUnique;
    use FieldConcerns\CanHaveLengthConstraints;
    use FieldConcerns\CanHavePlaceholder;
}
