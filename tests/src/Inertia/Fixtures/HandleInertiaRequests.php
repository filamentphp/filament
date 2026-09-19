<?php

namespace Filament\Tests\Inertia\Fixtures;

use Filament\Facades\Filament;
use Filament\Tests\Fixtures\Resources\Tenancy\TenantScopedUsers\TenantScopedUserResource;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Symfony\Component\HttpFoundation\Response;

class HandleInertiaRequests extends Middleware
{
    public function share(Request $request): array
    {
        RequestState::$shares++;

        return [
            ...parent::share($request),
            'sharedLabel' => $request->header('X-Test-Label'),
            'sessionAvailable' => $request->hasSession(),
            'sharedUser' => $request->user()?->getAuthIdentifier(),
            'sharedTenant' => Filament::getTenant()?->getKey(),
            'sharedRecords' => Filament::getCurrentPanel()?->getId() === 'inertia-tenancy-tests'
                ? TenantScopedUserResource::getEloquentQuery()->orderBy('id')->pluck('email')->all()
                : [],
        ];
    }

    public function version(Request $request): ?string
    {
        return 'fixture-version';
    }

    public function terminate(Request $request, Response $response): void
    {
        RequestState::$terminations++;
    }
}
