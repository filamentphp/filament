<?php

namespace Filament\Tables\Columns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Filesystem\FilesystemAdapter;

class SpatieMediaLibraryImageColumn extends ImageColumn
{
    protected ?string $collection = null;

    protected string $conversion = '';

    protected string | Closure $visibility = 'public';

    public function collection(string $collection): static
    {
        $this->collection = $collection;

        return $this;
    }

    public function conversion(string $conversion): static
    {
        $this->conversion = $conversion;

        return $this;
    }

    public function visibility(string | Closure | null $visibility): static
    {
        $this->visibility = $visibility;

        return $this;
    }

    public function getCollection(): ?string
    {
        return $this->collection ?? 'default';
    }

    public function getConversion(): string
    {
        return $this->conversion ?? '';
    }

    public function getVisibility(): string
    {
        return $this->evaluate($this->visibility);
    }

    public function getImagePath(): ?string
    {
        $state = $this->getState();

        if ($state) {
            return $state;
        }

        $record = $this->getRecord();

        if (! method_exists($record, 'getFirstMediaUrl')) {
            return $state;
        }

        if ($this->getVisibility() === 'private') {
            return $record->getFirstTemporaryUrl(now()->addMinutes(5), $this->getCollection(), $this->getConversion());
        }

        return $record->getFirstMediaUrl($this->getCollection(), $this->getConversion());
    }

    public function applyEagerLoading(Builder $query): Builder
    {
        if ($this->isHidden()) {
            return $query;
        }

        return $query->with(['media']);
    }
}
