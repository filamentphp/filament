<?php

namespace Filament\Forms\Components;

use Illuminate\Support\Str;

class Select extends Field
{
    use Concerns\CanBeAutofocused;
    use Concerns\CanBeCompared;
    use Concerns\CanBeUnique;
    use Concerns\CanBeLengthConstrained;
    use Concerns\HasPlaceholder;

    public $emptyOptionsMessage = 'forms::fields.select.emptyOptionsMessage';

    public $getDisplayValue;

    public $getOptionSearchResults;

    public $noSearchResultsMessage = 'forms::fields.select.noSearchResultsMessage';

    public $options = [];

    public function __construct($name)
    {
        parent::__construct($name);

        $this->placeholder('forms::fields.select.placeholder');

        $this->getDisplayValueUsing(function ($value) {
            return $this->options[$value] ?? null;
        });

        $this->getOptionSearchResultsUsing(function ($search) {
            return collect($this->options)
                ->filter(fn ($option) => Str::of($option)->lower()->contains($search))
                ->toArray();
        });
    }

    public function emptyOptionsMessage($message)
    {
        $this->emptyOptionsMessage = $message;

        return $this;
    }

    public function getDisplayValue($value)
    {
        $callback = $this->getDisplayValue;

        return $callback($value);
    }

    public function getOptionSearchResults($search)
    {
        $search = (string) Str::of($search)->trim()->lower();

        $callback = $this->getOptionSearchResults;

        return $callback($search);
    }

    public function getDisplayValueUsing($callback)
    {
        $this->getDisplayValue = $callback;

        return $this;
    }

    public function getOptionSearchResultsUsing($callback)
    {
        $this->getOptionSearchResults = $callback;

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
