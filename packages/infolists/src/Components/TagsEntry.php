<?php

namespace Filament\Infolists\Components;

use Closure;

class TagsEntry extends Entry
{
    /**
     * @var view-string
     */
    protected string $view = 'filament-infolists::components.tags-entry';

    protected string | Closure | null $separator = null;

    protected int | Closure | null $limit = null;

    /**
     * @return array<string>
     */
    public function getTags(): array
    {
        $tags = $this->getState();

        if (is_array($tags)) {
            return $tags;
        }

        if (! ($separator = $this->getSeparator())) {
            return [];
        }

        $tags = explode($separator, $tags);

        if (count($tags) === 1 && blank($tags[0])) {
            $tags = [];
        }

        return $tags;
    }

    public function separator(string | Closure | null $separator = ','): static
    {
        $this->separator = $separator;

        return $this;
    }

    public function limit(int | Closure | null $limit = 3): static
    {
        $this->limit = $limit;

        return $this;
    }

    public function getSeparator(): ?string
    {
        return $this->evaluate($this->separator);
    }

    public function getLimit(): ?int
    {
        return $this->evaluate($this->limit);
    }

    public function hasActiveLimit(): bool
    {
        $limit = $this->getLimit();

        return $limit && count($this->getTags()) > $limit;
    }

    /**
     * @return array<string>
     */
    public function getMoreTags(): array
    {
        return array_slice($this->getTags(), $this->getLimit());
    }
}
