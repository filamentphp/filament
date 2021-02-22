<?php

namespace Filament\Forms\Components;

class DateTimePicker extends DatePicker
{
    public $time = true;

    protected $defaultDisplayFormat = 'F j, Y H:i:s';

    protected $defaultDisplayFormatWithoutSeconds = 'F j, Y H:i';

    protected $defaultFormat = 'Y-m-d H:i:s';

    protected $defaultFormatWithoutSeconds = 'Y-m-d H:i';

    protected $view = 'forms::components.date-time-picker';

    public function __construct($name)
    {
        parent::__construct($name);

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
