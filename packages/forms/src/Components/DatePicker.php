<?php

namespace Filament\Forms\Components;

class DatePicker extends Field
{
    use Concerns\CanBeAutofocused;
    use Concerns\CanBeCompared;
    use Concerns\CanBeUnique;
    use Concerns\HasPlaceholder;

    protected $displayFormat;

    protected $firstDayOfWeek;

    protected $format;

    protected $hasSeconds = true;

    protected $hasTime = false;

    protected $maxDate;

    protected $minDate;

    protected $view = 'forms::components.date-time-picker';

    protected function setUp()
    {
        $this->configure(function () {
            $this->displayFormat('F j, Y');
            $this->resetFirstDayOfWeek();
            $this->format('Y-m-d');
        });
    }

    public function displayFormat($format)
    {
        $this->configure(function () use ($format) {
            $this->displayFormat = $format;
        });

        return $this;
    }

    public function firstDayOfWeek($day = 1)
    {
        $this->configure(function () use ($day) {
            if ($day < 0 || $day > 7) {
                $day = $this->getDefaultFirstDayOfWeek();
            }

            $this->firstDayOfWeek = $day;
        });

        return $this;
    }

    public function format($format)
    {
        $this->configure(function () use ($format) {
            $this->format = $format;
        });

        return $this;
    }

    protected function getDefaultFirstDayOfWeek()
    {
        return config('forms.first_day_of_week', 1);
    }

    public function getDisplayFormat()
    {
        return $this->displayFormat;
    }

    public function getFirstDayOfWeek()
    {
        return $this->firstDayOfWeek;
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
        $this->configure(function () use ($date) {
            $this->maxDate = $date;

            $this->addRules([$this->getName() => ["before_or_equal:{$date}"]]);
        });

        return $this;
    }

    public function minDate($date)
    {
        $this->configure(function () use ($date) {
            $this->minDate = $date;

            $this->addRules([$this->getName() => ["after_or_equal:{$date}"]]);
        });

        return $this;
    }

    public function resetFirstDayOfWeek()
    {
        $this->firstDayOfWeek($this->getDefaultFirstDayOfWeek());

        return $this;
    }

    public function weekStartsOnSunday()
    {
        $this->firstDayOfWeek(7);

        return $this;
    }

    public function weekStartsOnMonday()
    {
        $this->firstDayOfWeek(1);

        return $this;
    }
}
