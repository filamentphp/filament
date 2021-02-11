<?php

namespace Filament\Fields;

use Filament\FieldConcerns;

class DateTime extends InputField
{
    use FieldConcerns\CanBeAutofocused;
    use FieldConcerns\CanBeCompared;
    use FieldConcerns\CanBeDateConstrained;
    use FieldConcerns\CanBeDisabled;
    use FieldConcerns\CanBeUnique;
    use FieldConcerns\HasDateFormats;
    use FieldConcerns\HasPlaceholder;

    protected $defaultDisplayFormat = 'F j, Y H:i:s';

    protected $defaultDisplayFormatWithoutSeconds = 'F j, Y H:i';

    protected $defaultFormat = 'Y-m-d H:i:s';

    protected $defaultFormatWithoutSeconds = 'Y-m-d H:i';

    public $withoutSeconds = false;

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
