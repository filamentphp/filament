<?php

namespace Filament\Forms\Components;

class DateTimePicker extends DatePicker
{
    public $time = true;

    public $defaultDisplayFormat = 'F j, Y H:i:s';

    public $defaultDisplayFormatWithoutSeconds = 'F j, Y H:i';

    public $defaultFormat = 'Y-m-d H:i:s';

    public $defaultFormatWithoutSeconds = 'Y-m-d H:i';

    public $view = 'forms::components.date-time-picker';

    protected function setUp()
    {
        $this->displayFormat($this->defaultDisplayFormat);
        $this->format($this->defaultFormat);
    }

    public function withoutSeconds()
    {
        $this->withoutSeconds = true;

        if ($this->displayFormat === $this->defaultDisplayFormat) {
            $this->displayFormat($this->defaultDisplayFormatWithoutSeconds);
        }

        if ($this->format === $this->defaultFormat) {
            $this->format($this->defaultFormatWithoutSeconds);
        }

        return $this;
    }
}
