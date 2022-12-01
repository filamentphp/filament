<?php

namespace Filament\Actions\Contracts;

use Illuminate\Database\Eloquent\Model;

interface ReplicatesRecords
{
    public function callBeforeReplicaSaved(Model $replica): void;

    public function callAfterReplicaSaved(Model $replica): mixed;

    /**
     * @return array<string> | null
     */
    public function getExcludedAttributes(): ?array;
}
