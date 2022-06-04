<?php

namespace Filament\Support\Actions\Contracts;

use Illuminate\Database\Eloquent\Model;

interface ReplicatesRecords
{
    public function callBeforeReplicaSaved(Model $replica): void;

    public function callAfterReplicaSaved(Model $replica);

    public function getExcludedAttributes(): ?array;
}
