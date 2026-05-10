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
use Filament\Tests\Fixtures\Pages\AuthorizableSettings;
use Filament\Tests\Fixtures\Pages\AutofocusBasicBrowserTest;
use Filament\Tests\Fixtures\Pages\AutofocusBrowserTest;
use Filament\Tests\Fixtures\Pages\AutofocusSecondTabBrowserTest;
use Filament\Tests\Fixtures\Pages\AutofocusWizardBrowserTest;
use Filament\Tests\Fixtures\Pages\BuilderTest;
use Filament\Tests\Fixtures\Pages\CalloutBrowserTest;
use Filament\Tests\Fixtures\Pages\CheckboxListTest;
use Filament\Tests\Fixtures\Pages\CheckboxTest;
use Filament\Tests\Fixtures\Pages\CodeEditorBrowserTest;
use Filament\Tests\Fixtures\Pages\ColorPickerTest;
use Filament\Tests\Fixtures\Pages\ColumnsBrowserTest;
use Filament\Tests\Fixtures\Pages\DatePickerBrowserTest;
use Filament\Tests\Fixtures\Pages\DateTimePickerTest;
use Filament\Tests\Fixtures\Pages\FileUploadBrowserTest;
use Filament\Tests\Fixtures\Pages\InfolistEntriesBrowserTest;
use Filament\Tests\Fixtures\Pages\KeyValueTest;
use Filament\Tests\Fixtures\Pages\ManageSiteSettings;
use Filament\Tests\Fixtures\Pages\MarkdownEditorBrowserTest;
use Filament\Tests\Fixtures\Pages\OneTimeCodeInputBrowserTest;
use Filament\Tests\Fixtures\Pages\PartialRenderingTest;
use Filament\Tests\Fixtures\Pages\QueryBuilderTableTest;
use Filament\Tests\Fixtures\Pages\RadioTest;
use Filament\Tests\Fixtures\Pages\RepeaterTest;
use Filament\Tests\Fixtures\Pages\RichEditorBrowserTest;
use Filament\Tests\Fixtures\Pages\SectionBrowserTest;
use Filament\Tests\Fixtures\Pages\SelectTest;
use Filament\Tests\Fixtures\Pages\Settings;
use Filament\Tests\Fixtures\Pages\SliderBrowserTest;
use Filament\Tests\Fixtures\Pages\TabsBrowserTest;
use Filament\Tests\Fixtures\Pages\TagsInputTest;
use Filament\Tests\Fixtures\Pages\TextareaTest;
use Filament\Tests\Fixtures\Pages\TextInputTest;
use Filament\Tests\Fixtures\Pages\TimePickerBrowserTest;
use Filament\Tests\Fixtures\Pages\ToggleButtonsTest;
use Filament\Tests\Fixtures\Pages\ToggleTest;
use Filament\Tests\Fixtures\Pages\WizardBrowserTest;
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
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
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
                AuthorizableSettings::class,
                AutofocusBasicBrowserTest::class,
                AutofocusBrowserTest::class,
                AutofocusSecondTabBrowserTest::class,
                AutofocusWizardBrowserTest::class,
                BuilderTest::class,
                CalloutBrowserTest::class,
                CheckboxListTest::class,
                CodeEditorBrowserTest::class,
                ColumnsBrowserTest::class,
                CheckboxTest::class,
                ColorPickerTest::class,
                DatePickerBrowserTest::class,
                DateTimePickerTest::class,
                FileUploadBrowserTest::class,
                InfolistEntriesBrowserTest::class,
                KeyValueTest::class,
                ManageSiteSettings::class,
                MarkdownEditorBrowserTest::class,
                OneTimeCodeInputBrowserTest::class,
                PartialRenderingTest::class,
                QueryBuilderTableTest::class,
                RadioTest::class,
                RepeaterTest::class,
                RichEditorBrowserTest::class,
                SectionBrowserTest::class,
                SelectTest::class,
                Settings::class,
                SliderBrowserTest::class,
                TabsBrowserTest::class,
                TagsInputTest::class,
                TextareaTest::class,
                TextInputTest::class,
                TimePickerBrowserTest::class,
                ToggleButtonsTest::class,
                ToggleTest::class,
                WizardBrowserTest::class,
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
                class_exists(PreventRequestForgery::class) ? PreventRequestForgery::class : VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
