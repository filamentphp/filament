<?php

namespace Filament\Context\Concerns;

use Filament\Billing\Providers\Contracts\Provider as BillingProvider;
use Filament\Navigation\MenuItem;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait HasTenancy
{
    protected ?BillingProvider $tenantBillingProvider = null;

    protected ?string $tenantModel = null;

    protected ?string $tenantRegistrationPage = null;

    protected ?string $tenantSlugAttribute = null;

    protected ?string $tenantOwnershipRelationshipName = null;

    /**
     * @var array<MenuItem>
     */
    protected array $tenantMenuItems = [];

    protected bool $isTenantSubscriptionRequired = false;

    public function requiresTenantSubscription(bool $condition = true): static
    {
        $this->isTenantSubscriptionRequired = $condition;

        return $this;
    }

    /**
     * @param  array<MenuItem>  $items
     */
    public function tenantMenuItems(array $items): static
    {
        $this->tenantMenuItems = array_merge($this->tenantMenuItems, $items);

        return $this;
    }

    public function tenant(?string $model, ?string $slugAttribute = null, ?string $ownershipRelationship = null): static
    {
        $this->tenantModel = $model;
        $this->tenantSlugAttribute = $slugAttribute;
        $this->tenantOwnershipRelationshipName = $ownershipRelationship;

        return $this;
    }

    public function tenantBillingProvider(?BillingProvider $provider): static
    {
        $this->tenantBillingProvider = $provider;

        return $this;
    }

    public function tenantRegistration(?string $page): static
    {
        $this->tenantRegistrationPage = $page;

        return $this;
    }

    public function hasRoutableTenancy(): bool
    {
        /** @var EloquentUserProvider $userProvider */
        $userProvider = $this->auth()->getProvider();

        return $this->hasTenancy() && ($userProvider->getModel() !== $this->getTenantModel());
    }

    public function hasTenancy(): bool
    {
        return filled($this->getTenantModel());
    }

    public function isTenantSubscriptionRequired(): bool
    {
        return $this->isTenantSubscriptionRequired;
    }

    public function hasTenantBilling(): bool
    {
        return filled($this->getTenantBillingProvider());
    }

    public function hasTenantRegistration(): bool
    {
        return filled($this->getTenantRegistrationPage());
    }

    public function getTenantBillingProvider(): ?BillingProvider
    {
        return $this->tenantBillingProvider;
    }

    public function getTenantRegistrationPage(): ?string
    {
        return $this->tenantRegistrationPage;
    }

    public function getTenant(string $key): Model
    {
        $tenantModel = $this->getTenantModel();

        $record = app($tenantModel)
            ->resolveRouteBinding($key, $this->getTenantSlugAttribute());

        if ($record === null) {
            throw (new ModelNotFoundException())->setModel($tenantModel, [$key]);
        }

        return $record;
    }

    public function getTenantModel(): ?string
    {
        return $this->tenantModel;
    }

    public function getTenantSlugAttribute(): ?string
    {
        return $this->tenantSlugAttribute;
    }

    public function getTenantBillingUrl(Model $tenant): ?string
    {
        if (! $this->hasTenantBilling()) {
            return null;
        }

        return route("filament.{$this->getId()}.tenant.billing", [
            'tenant' => $tenant,
        ]);
    }

    public function getTenantRegistrationUrl(): ?string
    {
        if (! $this->hasTenantRegistration()) {
            return null;
        }

        return route("filament.{$this->getId()}.tenant.registration");
    }

    /**
     * @return array<MenuItem>
     */
    public function getTenantMenuItems(): array
    {
        return collect($this->tenantMenuItems)
            ->sort(fn (MenuItem $item): int => $item->getSort())
            ->all();
    }

    public function getTenantOwnershipRelationshipName(): string
    {
        if (filled($this->tenantOwnershipRelationshipName)) {
            return $this->tenantOwnershipRelationshipName;
        }

        return (string) str($this->getTenantModel())
            ->classBasename()
            ->camel();
    }
}
