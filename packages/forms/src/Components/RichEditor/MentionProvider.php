<?php

namespace Filament\Forms\Components\RichEditor;

use Closure;
use Illuminate\Support\Arr;

class MentionProvider
{
    protected ?Closure $getSearchResultsUsing = null;

    /**
     * @var array<string> | Closure
     */
    protected array | Closure $items = [];

    /**
     * @var array<string, mixed>|Closure|null
     */
    protected array | Closure | null $extraAttributes = null;

    protected ?Closure $getLabelsUsing = null;

    protected ?Closure $getUrlUsing = null;

    protected ?string $noItemsMessage = null;

    protected ?string $noSearchResultsMessage = null;

    protected ?string $searchingMessage = null;

    protected ?string $searchPrompt = null;

    final public function __construct(
        protected string $char,
    ) {}

    public static function make(string $char): static
    {
        return app(static::class, ['char' => $char]);
    }

    public function getSearchResultsUsing(?Closure $callback): static
    {
        $this->getSearchResultsUsing = $callback;

        return $this;
    }

    /**
     * @param  array<string> | Closure  $items
     */
    public function items(array | Closure $items): static
    {
        $this->items = $items;

        return $this;
    }

    /**
     * @param  Closure(array<string>): array<string, string>  $callback
     */
    public function getLabelsUsing(?Closure $callback): static
    {
        $this->getLabelsUsing = $callback;

        return $this;
    }

    /**
     * @param  Closure(string $id, string $label): ?string  $callback
     */
    public function url(?Closure $callback): static
    {
        $this->getUrlUsing = $callback;

        return $this;
    }

    /**
     * @param  array<string, mixed>|Closure(): array<string, mixed>  $attributes
     */
    public function extraAttributes(array | Closure $attributes): static
    {
        $this->extraAttributes = $attributes;

        return $this;
    }

    public function noItemsMessage(?string $message): static
    {
        $this->noItemsMessage = $message;

        return $this;
    }

    public function noSearchResultsMessage(?string $message): static
    {
        $this->noSearchResultsMessage = $message;

        return $this;
    }

    public function searchingMessage(?string $message): static
    {
        $this->searchingMessage = $message;

        return $this;
    }

    public function searchPrompt(?string $message): static
    {
        $this->searchPrompt = $message;

        return $this;
    }

    public function getChar(): string
    {
        return $this->char;
    }

    /**
     * @return array<string, mixed>
     */
    public function getExtraAttributes(): array
    {
        $attributes = $this->extraAttributes;

        if ($attributes instanceof Closure) {
            $attributes = $attributes();
        }

        return is_array($attributes) ? $attributes : [];
    }

    public function getNoItemsMessage(): string
    {
        return $this->noItemsMessage ?? __('filament-forms::components.rich_editor.mentions.no_options_message');
    }

    public function getNoSearchResultsMessage(): string
    {
        return $this->noSearchResultsMessage ?? __('filament-forms::components.rich_editor.mentions.no_search_results_message');
    }

    public function getSearchingMessage(): string
    {
        return $this->searchingMessage ?? __('filament-forms::components.rich_editor.mentions.searching_message');
    }

    public function getSearchPrompt(): string
    {
        return $this->searchPrompt ?? __('filament-forms::components.rich_editor.mentions.search_prompt');
    }

    public function getUrl(string $id, string $label): ?string
    {
        if (! ($this->getUrlUsing instanceof Closure)) {
            return null;
        }

        return ($this->getUrlUsing)($id, $label);
    }

    public function hasUrl(): bool
    {
        return $this->getUrlUsing instanceof Closure;
    }

    /**
     * @param  array<string>  $ids
     * @return array<string, string>
     */
    public function getLabels(array $ids): array
    {
        if ($this->getLabelsUsing instanceof Closure) {
            $labels = ($this->getLabelsUsing)($ids);
        } else {
            $labels = Arr::only($this->getItems(), $ids);
        }

        $result = [];

        foreach ($labels as $id => $label) {
            $result[(string) $id] = (string) $label;
        }

        return $result;
    }

    public function hasSearchResultsUsing(): bool
    {
        return $this->getSearchResultsUsing instanceof Closure;
    }

    public function hasItems(): bool
    {
        return filled($this->items);
    }

    /**
     * @return array<string, string>
     */
    public function getItems(): array
    {
        $items = $this->items;

        if ($items instanceof Closure) {
            $items = $items();
        }

        $normalized = [];

        foreach ($items as $id => $label) {
            $normalized[(string) $id] = (string) $label;
        }

        return $normalized;
    }

    /**
     * @return array<string, string>
     */
    public function getSearchResults(string $search): array
    {
        if ($this->getSearchResultsUsing instanceof Closure) {
            $results = ($this->getSearchResultsUsing)($search) ?? [];
        } else {
            $items = $this->getItems();

            if (blank($search)) {
                $results = $items;
            } else {
                $searchLower = strtolower($search);

                $results = array_filter(
                    $items,
                    static fn (string $label): bool => str_contains(strtolower($label), $searchLower),
                );
            }
        }

        $normalized = [];

        foreach ($results as $id => $label) {
            $normalized[(string) $id] = (string) $label;
        }

        return $normalized;
    }
}
