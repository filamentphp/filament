<?php

namespace Filament\Forms\Components;

class MultiSelect extends Field
{
    use Concerns\CanBeAutofocused;
    use Concerns\HasPlaceholder;

    protected $emptyOptionsMessage = 'forms::fields.multiSelect.emptyOptionsMessage';

    protected $noSearchResultsMessage = 'forms::fields.multiSelect.noSearchResultsMessage';

    protected $options = [];

    protected function setUp()
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
