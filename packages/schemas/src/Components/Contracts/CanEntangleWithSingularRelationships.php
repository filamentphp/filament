<?php

namespace Filament\Schemas\Components\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;

interface CanEntangleWithSingularRelationships
{
    public function cachedExistingRecord(?Model $record): static;

    public function clearCachedExistingRecord(): void;

    public function fillFromRelationship(): void;

    public function getCachedExistingRecord(): ?Model;

    /**
     * @return class-string<Model>|null
     */
    public function getRelatedModel(): ?string;

    public function getRelationship(): BelongsTo | HasOne | MorphOne | null;

    public function getRelationshipName(): ?string;

    public function hasRelationship(): bool;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function mutateRelationshipDataBeforeFill(array $data): array;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function mutateRelationshipDataBeforeCreate(array $data): array;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function mutateRelationshipDataBeforeSave(array $data): array;
}
