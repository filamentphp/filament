<?php

namespace Filament\Forms\Components;

use Illuminate\Contracts\Support\Arrayable;

class MultiSelect extends Field
{
    use Concerns\HasPlaceholder;

    protected string $view = 'forms::components.multi-select';

    protected $getOptionLabelsUsing = null;

    protected $getSearchResultsUsing = null;

    protected $noOptionsMessage = null;

    protected $noSearchResultsMessage = null;

    protected $options = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->getOptionLabelsUsing(function (MultiSelect $component, array $values): array {
            $options = $component->getOptions();

            return collect($values)
                ->mapWithKeys(fn ($value) => [$value => $options[$value] ?? $value])
                ->toArray();
        });

        $this->noOptionsMessage(__('forms::components.multiSelect.noOptionsMessage'));

        $this->noSearchResultsMessage(__('forms::components.multiSelect.noSearchResultsMessage'));

        $this->placeholder(__('forms::components.multiSelect.placeholder'));
    }

    public function getOptionLabelsUsing(callable $callback): static
    {
        $this->getOptionLabelsUsing = $callback;

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

    public function getNoOptionsMessage(): string
    {
        return $this->evaluate($this->noOptionsMessage);
    }

    public function getNoSearchResultsMessage(): string
    {
        return $this->evaluate($this->noSearchResultsMessage);
    }

    public function getOptionLabels(): array
    {
        $labels = $this->evaluate($this->getOptionLabelsUsing, [
            'values' => $this->getState(),
        ]);

        if ($labels instanceof Arrayable) {
            $labels = $labels->toArray();
        }

        return $labels;
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

    public function getState(): array
    {
        $state = parent::getState();

        if (! is_array($state)) {
            return [];
        }

        return $state;
    }
}
