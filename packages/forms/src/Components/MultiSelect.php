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
        $this->configure(function () use ($message) {
            $this->emptyOptionsMessage = $message;
        });

        return $this;
    }

    public function getEmptyOptionsMessage()
    {
        return $this->emptyOptionsMessage;
    }

    public function getNoSearchResultsMessage()
    {
        return $this->noSearchResultsMessage;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function noSearchResultsMessage($message)
    {
        $this->configure(function () use ($message) {
            $this->noSearchResultsMessage = $message;
        });

        return $this;
    }

    public function options($options)
    {
        $this->configure(function () use ($options) {
            $this->options = $options;
        });

        return $this;
    }
}
