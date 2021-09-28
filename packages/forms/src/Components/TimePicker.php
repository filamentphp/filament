<?php

namespace Filament\Forms\Components;

class TimePicker extends DateTimePicker
{
    protected $defaultDisplayFormat = 'H:i:s';

    protected $defaultDisplayFormatWithoutSeconds = 'H:i';

    protected $hasTime = true;

    protected $hasDate = false;

    protected function setUp()
    {
        parent::setUp();

        $this->configure(function () {
            $this->displayFormat($this->defaultDisplayFormat);
            $this->format($this->defaultFormat);
        });
    }
}