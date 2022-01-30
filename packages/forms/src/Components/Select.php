<?php

namespace Filament\Forms\Components;

use Closure;
use Illuminate\Contracts\Support\Arrayable;

class Select extends Field
{
    use Concerns\HasExtraAlpineAttributes;
    use Concerns\HasPlaceholder;

    protected string $view = 'forms::components.select';

    protected ?Closure $getOptionLabelUsing = null;

    protected ?Closure $getSearchResultsUsing = null;

    protected bool | Closure | null $isOptionDisabled = null;

    protected bool | Closure | null $isPlaceholderSelectionDisabled = false;

    protected bool | Closure $isSearchable = false;

    protected ?array $searchColumns = null;

    protected array | Arrayable | Closure $options = [];

    protected string | Closure | null $noSearchResultsMessage = null;

    protected string | Closure | null $searchPrompt = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->getOptionLabelUsing(function (Select $component, $value): ?string {
            if (array_key_exists($value, $options = $component->getOptions())) {
                return $options[$value];
            }

            return $value;
        });

        $this->noSearchResultsMessage(__('forms::components.select.no_search_results_message'));

        $this->placeholder(__('forms::components.select.placeholder'));

        $this->searchPrompt(__('forms::components.select.search_prompt'));
    }

    public function boolean(string $trueLabel = 'Yes', string $falseLabel = 'No'): static
    {
        $this->options([
            1 => $trueLabel,
            0 => $falseLabel,
        ]);

        return $this;
    }

    public function disableOptionWhen(bool | Closure $callback): static
    {
        $this->isOptionDisabled = $callback;

        return $this;
    }

    public function disablePlaceholderSelection(bool | Closure $condition = true): static
    {
        $this->isPlaceholderSelectionDisabled = $condition;

        return $this;
    }

    public function getOptionLabelUsing(?Closure $callback): static
    {
        $this->getOptionLabelUsing = $callback;

        return $this;
    }

    public function getSearchResultsUsing(?Closure $callback): static
    {
        $this->getSearchResultsUsing = $callback;

        return $this;
    }

    public function options(array | Arrayable | Closure $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function searchable(bool | array | Closure $condition = true): static
    {
        if (is_array($condition)) {
            $this->isSearchable = true;
            $this->searchColumns = $condition;
        } else {
            $this->isSearchable = $condition;
            $this->searchColumns = null;
        }

        return $this;
    }

    public function searchPrompt(string | Closure | null $message): static
    {
        $this->searchPrompt = $message;

        return $this;
    }

    public function noSearchResultsMessage(string | Closure | null $message): static
    {
        $this->noSearchResultsMessage = $message;

        return $this;
    }

    public function getOptionLabel(): ?string
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

    public function getNoSearchResultsMessage(): string
    {
        return $this->evaluate($this->noSearchResultsMessage);
    }

    public function getSearchColumns(): ?array
    {
        return $this->searchColumns;
    }

    public function getSearchPrompt(): string
    {
        return $this->evaluate($this->searchPrompt);
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

    public function isOptionDisabled($value, string $label): bool
    {
        if ($this->isOptionDisabled === null) {
            return false;
        }

        return (bool) $this->evaluate($this->isOptionDisabled, [
            'label' => $label,
            'value' => $value,
        ]);
    }

    public function isPlaceholderSelectionDisabled(): bool
    {
        return (bool) $this->evaluate($this->isPlaceholderSelectionDisabled);
    }

    public function isSearchable(): bool
    {
        return (bool) $this->evaluate($this->isSearchable);
    }
}
