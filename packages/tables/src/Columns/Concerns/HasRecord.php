<?php

namespace Filament\Tables\Columns\Concerns;

use Illuminate\Database\Eloquent\Model;

trait HasRecord
{
    /**
     * @var Model | array<string, mixed> | null
     */
    protected Model | array | null $record = null;

    protected ?string $recordKey = null;

    /**
     * @param  Model | array<string, mixed>  $record
     */
    public function record(Model | array $record): static
    {
        $this->record = $record;

        return $this;
    }

    public function recordKey(?string $recordKey): static
    {
        $this->recordKey = $recordKey;

        return $this;
    }

    public function getRecordKey(): ?string
    {
        return $this->recordKey;
    }

    /**
     * @return Model | array<string, mixed> | null
     */
    public function getRecord(): Model | array | null
    {
        return $this->record ?? $this->getLayout()?->getRecord();
    }
}
