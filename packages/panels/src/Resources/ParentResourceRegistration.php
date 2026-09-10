<?php

namespace Filament\Resources;

use Filament\Resources\Pages\ManageRelatedRecords;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

class ParentResourceRegistration
{
    protected ?string $pageName = null;

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
            ->camel()
            ->when(
                function (Stringable $singularRelationshipName): bool {
                    $model = $this->childResource::getModel();

                    if (method_exists($model, $singularRelationshipName)) {
                        return false;
                    }

                    return method_exists($model, $singularRelationshipName->plural());
                },
                fn (Stringable $singularRelationshipName): Stringable => $singularRelationshipName->plural(),
            );
    }

    public function relationship(string $name): static
    {
        $this->relationshipName = $name;

        return $this;
    }

    public function inverseRelationship(string $name): static
    {
        $this->inverseRelationshipName = $name;

        return $this;
    }

    /**
     * Set the parent resource page name used as the nested resource index URL.
     *
     * By default, Filament looks for a page keyed like the relationship name
     * (for example `lessons`). Use this when the relation page is registered
     * under a different key (for example `manageLessons`).
     */
    public function page(string $name): static
    {
        $this->pageName = $name;

        return $this;
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
        return (string) str($this->inverseRelationshipName)
            ->singular()
            ->snake();
    }

    public function getRouteName(): string
    {
        return Str::kebab($this->relationshipName);
    }

    public function getPageName(): ?string
    {
        return $this->pageName;
    }

    /**
     * Resolve which parent page should be used as the nested resource index.
     *
     * Order: explicit `page()` → page keyed like the relationship → first
     * `ManageRelatedRecords` page whose relationship matches.
     */
    public function resolveRelationshipPageName(): ?string
    {
        if (filled($this->pageName)) {
            return $this->pageName;
        }

        $parentResource = $this->getParentResource();
        $relationshipPageName = $this->getRouteName();

        if ($parentResource::hasPage($relationshipPageName)) {
            return $relationshipPageName;
        }

        foreach ($parentResource::getPages() as $name => $page) {
            $pageClass = $page->getPage();

            if (! is_subclass_of($pageClass, ManageRelatedRecords::class)) {
                continue;
            }

            if ($pageClass::getRelationshipName() !== $this->getRelationshipName()) {
                continue;
            }

            return $name;
        }

        return null;
    }
}
