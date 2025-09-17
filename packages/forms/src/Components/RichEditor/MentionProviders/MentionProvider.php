<?php

namespace Filament\Forms\Components\RichEditor\MentionProviders;

use Closure;

class MentionProvider
{

    /**
     * @param  array<mixed>|null  $options
     * @param  array<string, mixed>|Closure|null  $extraAttributes
     */
    public function __construct(
        public string $char,
        protected ?Closure $getSearchResultsUsing = null,
        protected ?array $options = null,
        protected array|Closure|null $extraAttributes = null,
        protected ?Closure $getOptionLabelUsing = null,
    ) {
    }

    public static function make(string $char): self
    {
        return new self(char: $char);
    }

    public function getSearchResultsUsing(Closure $callback): static
    {
        $this->getSearchResultsUsing = $callback;

        return $this;
    }

    /**
     * @param  array<mixed>  $options
     */
    public function options(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function getOptionLabelUsing(Closure $callback): static
    {
        $this->getOptionLabelUsing = $callback;


        return $this;
    }

    /**
     * Define extra HTML attributes to apply on the rendered mention element.
     *
     * @param  array<string, mixed>|Closure():array<string, mixed>  $attributes
     */
    public function extraAttributes(array|Closure $attributes): static
    {
        $this->extraAttributes = $attributes;

        return $this;
    }

    public function getChar(): string
    {
        return $this->char;
    }

    /**
     * @return array<string, mixed>
     */
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

    public function getOptionLabelForId(mixed $id): ?string
    {
        if ($this->getOptionLabelUsing instanceof Closure) {
            return ($this->getOptionLabelUsing)($id);
        }

        $items = $this->options ?? [];

        // Associative array: id => label
        if (! empty($items) && array_keys($items) !== range(0, count($items) - 1)) {
            return isset($items[$id]) ? strval($items[$id]) : null;
        }

        foreach ($items as $item) {
            if (is_array($item)) {
                $itemId = $item['id'] ?? null;
                if ($itemId == $id) {
                    $label = $item['label'] ?? ($item['name'] ?? null);
                    return $label !== null ? strval($label) : null;
                }
            } elseif (is_string($item) && $item === $id) {
                return $item;
            }
        }

        return null;
    }

    /**
     * @return array<mixed>
     */
    public function resolveItems(string $search): array
    {
        if (! $this->getSearchResultsUsing && ! $this->options) {
            return [];
        }

        $results = $this->getSearchResultsUsing
            ? ($this->getSearchResultsUsing)($search)
            : $this->options;

        // if ($results instanceof \Illuminate\Contracts\Support\Arrayable) {
        //     $results = $results->toArray();
        // }

        return $results ?? [];
    }
}

