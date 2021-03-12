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

    protected $hasTime = false;

    protected $hasSeconds = true;

    protected $maxDate;

    protected $minDate;

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

    public function getDisplayFormat()
    {
        return $this->displayFormat;
    }

    public function getFormat()
    {
        return $this->format;
    }

    public function getMaxDate()
    {
        return $this->maxDate;
    }

    public function getMinDate()
    {
        return $this->minDate;
    }

    public function hasSeconds()
    {
        return $this->hasSeconds;
    }

    public function hasTime()
    {
        return $this->hasTime;
    }

    public function maxDate($date)
    {
        $this->maxDate = $date;

        $this->addRules([$this->getName() => ["before_or_equal:$date"]]);

        return $this;
    }

    public function minDate($date)
    {
        $this->minDate = $date;

        $this->addRules([$this->getName() => ["after_or_equal:$date"]]);

        return $this;
    }
}
