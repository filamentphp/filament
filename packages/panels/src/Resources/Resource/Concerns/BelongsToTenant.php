<?php

namespace Filament\Resources\Resource\Concerns;

use Exception;
use Filament\Facades\Filament;
use Filament\Panel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Relation;

trait BelongsToTenant
{
    protected static bool $isScopedToTenant = true;

    protected static ?string $tenantOwnershipRelationshipName = null;

    protected static ?string $tenantRelationshipName = null;

    public static function scopeEloquentQueryToTenant(Builder $query, ?Model $tenant): Builder
    {
        $tenant ??= Filament::getTenant();

        $tenantOwnershipRelationship = static::getTenantOwnershipRelationship($query->getModel());
        $tenantOwnershipRelationshipName = static::getTenantOwnershipRelationshipName();

        return match (true) {
            $tenantOwnershipRelationship instanceof MorphTo => $query->whereMorphedTo(
                $tenantOwnershipRelationshipName,
                $tenant,
            ),
            $tenantOwnershipRelationship instanceof BelongsTo => $query->whereBelongsTo(
                $tenant,
                $tenantOwnershipRelationshipName,
            ),
            default => $query->whereHas(
                $tenantOwnershipRelationshipName,
                fn (Builder $query) => $query->whereKey($tenant->getKey()),
            ),
        };
    }

    public static function isTenantSubscriptionRequired(Panel $panel): bool
    {
        return $panel->isTenantSubscriptionRequired();
    }

    public static function scopeToTenant(bool $condition = true): void
    {
        static::$isScopedToTenant = $condition;
    }

    public static function isScopedToTenant(): bool
    {
        return static::$isScopedToTenant;
    }

    public static function getTenantOwnershipRelationshipName(): string
    {
        return static::$tenantOwnershipRelationshipName ?? Filament::getTenantOwnershipRelationshipName();
    }

    public static function getTenantOwnershipRelationship(Model $record): Relation
    {
        $relationshipName = static::getTenantOwnershipRelationshipName();

        if (! $record->isRelation($relationshipName)) {
            $resourceClass = static::class;
            $recordClass = $record::class;

            throw new Exception("The model [{$recordClass}] does not have a relationship named [{$relationshipName}]. You can change the relationship being used by passing it to the [ownershipRelationship] argument of the [tenant()] method in configuration. You can change the relationship being used per-resource by setting it as the [\$tenantOwnershipRelationshipName] static property on the [{$resourceClass}] resource class.");
        }

        return $record->{$relationshipName}();
    }

    public static function getTenantRelationshipName(): string
    {
        return static::$tenantRelationshipName ?? (string) str(static::getModel())
            ->classBasename()
            ->pluralStudly()
            ->camel();
    }

    public static function getTenantRelationship(Model $tenant): Relation
    {
        $relationshipName = static::getTenantRelationshipName();

        if (! $tenant->isRelation($relationshipName)) {
            $resourceClass = static::class;
            $tenantClass = $tenant::class;

            throw new Exception("The model [{$tenantClass}] does not have a relationship named [{$relationshipName}]. You can change the relationship being used by setting it as the [\$tenantRelationshipName] static property on the [{$resourceClass}] resource class.");
        }

        return $tenant->{$relationshipName}();
    }

    public static function registerTenancyModelGlobalScope(Panel $panel): void
    {
        if (! static::isScopedToTenant()) {
            return;
        }

        $model = static::getModel();

        if (! class_exists($model)) {
            return;
        }

        if ($model::hasGlobalScope($panel->getTenancyScopeName())) {
            return;
        }

        $model::addGlobalScope($panel->getTenancyScopeName(), function (Builder $query) use ($panel): void {
            if (Filament::getCurrentOrDefaultPanel() !== $panel) {
                return;
            }

            $tenant = Filament::getTenant();

            if (! $tenant) {
                return;
            }

            static::scopeEloquentQueryToTenant($query, $tenant);
        });
    }

    public static function observeTenancyModelCreation(Panel $panel): void
    {
        if (! static::isScopedToTenant()) {
            return;
        }

        $model = static::getModel();

        if (! class_exists($model)) {
            return;
        }

        $model::creating(function (Model $record) use ($panel): void {
            if (Filament::getCurrentOrDefaultPanel() !== $panel) {
                return;
            }

            $tenant = Filament::getTenant();

            if (! $tenant) {
                return;
            }

            $relationship = static::getTenantOwnershipRelationship($record);

            if ($relationship instanceof BelongsTo) {
                $relationship->associate($tenant);
            }
        });

        $model::created(function (Model $record) use ($panel): void {
            if (Filament::getCurrentOrDefaultPanel() !== $panel) {
                return;
            }

            $tenant = Filament::getTenant();

            if (! $tenant) {
                return;
            }

            $relationship = static::getTenantOwnershipRelationship($record);

            if ($relationship instanceof BelongsTo) {
                return;
            }

            $relationship->save($tenant); /** @phpstan-ignore-line */
        });
    }
}
