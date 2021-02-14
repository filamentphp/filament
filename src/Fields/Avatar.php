<?php

namespace Filament\Fields;

class Avatar extends File
{
    public function __construct($name)
    {
        parent::__construct($name);

        $this->image();
    }
}
