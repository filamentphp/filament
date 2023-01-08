<?php

namespace Filament\Actions\Contracts;

use Illuminate\Database\Eloquent\Model;

interface ReplicatesRecords
{
    public function callBeforeReplicaSaved(): void;

    /**
     * @return array<string> | null
     */
    public function getExcludedAttributes(): ?array;
}
