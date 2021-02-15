<?php

namespace Filament\Forms\Fields;

use Filament\Forms\Fields\Concerns;

class Text extends InputField
{
    use Concerns\CanBeAutocompleted;
    use Concerns\CanBeAutofocused;
    use Concerns\CanBeCompared;
    use Concerns\CanBeDisabled;
    use Concerns\CanBeUnique;
    use Concerns\CanBeLengthConstrained;
    use Concerns\HasPlaceholder;

    public $type = 'text';

    public function email()
    {
        $this->type('email');

        $this->addRules([$this->name => ['email']]);

        return $this;
    }

    public function type($type)
    {
        $this->type = $type;

        return $this;
    }

    public function max($value)
    {
        $this->addRules([$this->name => ["max:$value"]]);

        return $this;
    }

    public function min($value)
    {
        $this->addRules([$this->name => ["min:$value"]]);

        return $this;
    }

    public function tel()
    {
        $this->type('tel');

        return $this;
    }

    public function password()
    {
        $this->type('password');

        return $this;
    }
}
