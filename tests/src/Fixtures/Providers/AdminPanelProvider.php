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
use Filament\Tests\Fixtures\Clusters\WithoutSubNavigationCluster;
use Filament\Tests\Fixtures\Clusters\WithoutSubNavigationCluster\Pages\ClusteredPageWithoutSubNavigation;
use Filament\Tests\Fixtures\Pages\Actions;
use Filament\Tests\Fixtures\Pages\AfterStateUpdatedJsTest;
use Filament\Tests\Fixtures\Pages\AutofocusBasicBrowserTest;
use Filament\Tests\Fixtures\Pages\AutofocusBrowserTest;
use Filament\Tests\Fixtures\Pages\AutofocusSecondTabBrowserTest;
use Filament\Tests\Fixtures\Pages\AutofocusWizardBrowserTest;
use Filament\Tests\Fixtures\Pages\BuilderTest;
use Filament\Tests\Fixtures\Pages\CalloutBrowserTest;
use Filament\Tests\Fixtures\Pages\KeyValueTest;
use Filament\Tests\Fixtures\Pages\RepeaterTest;
use Filament\Tests\Fixtures\Pages\SelectTest;
use Filament\Tests\Fixtures\Pages\Settings;
use Filament\Tests\Fixtures\Pages\TagsInputTest;
use Filament\Tests\Fixtures\Pages\ToggleTest;
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
                AfterStateUpdatedJsTest::class,
                AutofocusBasicBrowserTest::class,
                AutofocusBrowserTest::class,
                AutofocusSecondTabBrowserTest::class,
                AutofocusWizardBrowserTest::class,
                BuilderTest::class,
                CalloutBrowserTest::class,
                KeyValueTest::class,
                RepeaterTest::class,
                SelectTest::class,
                Settings::class,
                TagsInputTest::class,
                ToggleTest::class,
                UserManagement::class,
                ManageAdmins::class,
                ManageStaff::class,
                GeneralSettings::class,
                WithoutSubNavigationCluster::class,
                ClusteredPageWithoutSubNavigation::class,
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
