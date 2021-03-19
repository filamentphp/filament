<?php

namespace Filament\Forms\Components;

use Illuminate\Support\Str;

class Select extends Field
{
    use Concerns\CanBeAutofocused;
    use Concerns\CanBeCompared;
    use Concerns\CanBeUnique;
    use Concerns\HasPlaceholder;

    protected $emptyOptionsMessage = 'forms::fields.select.emptyOptionsMessage';

    protected $getDisplayValue;

    protected $getOptionSearchResults;

    protected $noSearchResultsMessage = 'forms::fields.select.noSearchResultsMessage';

    protected $options = [];

    protected function setUp()
    {
        $this->placeholder('forms::fields.select.placeholder');

        $this->getDisplayValueUsing(function ($value) {
            return $this->getOptions()[(string) $value] ?? null;
        });

        $this->getOptionSearchResultsUsing(function ($search) {
            return collect($this->getOptions())
                ->filter(fn ($option) => Str::of($option)->lower()->contains($search))
                ->toArray();
        });
    }

    public function emptyOptionsMessage($message)
    {
        $this->configure(function () use ($message) {
            $this->emptyOptionsMessage = $message;
        });

        return $this;
    }

    public function getDisplayValue($value)
    {
        $callback = $this->getDisplayValue;

        return $callback($value);
    }

    public function getDisplayValueUsing($callback)
    {
        $this->configure(function () use ($callback) {
            $this->getDisplayValue = $callback;
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

    public function getOptionSearchResults($search)
    {
        $search = (string) Str::of($search)->trim()->lower();

        $callback = $this->getOptionSearchResults;

        return $callback($search);
    }

    public function getOptionSearchResultsUsing($callback)
    {
        $this->configure(function () use ($callback) {
            $this->getOptionSearchResults = $callback;
        });

        return $this;
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
