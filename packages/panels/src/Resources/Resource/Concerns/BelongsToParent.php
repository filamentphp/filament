<?php

namespace Filament\Resources\Resource\Concerns;

use Filament\Resources\ParentResourceRegistration;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

trait BelongsToParent
{
    /**
     * @var class-string<resource>|null
     */
    protected static ?string $parentResource = null;

    public static function getParentResource(): string | ParentResourceRegistration | null
    {
        return static::$parentResource;
    }

    public static function asParent(): ParentResourceRegistration
    {
        return new ParentResourceRegistration(static::class);
    }

    public static function getParentResourceRegistration(): ?ParentResourceRegistration
    {
        $parentResource = static::getParentResource();

        if (is_string($parentResource)) {
            $parentResource = $parentResource::asParent();
        }

        return $parentResource;
    }

    public static function scopeEloquentQueryToParent(Builder $query, Model $parentRecord): Builder
    {
        $parentResourceRegistration = static::getParentResourceRegistration();

        $parentRelationship = $parentResourceRegistration->getInverseRelationship($query->getModel());
        $parentRelationshipName = $parentResourceRegistration->getInverseRelationshipName();

        return match (true) {
            $parentRelationship instanceof MorphTo => $query->whereMorphedTo(
                $parentRelationshipName,
                $parentRecord,
            ),
            $parentRelationship instanceof BelongsTo => $query->whereBelongsTo(
                $parentRecord,
                $parentRelationshipName,
            ),
            default => $query->whereHas(
                $parentRelationshipName,
                fn (Builder $query) => $query->whereKey($parentRecord->getKey()),
            ),
        };
    }
}
