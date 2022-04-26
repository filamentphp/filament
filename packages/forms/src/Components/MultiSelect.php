<?php

namespace Filament\Forms\Components;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\HtmlString;

class MultiSelect extends Field
{
    use Concerns\CanLimitItemsLength;
    use Concerns\HasExtraAlpineAttributes;
    use Concerns\HasOptions;
    use Concerns\HasPlaceholder;

    protected string $view = 'forms::components.multi-select';

    protected ?Closure $getOptionLabelsUsing = null;

    protected ?Closure $getSearchResultsUsing = null;

    protected string | HtmlString | Closure | null $noSearchResultsMessage = null;

    protected string | HtmlString | Closure | null $searchPrompt = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->default([]);

        $this->afterStateHydrated(static function (MultiSelect $component, $state) {
            if (is_array($state)) {
                return;
            }

            $component->state([]);
        });

        $this->getOptionLabelsUsing(static function (MultiSelect $component, array $values): array {
            $options = $component->getOptions();

            return collect($values)
                ->mapWithKeys(fn ($value) => [$value => $options[$value] ?? $value])
                ->toArray();
        });

        $this->noSearchResultsMessage(__('forms::components.multi_select.no_search_results_message'));

        $this->placeholder(__('forms::components.multi_select.placeholder'));

        $this->searchPrompt(__('forms::components.multi_select.search_prompt'));
    }

    public function getOptionLabelsUsing(?Closure $callback): static
    {
        $this->getOptionLabelsUsing = $callback;

        return $this;
    }

    public function getSearchResultsUsing(?Closure $callback): static
    {
        $this->getSearchResultsUsing = $callback;

        return $this;
    }

    public function noSearchResultsMessage(string | HtmlString | Closure | null $message): static
    {
        $this->noSearchResultsMessage = $message;

        return $this;
    }

    public function searchPrompt(string | HtmlString | Closure | null $message): static
    {
        $this->searchPrompt = $message;

        return $this;
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

    public function getNoSearchResultsMessage(): string | HtmlString
    {
        return $this->evaluate($this->noSearchResultsMessage);
    }

    public function getSearchPrompt(): string | HtmlString
    {
        return $this->evaluate($this->searchPrompt);
    }

    public function getSearchResults(string $searchQuery): array
    {
        if (! $this->getSearchResultsUsing) {
            return [];
        }

        $results = $this->evaluate($this->getSearchResultsUsing, [
            'query' => $searchQuery,
            'searchQuery' => $searchQuery,
        ]);

        if ($results instanceof Arrayable) {
            $results = $results->toArray();
        }

        return $results;
    }
}
