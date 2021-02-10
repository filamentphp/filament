<?php

namespace Filament\Fields;

use Filament\FieldConcerns;

class Text extends InputField
{
    use FieldConcerns\CanBeAutocompleted;
    use FieldConcerns\CanBeAutofocused;
    use FieldConcerns\CanBeCompared;
    use FieldConcerns\CanBeDisabled;
    use FieldConcerns\CanBeUnique;
    use FieldConcerns\CanBeLengthConstrained;
    use FieldConcerns\HasPlaceholder;

    public $type = 'text';

    public function email()
    {
        $this->type('email');

        $this->addRules([$this->name => 'email']);

        return $this;
    }

    public function tel()
    {
        $this->type('tel');

        return $this;
    }

    public function type($type)
    {
        $this->type = $type;

        return $this;
    }

    public function password()
    {
        $this->type('password');

        return $this;
    }
}
