<?php

namespace Filament\Fields;

class Text extends InputField
{
    public $type = 'text';

    public function email()
    {
        $this->type('email');

        $this->addRules([$this->name => 'email']);

        return $this;
    }

    public function password()
    {
        $this->type('password');

        return $this;
    }

    public function type($type)
    {
        $this->type = $type;

        return $this;
    }
}
