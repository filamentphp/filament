<?php

namespace Filament\Tables\Columns\Concerns;

use Illuminate\Database\Eloquent\Model;

trait HasRecord
{
    /**
     * @var Model | array<string, mixed> | null
     */
    protected Model | array | null $record = null;

    /**
     * @param  Model | array<string, mixed>  $record
     */
    public function record(Model | array $record): static
    {
        $this->record = $record;

        return $this;
    }

    /**
     * @return Model | array<string, mixed> | null
     */
    public function getRecord(): Model | array | null
    {
        return $this->record ?? $this->getLayout()?->getRecord();
    }
}
