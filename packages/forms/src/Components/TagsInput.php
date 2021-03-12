<?php

namespace Filament\Forms\Components;

class TagsInput extends Field
{
    use Concerns\CanBeAutofocused;
    use Concerns\CanBeCompared;
    use Concerns\CanBeUnique;
    use Concerns\CanBeLengthConstrained;
    use Concerns\HasPlaceholder;

    protected $separator = ',';

    public function getPlaceholder()
    {
        if ($this->placeholder === null) {
            return 'forms::fields.tags.placeholder';
        }

        return $this->placeholder;
    }

    public function getSeparator()
    {
        return $this->separator;
    }

    public function separator($separator)
    {
        $this->configure(function () use ($separator) {
            $this->separator = $separator;
        });

        return $this;
    }
}
