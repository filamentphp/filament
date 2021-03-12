<?php

namespace Filament\Forms\Components;

class TextInput extends Field
{
    use Concerns\CanBeAutocompleted;
    use Concerns\CanBeAutofocused;
    use Concerns\CanBeCompared;
    use Concerns\CanBeUnique;
    use Concerns\CanBeLengthConstrained;
    use Concerns\HasPlaceholder;
    use Concerns\HasPrefix;

    protected $type = 'text';

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

    public function numeric()
    {
        $this->type('number');

        $this->addRules([$this->name => ['numeric']]);

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

    public function url()
    {
        $this->type('url');

        $this->addRules([$this->name => ['url']]);

        return $this;
    }
}
