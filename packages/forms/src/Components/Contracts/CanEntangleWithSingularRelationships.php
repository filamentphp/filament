<?php

namespace Filament\Forms\Components\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

interface CanEntangleWithSingularRelationships
{
    public function getRelationship(): BelongsTo | HasOne | null;

    public function getCachedExistingRecord(): ?Model;

    public function fillFromRelationship(): void;

    public function clearCachedExistingRecord(): void;
}
