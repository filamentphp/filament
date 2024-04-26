<?php

namespace Filament\Resources;

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

    public function getChildResource(): ?string
    {
        return $this->childResource;
    }

    public function getRelationshipName(): ?string
    {
        return $this->relationshipName;
    }

    public function getInverseRelationshipName(): ?string
    {
        return $this->inverseRelationshipName;
    }
}
