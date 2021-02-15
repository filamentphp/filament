<?php

namespace Filament\Forms\Fields;

use Filament\Forms\Fields\Concerns;

class Date extends InputField
{
    use Concerns\CanBeAutofocused;
    use Concerns\CanBeCompared;
    use Concerns\CanBeDisabled;
    use Concerns\CanBeUnique;
    use Concerns\HasPlaceholder;

    public $displayFormat;

    public $format;

    public $maxDate;

    public $minDate;

    public function __construct($name)
    {
        parent::__construct($name);

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
