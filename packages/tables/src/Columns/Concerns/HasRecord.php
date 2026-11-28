<?php

namespace Filament\Tables\Columns\Concerns;

use Illuminate\Database\Eloquent\Model;
use stdClass;

trait HasRecord
{
    /**
     * @var Model | array<string, mixed> | null
     */
    protected Model | array | null $record = null;

    protected ?string $recordKey = null;

    protected ?string $cachedStateCacheKey = null;

    /**
     * @param  Model | array<string, mixed>  $record
     */
    public function record(Model | array $record): static
    {
        $this->cachedStateCacheKey = null;
        $this->record = $record;
        $this->rowLoop = null;

        return $this;
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     *
     * @param  Model | array<string, mixed> | null  $record
     */
    public function getCloneForRecord(Model | array | null $record, ?stdClass $rowLoop = null): static
    {
        $component = clone $this;
        $component->record = $record;
        $component->rowLoop = $rowLoop;
        $component->layout = $this->getLayout()?->getCloneForRecord($record, $rowLoop);

        return $component;
    }

    public function recordKey(?string $recordKey): static
    {
        $this->cachedStateCacheKey = null;
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
