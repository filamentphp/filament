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
        $this->configure(function () {
            $this->type('email');

            $this->addRules([$this->getName() => ['email']]);
        });

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function max($value)
    {
        $this->configure(function () use ($value) {
            $this->addRules([$this->getName() => ["max:{$value}"]]);
        });

        return $this;
    }

    public function min($value)
    {
        $this->configure(function () use ($value) {
            $this->addRules([$this->getName() => ["min:{$value}"]]);
        });

        return $this;
    }

    public function numeric()
    {
        $this->configure(function () {
            $this->type('number');

            $this->addRules([$this->getName() => ['numeric']]);
        });

        return $this;
    }

    public function password()
    {
        $this->configure(function () {
            $this->type('password');
        });

        return $this;
    }

    public function tel()
    {
        $this->configure(function () {
            $this->type('tel');
        });

        return $this;
    }

    public function type($type)
    {
        $this->configure(function () use ($type) {
            $this->type = $type;
        });

        return $this;
    }

    public function url()
    {
        $this->configure(function () {
            $this->type('url');

            $this->addRules([$this->getName() => ['url']]);
        });

        return $this;
    }
}
