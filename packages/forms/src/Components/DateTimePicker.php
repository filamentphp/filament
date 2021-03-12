<?php

namespace Filament\Forms\Components;

class DateTimePicker extends DatePicker
{
    protected $time = true;

    protected $defaultDisplayFormat = 'F j, Y H:i:s';

    protected $defaultDisplayFormatWithoutSeconds = 'F j, Y H:i';

    protected $defaultFormat = 'Y-m-d H:i:s';

    protected $defaultFormatWithoutSeconds = 'Y-m-d H:i';

    protected $view = 'forms::components.date-time-picker';

    protected function setUp()
    {
        $this->configure(function () {
            $this->displayFormat($this->defaultDisplayFormat);
            $this->format($this->defaultFormat);
        });
    }

    public function withoutSeconds()
    {
        $this->configure(function () {
            $this->hasSeconds = false;

            if ($this->getDisplayFormat() === $this->defaultDisplayFormat) {
                $this->displayFormat($this->defaultDisplayFormatWithoutSeconds);
            }

            if ($this->getFormat() === $this->defaultFormat) {
                $this->format($this->defaultFormatWithoutSeconds);
            }
        });

        return $this;
    }
}
