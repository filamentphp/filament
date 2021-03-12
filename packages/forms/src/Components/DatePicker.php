<?php

namespace Filament\Forms\Components;

class DatePicker extends Field
{
    use Concerns\CanBeAutofocused;
    use Concerns\CanBeCompared;
    use Concerns\CanBeUnique;
    use Concerns\HasPlaceholder;

    protected $displayFormat;

    protected $format;

    protected $maxDate;

    protected $minDate;

    protected $time = false;

    protected $withoutSeconds = false;

    protected $view = 'forms::components.date-time-picker';

    protected function setUp()
    {
        $this->displayFormat('F j, Y');
        $this->format('Y-m-d');
    }

    public function displayFormat($format)
    {
        $this->displayFormat = $format;

        return $this;
    }

    public function format($format)
    {
        $this->format = $format;

        return $this;
    }

    public function maxDate($date)
    {
        $this->maxDate = $date;

        $this->addRules([$this->name => ["before_or_equal:$date"]]);

        return $this;
    }

    public function minDate($date)
    {
        $this->minDate = $date;

        $this->addRules([$this->name => ["after_or_equal:$date"]]);

        return $this;
    }
}
