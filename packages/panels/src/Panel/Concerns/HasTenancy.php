<?php

namespace Filament\Panel\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Billing\Providers\Contracts\BillingProvider;
use Filament\Facades\Filament;
use Filament\Navigation\MenuItem;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Filament\View\PanelsIconAlias;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait HasTenancy
{
    protected ?BillingProvider $tenantBillingProvider = null;

    protected string $tenantBillingRouteSlug = 'billing';

    protected bool | Closure $hasTenantMenu = true;

    protected bool | Closure | null $isTenantMenuSearchable = null;

    protected ?string $tenantRoutePrefix = null;

    protected ?string $tenantDomain = null;

    /**
     * @var class-string<Model>|null
     */
    protected ?string $tenantModel = null;

    protected ?string $tenantProfilePage = null;

    protected ?string $tenantRegistrationPage = null;

    protected ?string $tenantSlugAttribute = null;

    protected ?string $tenantOwnershipRelationshipName = null;

    protected ?Closure $resolveTenantUsing = null;

    /**
     * @var array<Action | Closure | MenuItem>
     */
    protected array $tenantMenuItems = [];

    protected bool $isTenantSubscriptionRequired = false;

    public function requiresTenantSubscription(bool $condition = true): static
    {
        $this->isTenantSubscriptionRequired = $condition;

        return $this;
    }

    /**
     * @param  array<Action | Closure | MenuItem>  $items
     */
    public function tenantMenuItems(array $items): static
    {
        $this->tenantMenuItems = [
            ...$this->tenantMenuItems,
            ...$items,
        ];

        return $this;
    }

    public function searchableTenantMenu(bool | Closure | null $condition = true): static
    {
        $this->isTenantMenuSearchable = $condition;

        return $this;
    }

    public function tenantMenu(bool | Closure $condition = true): static
    {
        $this->hasTenantMenu = $condition;

        return $this;
    }

    /**
     * @param  class-string<Model>|null  $model
     */
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

    public function tenantDomain(?string $domain): static
    {
        $this->tenantDomain = $domain;

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

    public function resolveTenantUsing(?Closure $callback): static
    {
        $this->resolveTenantUsing = $callback;

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

    public function hasTenantDomain(): bool
    {
        return filled($this->getTenantDomain());
    }

    public function getTenantDomain(): ?string
    {
        return $this->tenantDomain;
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
        if ($this->resolveTenantUsing) {
            return $this->evaluate($this->resolveTenantUsing, [
                'key' => $key,
            ]);
        }

        $tenantModel = $this->getTenantModel();

        $record = app($tenantModel)
            ->resolveRouteBinding($key, $this->getTenantSlugAttribute());

        if ($record === null) {
            throw (new ModelNotFoundException)->setModel($tenantModel, [$key]);
        }

        return $record;
    }

    /**
     * @return class-string<Model>|null
     */
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

    protected function getTenantProfileMenuItem(Action | Closure | MenuItem | null $item = null): Action
    {
        $currentTenant = Filament::getTenant();

        $page = Filament::getTenantProfilePage();

        $action = Action::make('profile')
            ->label($page ? $page::getLabel() : Filament::getTenantName($currentTenant))
            ->icon(FilamentIcon::resolve(PanelsIconAlias::TENANT_MENU_PROFILE_BUTTON) ?? Heroicon::Cog6Tooth)
            ->url($url = Filament::getTenantProfileUrl())
            ->visible(filament()->hasTenantProfile() && filled($url) && (blank($page) || $page::canView($currentTenant)))
            ->sort(-2);

        if ($item instanceof MenuItem) {
            return $item->toAction($action);
        }

        return $this->evaluate($item, [
            'action' => $action,
        ]) ?? $action;
    }

    protected function getTenantBillingMenuItem(Action | Closure | MenuItem | null $item = null): Action
    {
        $action = Action::make('billing')
            ->label(__('filament-panels::layout.actions.billing.label'))
            ->color('gray')
            ->icon(FilamentIcon::resolve(PanelsIconAlias::TENANT_MENU_BILLING_BUTTON) ?? Heroicon::CreditCard)
            ->url($url = Filament::getTenantBillingUrl())
            ->visible(filament()->hasTenantBilling() && filled($url))
            ->sort(-1);

        if ($item instanceof MenuItem) {
            return $item->toAction($action);
        }

        return $this->evaluate($item, [
            'action' => $action,
        ]) ?? $action;
    }

    protected function getTenantRegistrationMenuItem(Action | Closure | MenuItem | null $item = null): Action
    {
        $page = Filament::getTenantRegistrationPage();

        $action = Action::make('register')
            ->label($page ? $page::getLabel() : null)
            ->icon(FilamentIcon::resolve(PanelsIconAlias::TENANT_MENU_REGISTRATION_BUTTON) ?? Heroicon::Plus)
            ->url($url = Filament::getTenantRegistrationUrl())
            ->visible(filament()->hasTenantRegistration() && filled($url) && (blank($page) || $page::canView(Filament::getTenant())))
            ->sort(PHP_INT_MAX);

        if ($item instanceof MenuItem) {
            return $item->toAction($action);
        }

        return $this->evaluate($item, [
            'action' => $action,
        ]) ?? $action;
    }

    public function isTenantMenuSearchable(): ?bool
    {
        return $this->evaluate($this->isTenantMenuSearchable);
    }

    /**
     * @return array<Action>
     */
    public function getTenantMenuItems(): array
    {
        return collect($this->tenantMenuItems)
            ->mapWithKeys(function (Action | Closure | MenuItem $item, int | string $key): array {
                if ($item instanceof Action) {
                    return [$item->getName() => $item];
                }

                if ($key === 'profile') {
                    return ['profile' => $this->getTenantProfileMenuItem($item)];
                }

                if ($key === 'billing') {
                    return ['billing' => $this->getTenantBillingMenuItem($item)];
                }

                if ($key === 'register') {
                    return ['register' => $this->getTenantRegistrationMenuItem($item)];
                }

                $action = $this->evaluate($item);

                if ($action instanceof MenuItem) {
                    $action = $action->toAction();
                }

                return [$action->getName() => $action];
            })
            ->when(
                fn (Collection $items): bool => ! $items->has('profile'),
                fn (Collection $items): Collection => $items->put('profile', $this->getTenantProfileMenuItem()),
            )
            ->when(
                fn (Collection $items): bool => ! $items->has('billing'),
                fn (Collection $items): Collection => $items->put('billing', $this->getTenantBillingMenuItem()),
            )
            ->when(
                fn (Collection $items): bool => ! $items->has('register'),
                fn (Collection $items): Collection => $items->put('register', $this->getTenantRegistrationMenuItem()),
            )
            ->filter(fn (Action $item): bool => $item->isVisible())
            ->sortBy(fn (Action $item): int => $item->getSort())
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

    public function getTenancyScopeName(): string
    {
        return "{$this->getId()}_tenancy";
    }
}
