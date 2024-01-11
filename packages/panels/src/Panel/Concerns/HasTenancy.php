<?php

namespace Filament\Panel\Concerns;

use Closure;
use Filament\Billing\Providers\Contracts\Provider as BillingProvider;
use Filament\Navigation\MenuItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;

trait HasTenancy
{
    protected ?BillingProvider $tenantBillingProvider = null;

    protected string $tenantBillingRouteSlug = 'billing';

    protected bool | Closure $hasTenantMenu = true;

    protected ?string $tenantRoutePrefix = null;

    protected ?string $tenantModel = null;

    protected ?string $tenantProfilePage = null;

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
        $this->tenantMenuItems = [
            ...$this->tenantMenuItems,
            ...$items,
        ];

        return $this;
    }

    public function tenantMenu(bool | Closure $condition = true): static
    {
        $this->hasTenantMenu = $condition;

        return $this;
    }

    public function tenant(?string $model, ?string $slugAttribute = null, ?string $ownershipRelationship = null): static
    {
        $this->tenantModel = $model;
        $this->tenantSlugAttribute = $slugAttribute;
        $this->tenantOwnershipRelationshipName = $ownershipRelationship;

        return $this;
    }

    public function tenantRoutePrefix(?string $prefix): static
    {
        $this->tenantRoutePrefix = $prefix;

        return $this;
    }

    public function tenantBillingProvider(?BillingProvider $provider): static
    {
        $this->tenantBillingProvider = $provider;

        return $this;
    }

    public function tenantBillingRouteSlug(string $slug): static
    {
        $this->tenantBillingRouteSlug = $slug;

        return $this;
    }

    public function tenantProfile(?string $page): static
    {
        $this->tenantProfilePage = $page;

        return $this;
    }

    public function tenantRegistration(?string $page): static
    {
        $this->tenantRegistrationPage = $page;

        return $this;
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

    public function hasTenantProfile(): bool
    {
        return filled($this->getTenantProfilePage());
    }

    public function hasTenantRegistration(): bool
    {
        return filled($this->getTenantRegistrationPage());
    }

    public function hasTenantRoutePrefix(): bool
    {
        return filled($this->getTenantRoutePrefix());
    }

    public function getTenantRoutePrefix(): ?string
    {
        return $this->tenantRoutePrefix;
    }

    public function getTenantBillingProvider(): ?BillingProvider
    {
        return $this->tenantBillingProvider;
    }

    public function getTenantBillingRouteSlug(): string
    {
        return Str::start($this->tenantBillingRouteSlug, '/');
    }

    public function getTenantProfilePage(): ?string
    {
        return $this->tenantProfilePage;
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

    /**
     * @param  array<mixed>  $parameters
     */
    public function getTenantBillingUrl(Model $tenant, array $parameters = []): ?string
    {
        if (! $this->hasTenantBilling()) {
            return null;
        }

        return $this->route('tenant.billing', [
            'tenant' => $tenant,
            ...$parameters,
        ]);
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getTenantProfileUrl(array $parameters = []): ?string
    {
        if (! $this->hasTenantProfile()) {
            return null;
        }

        return $this->route('tenant.profile', $parameters);
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getTenantRegistrationUrl(array $parameters = []): ?string
    {
        if (! $this->hasTenantRegistration()) {
            return null;
        }

        return $this->route('tenant.registration', $parameters);
    }

    public function hasTenantMenu(): bool
    {
        return (bool) $this->evaluate($this->hasTenantMenu);
    }

    /**
     * @return array<MenuItem>
     */
    public function getTenantMenuItems(): array
    {
        return collect($this->tenantMenuItems)
            ->filter(function (MenuItem $item, string | int $key): bool {
                if (in_array($key, ['billing', 'profile', 'register'])) {
                    return true;
                }

                return $item->isVisible();
            })
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
