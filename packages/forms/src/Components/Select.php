<?php

namespace Filament\Forms\Components;

use Closure;
use Exception;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\HtmlString;

class Select extends Field
{
    use Concerns\HasAffixes;
    use Concerns\HasExtraAlpineAttributes;
    use Concerns\HasExtraInputAttributes;
    use Concerns\CanLimitItemsLength;
    use Concerns\HasOptions;
    use Concerns\HasPlaceholder;

    protected string $view = 'forms::components.select';

    protected array | Closure | null $createFormSchema = null;

    protected ?Closure $saveCreateFormUsing = null;

    protected bool | Closure $isMultiple = false;

    protected ?Closure $getOptionLabelUsing = null;

    protected ?Closure $getOptionLabelsUsing = null;

    protected ?Closure $getSearchResultsUsing = null;

    protected bool | Closure | null $isOptionDisabled = null;

    protected bool | Closure | null $isPlaceholderSelectionDisabled = false;

    protected bool | Closure $isSearchable = false;

    protected ?array $searchColumns = null;

    protected string | HtmlString | Closure | null $noSearchResultsMessage = null;

    protected string | HtmlString | Closure | null $searchPrompt = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->default(fn (Select $component): ?array => $component->isMultiple() ? [] : null);

        $this->afterStateHydrated(function (Select $component, $state): void {
            if (! $component->isMultiple()) {
                return;
            }

            if (is_array($state)) {
                return;
            }

            $component->state([]);
        });

        $this->getOptionLabelUsing(function (Select $component, $value): ?string {
            if (array_key_exists($value, $options = $component->getOptions())) {
                return $options[$value];
            }

            return $value;
        });

        $this->getOptionLabelsUsing(function (Select $component, array $values): array {
            $options = $component->getOptions();

            return collect($values)
                ->mapWithKeys(fn ($value) => [$value => $options[$value] ?? $value])
                ->toArray();
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

    public function createForm(array | Closure $schema): static
    {
        $this->createFormSchema = $schema;

        $this->registerActions([
            'create' => $this->getCreateAction(),
        ]);

        return $this;
    }

    public function saveCreateFormUsing(Closure $callback): static
    {
        $this->saveCreateFormUsing = $callback;

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

    public function getSaveCreateFormUsing(): ?Closure
    {
        return $this->saveCreateFormUsing;
    }

    public function getCreateAction(): ?Action
    {
        if ($this->createFormSchema === null) {
            return null;
        }

        return Action::make('create')
            ->modalHeading('Create')
            ->form($this->getCreateFormSchema())
            ->action(function (Select $component, $data) {
                if (! $this->getSaveCreateFormUsing()) {
                    throw new Exception("Select field [{$component->getStatePath()}] must have a [saveCreateFormUsing()] closure set.");
                }

                $key = $component->evaluate($this->getSaveCreateFormUsing(), [
                    'data' => $data,
                ]);

                $state = $component->isMultiple() ?
                    array_merge($component->getState(), $key) :
                    $key;

                $component->state($state);
            });
    }

    public function getCreateFormSchema(): ?array
    {
        return $this->evaluate($this->createFormSchema);
    }

    public function hasCreateFormSchema(): bool
    {
        return $this->getCreateFormSchema() !== null;
    }

    public function getOptionLabelUsing(?Closure $callback): static
    {
        $this->getOptionLabelUsing = $callback;

        return $this;
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

    public function multiple(bool | Closure $condition = true): static
    {
        $this->isMultiple = $condition;

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

    public function getOptionLabel(): ?string
    {
        return $this->evaluate($this->getOptionLabelUsing, [
            'value' => $this->getState(),
        ]);
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

    public function getSearchColumns(): ?array
    {
        return $this->searchColumns;
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

    public function isMultiple(): bool
    {
        return $this->evaluate($this->isMultiple);
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
