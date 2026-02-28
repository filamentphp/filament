<?php

namespace Filament\Resources\Resource\Concerns;

use Filament\Resources\ParentResourceRegistration;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @template TModel of Model = Model
 */
trait BelongsToParent
{
    /**
     * @var class-string|null
     */
    protected static ?string $parentResource = null;

    public static function getParentResource(): ?string
    {
        return static::$parentResource;
    }

    public static function asParent(?string $childResource = null): ParentResourceRegistration
    {
        return new ParentResourceRegistration(static::class, $childResource);
    }

    public static function getParentResourceRegistration(): ?ParentResourceRegistration
    {
        $parentResource = static::getParentResource();

        if (is_string($parentResource)) {
            $parentResource = $parentResource::asParent(childResource: static::class);
        }

        return $parentResource;
    }

    /**
     * @param  Builder<TModel>  $query
     * @return Builder<TModel>
     */
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
