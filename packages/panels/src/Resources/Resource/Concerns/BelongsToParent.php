<?php

namespace Filament\Resources\Resource\Concerns;

use Filament\Resources\ParentResourceRegistration;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

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
        $relationship = static::getParentResourceRegistration()->getRelationship($parentRecord);

        return $query->whereIn(
            $query->getModel()->getQualifiedKeyName(),
            $relationship->getQuery()
                ->toBase()
                ->reorder()
                ->select($relationship->getRelated()->getQualifiedKeyName()),
        );
    }
}
