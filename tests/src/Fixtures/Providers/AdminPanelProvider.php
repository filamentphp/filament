<?php

namespace Filament\Tests\Fixtures\Providers;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Tests\Fixtures\Clusters\UserManagement;
use Filament\Tests\Fixtures\Clusters\UserManagement\Pages\GeneralSettings;
use Filament\Tests\Fixtures\Clusters\UserManagement\Pages\ManageAdmins;
use Filament\Tests\Fixtures\Clusters\UserManagement\Pages\ManageStaff;
use Filament\Tests\Fixtures\Pages\Actions;
use Filament\Tests\Fixtures\Pages\Settings;
use Filament\Tests\Fixtures\Resources\Companies\CompanyResource;
use Filament\Tests\Fixtures\Resources\Companies\Resources\CompanyTeamResource;
use Filament\Tests\Fixtures\Resources\Departments\DepartmentResource;
use Filament\Tests\Fixtures\Resources\PostCategories\PostCategoryResource;
use Filament\Tests\Fixtures\Resources\Posts\PostResource;
use Filament\Tests\Fixtures\Resources\Shop\Products\ProductResource;
use Filament\Tests\Fixtures\Resources\TicketMessages\TicketMessageResource;
use Filament\Tests\Fixtures\Resources\Tickets\TicketResource;
use Filament\Tests\Fixtures\Resources\Users\Resources\UserPostResource;
use Filament\Tests\Fixtures\Resources\Users\UserResource;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->login()
            ->registration()
            ->passwordReset()
            ->emailChangeVerification()
            ->emailVerification()
            ->profile()
            ->resources([
                CompanyResource::class,
                CompanyTeamResource::class,
                DepartmentResource::class,
                PostResource::class,
                PostCategoryResource::class,
                ProductResource::class,
                TicketResource::class,
                TicketMessageResource::class,
                UserResource::class,
                UserPostResource::class,
            ])
            ->pages([
                Pages\Dashboard::class,
                Actions::class,
                Settings::class,
                UserManagement::class,
                ManageAdmins::class,
                ManageStaff::class,
                GeneralSettings::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
