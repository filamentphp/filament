<?php

namespace Filament\Tables\Columns;

class TagsColumn extends Column
{
    protected string $view = 'tables::columns.tags-column';

    protected ?string $separator = null;

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

    public function separator(?string $separator = ','): static
    {
        $this->separator = $separator;

        return $this;
    }

    public function getSeparator(): ?string
    {
        return $this->separator;
    }
}
