<?php

namespace Filament\Tables\Columns;

use Closure;

class TagsColumn extends Column
{
    protected string $view = 'tables::columns.tags-column';

    protected string | Closure | null $separator = null;

    protected ?int $limit = null;

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

    public function limit(int $limit): static
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
        return $this->limit;
    }

    public function hasMoreTags(): bool
    {
        return $this->limit && count($this->getTags()) > $this->limit;
    }

    public function getRemainingTags(): array
    {
        return array_slice($this->getTags(), $this->limit);
    }

    public function getMoreLabel(): ?string
    {
        if (! $this->hasMoreTags()) {
            return null;
        }

        return __('tables::table.fields.tags.more_results', [
            'count' => count($this->getTags()) - $this->limit,
        ]);
    }
}
