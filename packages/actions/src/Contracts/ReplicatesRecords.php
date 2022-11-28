<?php

namespace Filament\Actions\Contracts;

use Illuminate\Database\Eloquent\Model;

interface ReplicatesRecords
{
    public function callBeforeReplicaSaved(Model $replica): void;

    /**
     * @return mixed
     */
    public function callAfterReplicaSaved(Model $replica);

    /**
     * @return array<string> | null
     */
    public function getExcludedAttributes(): ?array;
}
