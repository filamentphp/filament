<?php

namespace Filament\Forms\Components;

class MultiSelect extends Field
{
    use Concerns\CanBeAutofocused;
    use Concerns\HasPlaceholder;

    public $emptyOptionsMessage = 'forms::fields.multiSelect.emptyOptionsMessage';

    public $noSearchResultsMessage = 'forms::fields.multiSelect.noSearchResultsMessage';

    public $options = [];

    protected function setup()
    {
        $this->placeholder('forms::fields.multiSelect.placeholder');
    }

    public function emptyOptionsMessage($message)
    {
        $this->emptyOptionsMessage = $message;

        return $this;
    }

    public function noSearchResultsMessage($message)
    {
        $this->noSearchResultsMessage = $message;

        return $this;
    }

    public function options($options)
    {
        $this->options = $options;

        return $this;
    }
}
