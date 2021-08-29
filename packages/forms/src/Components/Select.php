<?php

namespace Filament\Forms\Components;

use Illuminate\Contracts\Support\Arrayable;

class Select extends Field
{
    use Concerns\HasPlaceholder;

    protected string $view = 'forms::components.select';

    protected $getOptionLabelUsing = null;

    protected $getSearchResultsUsing = null;

    protected $isSearchable = false;

    protected $noOptionsMessage = null;

    protected $noSearchResultsMessage = null;

    protected $options = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->getOptionLabelUsing(function (Select $component, $value): ?string {
            if (array_key_exists($value, $options = $component->getOptions())) {
                return $options[$value];
            }

            return $value;
        });

        $this->noOptionsMessage(__('forms::components.select.noOptionsMessage'));

        $this->noSearchResultsMessage(__('forms::components.select.noSearchResultsMessage'));

        $this->placeholder(__('forms::components.select.placeholder'));
    }

    public function getOptionLabelUsing(callable $callback): static
    {
        $this->getOptionLabelUsing = $callback;

        return $this;
    }

    public function getSearchResultsUsing(callable $callback): static
    {
        $this->getSearchResultsUsing = $callback;

        return $this;
    }

    public function noOptionsMessage(string | callable $message): static
    {
        $this->noOptionsMessage = $message;

        return $this;
    }

    public function noSearchResultsMessage(string | callable $message): static
    {
        $this->noSearchResultsMessage = $message;

        return $this;
    }

    public function options(array | callable $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function searchable(bool | callable $condition = true): static
    {
        $this->isSearchable = $condition;

        return $this;
    }

    public function getNoOptionsMessage(): string
    {
        return $this->evaluate($this->noOptionsMessage);
    }

    public function getNoSearchResultsMessage(): string
    {
        return $this->evaluate($this->noSearchResultsMessage);
    }

    public function getOptionLabel()
    {
        return $this->evaluate($this->getOptionLabelUsing, [
            'value' => $this->getState(),
        ]);
    }

    public function getOptions(): array
    {
        $options = $this->evaluate($this->options);

        if ($options instanceof Arrayable) {
            $options = $options->toArray();
        }

        return $options;
    }

    public function getSearchResults(string $query): array
    {
        if (! $this->getSearchResultsUsing) {
            return [];
        }

        $results = $this->evaluate($this->getSearchResultsUsing, [
            'query' => $query,
        ]);

        if ($results instanceof Arrayable) {
            $results = $results->toArray();
        }

        return $results;
    }

    public function isSearchable(): bool
    {
        return (bool) $this->evaluate($this->isSearchable);
    }
}
