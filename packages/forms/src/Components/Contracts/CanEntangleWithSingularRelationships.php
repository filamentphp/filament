<?php

namespace Filament\Forms\Components\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;

interface CanEntangleWithSingularRelationships
{
    public function clearCachedExistingRecord(): void;

    public function fillFromRelationship(): void;

    public function getCachedExistingRecord(): ?Model;

    public function getRelatedModel(): ?string;

    public function getRelationship(): BelongsTo | HasOne | MorphOne | null;
}
