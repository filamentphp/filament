<?php

namespace Filament\Queue\Middleware;

use Closure;
use Filament\Actions\Imports\Jobs\ImportCsv;
use Filament\Facades\Filament;
use Filament\Models\Contracts\HasTenants;
use Illuminate\Auth\Access\AuthorizationException;
use LogicException;
use Throwable;

class RestoreImportTenant
{
    public function __construct(
        protected string $panelId,
        protected int | string $tenantId,
    ) {}

    public function handle(ImportCsv $job, Closure $next): mixed
    {
        $previousPanel = Filament::getCurrentPanel();
        $previousTenant = Filament::getTenant();
        $panel = Filament::getPanels()[$this->panelId] ?? throw new LogicException("Panel [{$this->panelId}] no longer exists.");
        $tenantModel = $panel->getTenantModel() ?? throw new LogicException("Panel [{$this->panelId}] no longer has tenancy enabled.");
        $guards = [];
        $previousUsers = [];

        foreach (array_unique([auth()->getDefaultDriver(), $panel->getAuthGuard()]) as $guardName) {
            $guards[$guardName] = auth()->guard($guardName);
            $previousUsers[$guardName] = $guards[$guardName]->user();

            if ((! $previousUsers[$guardName]) && (! method_exists($guards[$guardName], 'forgetUser'))) {
                throw new LogicException("Auth guard [{$guardName}] must implement [forgetUser()] to restore its guest state after importing.");
            }
        }

        try {
            Filament::setTenant(null, isQuiet: true);
            Filament::setCurrentPanel($panel);

            $user = $job->getImport()->user;

            if (! $user instanceof HasTenants) {
                throw new AuthorizationException;
            }

            foreach ($guards as $guard) {
                $guard->setUser($user);
            }

            $tenant = $tenantModel::query()->findOrFail($this->tenantId);

            if (! $user->canAccessTenant($tenant)) {
                throw new AuthorizationException;
            }

            $panel->bootTenancy();
            Filament::setTenant($tenant);

            return $next($job);
        } finally {
            Filament::setTenant($previousTenant, isQuiet: true);
            Filament::setCurrentPanel($previousPanel);
            $restorationException = null;

            foreach ($previousUsers as $guardName => $user) {
                try {
                    if ($user) {
                        $guards[$guardName]->setUser($user);
                    } else {
                        $guards[$guardName]->forgetUser();
                    }
                } catch (Throwable $exception) {
                    $restorationException ??= $exception;
                }
            }

            if ($restorationException) {
                throw $restorationException;
            }
        }
    }
}
