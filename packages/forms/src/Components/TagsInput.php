<?php

namespace Filament\Forms\Components;

class TagsInput extends Field
{
    use Concerns\CanBeAutofocused;
    use Concerns\CanBeCompared;
    use Concerns\CanBeUnique;
    use Concerns\CanBeLengthConstrained;
    use Concerns\HasPlaceholder;

    public $separator = ',';

    protected function setup()
    {
        $this->placeholder('forms::fields.tags.placeholder');
    }

    public function separator($separator)
    {
        $this->separator = $separator;

        return $this;
    }
}
