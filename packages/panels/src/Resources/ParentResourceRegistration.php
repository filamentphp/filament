<?php

namespace Filament\Resources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Support\Str;

class ParentResourceRegistration
{
    public function __construct(
        protected string $parentResource,
        protected ?string $childResource = null,
        protected ?string $relationshipName = null,
        protected ?string $inverseRelationshipName = null,
    ) {
        $this->childResource ??= debug_backtrace(limit: 3)[2]['class'];
        $this->relationshipName ??= (string) str($this->childResource::getModel())
            ->classBasename()
            ->camel()
            ->plural();
        $this->inverseRelationshipName ??= (string) str($this->parentResource::getModel())
            ->classBasename()
            ->camel();
    }

    public function getParentResource(): string
    {
        return $this->parentResource;
    }

    public function getChildResource(): string
    {
        return $this->childResource;
    }

    public function getRelationship(Model $parentRecord): HasOneOrMany | BelongsToMany
    {
        return $parentRecord->{$this->getRelationshipName()}();
    }

    public function getInverseRelationship(Model $parentRecord): BelongsTo | BelongsToMany
    {
        return $parentRecord->{$this->getInverseRelationshipName()}();
    }

    public function getRelationshipName(): string
    {
        return $this->relationshipName;
    }

    public function getInverseRelationshipName(): string
    {
        return $this->inverseRelationshipName;
    }

    public function getParentRouteParameterName(): string
    {
        return Str::kebab($this->inverseRelationshipName);
    }

    public function getSlug(): string
    {
        return Str::slug($this->relationshipName);
    }

    public function getRouteName(): string
    {
        return Str::kebab($this->relationshipName);
    }
}
