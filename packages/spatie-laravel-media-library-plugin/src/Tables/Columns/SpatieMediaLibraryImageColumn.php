<?php

namespace Filament\Tables\Columns;

use Illuminate\Database\Eloquent\Model;
use Throwable;

class SpatieMediaLibraryImageColumn extends ImageColumn
{
    protected ?string $collection = null;

    protected string $conversion = '';

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

    public function getCollection(): ?string
    {
        return $this->collection ?? 'default';
    }

    public function getConversion(): string
    {
        return $this->conversion ?? '';
    }

    public function getImagePath(): ?string
    {
        $state = $this->getState();

        if ($state && ! $state instanceof Model) {
            return $state;
        }
        if ($state instanceof Model) {
            $record = $state;
        }else{
            $record = $this->getRecord();
        }

        if ($this->getVisibility() === 'private' && method_exists($record, 'getFirstTemporaryUrl')) {
            try {
                return $record->getFirstTemporaryUrl(
                    now()->addMinutes(5),
                    $this->getCollection(),
                    $this->getConversion(),
                );
            } catch (Throwable $exception) {
                // This driver does not support creating temporary URLs.
            }
        }

        if (! method_exists($record, 'getFirstMediaUrl')) {
            return $state;
        }

        return $record->getFirstMediaUrl($this->getCollection(), $this->getConversion());
    }

}
