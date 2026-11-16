<?php

namespace Filament\Tests\Fixtures\Providers;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Tables\View\TablesRenderHook;
use Filament\Tests\Fixtures\Clusters\UserManagement;
use Filament\Tests\Fixtures\Clusters\UserManagement\Pages\GeneralSettings;
use Filament\Tests\Fixtures\Clusters\UserManagement\Pages\ManageAdmins;
use Filament\Tests\Fixtures\Clusters\UserManagement\Pages\ManageStaff;
use Filament\Tests\Fixtures\Clusters\WithoutSubNavigationCluster;
use Filament\Tests\Fixtures\Clusters\WithoutSubNavigationCluster\Pages\ClusteredPageWithoutSubNavigation;
use Filament\Tests\Fixtures\Pages\Actions;
use Filament\Tests\Fixtures\Pages\AfterStateUpdatedJsBrowserTest;
use Filament\Tests\Fixtures\Pages\AuthorizableSettings;
use Filament\Tests\Fixtures\Pages\AutofocusAfterCreateAnotherTabsModalBrowserTest;
use Filament\Tests\Fixtures\Pages\AutofocusBasicBrowserTest;
use Filament\Tests\Fixtures\Pages\AutofocusBrowserTest;
use Filament\Tests\Fixtures\Pages\AutofocusSecondTabBrowserTest;
use Filament\Tests\Fixtures\Pages\AutofocusWizardBrowserTest;
use Filament\Tests\Fixtures\Pages\BuilderBrowserTest;
use Filament\Tests\Fixtures\Pages\BuilderSearchableBrowserTest;
use Filament\Tests\Fixtures\Pages\CalloutBrowserTest;
use Filament\Tests\Fixtures\Pages\ChartWidgetBrowserTest;
use Filament\Tests\Fixtures\Pages\CheckboxBrowserTest;
use Filament\Tests\Fixtures\Pages\CheckboxListBrowserTest;
use Filament\Tests\Fixtures\Pages\CodeEditorBrowserTest;
use Filament\Tests\Fixtures\Pages\ColorPickerBrowserTest;
use Filament\Tests\Fixtures\Pages\ColumnManagerBrowserTest;
use Filament\Tests\Fixtures\Pages\ColumnManagerReorderingBrowserTest;
use Filament\Tests\Fixtures\Pages\ColumnsBrowserTest;
use Filament\Tests\Fixtures\Pages\DatabaseNotificationsBrowserTest;
use Filament\Tests\Fixtures\Pages\DatePickerBrowserTest;
use Filament\Tests\Fixtures\Pages\DateTimePickerBrowserTest;
use Filament\Tests\Fixtures\Pages\DissociateBulkActionBrowserTest;
use Filament\Tests\Fixtures\Pages\DropdownBrowserTest;
use Filament\Tests\Fixtures\Pages\FieldAccessibilityBrowserTest;
use Filament\Tests\Fixtures\Pages\FileUploadBrowserTest;
use Filament\Tests\Fixtures\Pages\FiltersModalBrowserTest;
use Filament\Tests\Fixtures\Pages\FiltersResetActionBrowserTest;
use Filament\Tests\Fixtures\Pages\IconBrowserTest;
use Filament\Tests\Fixtures\Pages\IndistinctStateBrowserTest;
use Filament\Tests\Fixtures\Pages\IndividualColumnSearchBrowserTest;
use Filament\Tests\Fixtures\Pages\InfolistEntriesBrowserTest;
use Filament\Tests\Fixtures\Pages\JsComponentBrowserTest;
use Filament\Tests\Fixtures\Pages\JsFieldFrameworkTest;
use Filament\Tests\Fixtures\Pages\JsWidgetBrowserTest;
use Filament\Tests\Fixtures\Pages\KeyValueBrowserTest;
use Filament\Tests\Fixtures\Pages\LazyLoadedRepeatableEntryBrowserTest;
use Filament\Tests\Fixtures\Pages\LazyLoadedStateBrowserTest;
use Filament\Tests\Fixtures\Pages\LazySchemaLoadingBrowserTest;
use Filament\Tests\Fixtures\Pages\ManageSiteSettings;
use Filament\Tests\Fixtures\Pages\MarkdownEditorBrowserTest;
use Filament\Tests\Fixtures\Pages\ModalBrowserTest;
use Filament\Tests\Fixtures\Pages\NotificationBrowserTest;
use Filament\Tests\Fixtures\Pages\OneTimeCodeInputBrowserTest;
use Filament\Tests\Fixtures\Pages\OneTimeCodeInputSubmitOnCompletionBrowserTest;
use Filament\Tests\Fixtures\Pages\PartialRenderingBrowserTest;
use Filament\Tests\Fixtures\Pages\QueryBuilderTableBrowserTest;
use Filament\Tests\Fixtures\Pages\RadioBrowserTest;
use Filament\Tests\Fixtures\Pages\RepeaterBrowserTest;
use Filament\Tests\Fixtures\Pages\RichEditorBrowserTest;
use Filament\Tests\Fixtures\Pages\RichEditorMinimalControlsBrowserTest;
use Filament\Tests\Fixtures\Pages\SchemaComponentScopeBrowserTest;
use Filament\Tests\Fixtures\Pages\ScopedActionsBrowserTest;
use Filament\Tests\Fixtures\Pages\SectionBrowserTest;
use Filament\Tests\Fixtures\Pages\SelectBrowserTest;
use Filament\Tests\Fixtures\Pages\Settings;
use Filament\Tests\Fixtures\Pages\SliderBrowserTest;
use Filament\Tests\Fixtures\Pages\StatsOverviewWidgetBrowserTest;
use Filament\Tests\Fixtures\Pages\TableActionGroupsBrowserTest;
use Filament\Tests\Fixtures\Pages\TableRenderHooksBrowserTest;
use Filament\Tests\Fixtures\Pages\TableReorderingBrowserTest;
use Filament\Tests\Fixtures\Pages\TabsBrowserTest;
use Filament\Tests\Fixtures\Pages\TagsInputBrowserTest;
use Filament\Tests\Fixtures\Pages\TextareaBrowserTest;
use Filament\Tests\Fixtures\Pages\TextInputBrowserTest;
use Filament\Tests\Fixtures\Pages\TimePickerBrowserTest;
use Filament\Tests\Fixtures\Pages\ToggleBrowserTest;
use Filament\Tests\Fixtures\Pages\ToggleButtonsBrowserTest;
use Filament\Tests\Fixtures\Pages\UnsavedChangesAlertBrowserTest;
use Filament\Tests\Fixtures\Pages\WizardBrowserTest;
use Filament\Tests\Fixtures\Resources\Companies\CompanyResource;
use Filament\Tests\Fixtures\Resources\Companies\Resources\CompanyTeamResource;
use Filament\Tests\Fixtures\Resources\Departments\DepartmentResource;
use Filament\Tests\Fixtures\Resources\PostCategories\PostCategoryResource;
use Filament\Tests\Fixtures\Resources\Posts\PostResource;
use Filament\Tests\Fixtures\Resources\Shop\Products\ProductResource;
use Filament\Tests\Fixtures\Resources\TicketMessages\TicketMessageResource;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\ScopedDepartmentsRelationManager;
use Filament\Tests\Fixtures\Resources\Tickets\TicketResource;
use Filament\Tests\Fixtures\Resources\Users\Resources\UserPostResource;
use Filament\Tests\Fixtures\Resources\Users\UserResource;
use Filament\Tests\Fixtures\Widgets\ChartWidgetWithAssistiveContent;
use Filament\Tests\Fixtures\Widgets\JsWidget;
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
            ->unsavedChangesAlerts(static fn (): bool => request()->routeIs('filament.admin.pages.unsaved-changes-alert-browser-test'))
            ->livewireComponents([
                JsWidget::class,
                ScopedDepartmentsRelationManager::class,
            ])
            ->renderHook(
                TablesRenderHook::CONTENT_BEFORE,
                static fn (): string => '<div data-testid="table-content-before-hook">Before table content</div>',
                TableRenderHooksBrowserTest::class,
            )
            ->renderHook(
                TablesRenderHook::CONTENT_AFTER,
                static fn (): string => '<div data-testid="table-content-after-hook">After table content</div>',
                TableRenderHooksBrowserTest::class,
            )
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
                SchemaComponentScopeBrowserTest::class,
                ScopedActionsBrowserTest::class,
                AfterStateUpdatedJsBrowserTest::class,
                AuthorizableSettings::class,
                AutofocusAfterCreateAnotherTabsModalBrowserTest::class,
                AutofocusBasicBrowserTest::class,
                AutofocusBrowserTest::class,
                AutofocusSecondTabBrowserTest::class,
                AutofocusWizardBrowserTest::class,
                BuilderSearchableBrowserTest::class,
                BuilderBrowserTest::class,
                DropdownBrowserTest::class,
                CalloutBrowserTest::class,
                CheckboxListBrowserTest::class,
                ChartWidgetBrowserTest::class,
                CodeEditorBrowserTest::class,
                ColumnManagerBrowserTest::class,
                ColumnManagerReorderingBrowserTest::class,
                ColumnsBrowserTest::class,
                CheckboxBrowserTest::class,
                ColorPickerBrowserTest::class,
                DatabaseNotificationsBrowserTest::class,
                DatePickerBrowserTest::class,
                DateTimePickerBrowserTest::class,
                LazySchemaLoadingBrowserTest::class,
                DissociateBulkActionBrowserTest::class,
                FieldAccessibilityBrowserTest::class,
                FileUploadBrowserTest::class,
                FiltersModalBrowserTest::class,
                FiltersResetActionBrowserTest::class,
                IconBrowserTest::class,
                IndistinctStateBrowserTest::class,
                IndividualColumnSearchBrowserTest::class,
                InfolistEntriesBrowserTest::class,
                JsComponentBrowserTest::class,
                JsFieldFrameworkTest::class,
                JsWidgetBrowserTest::class,
                KeyValueBrowserTest::class,
                LazyLoadedRepeatableEntryBrowserTest::class,
                LazyLoadedStateBrowserTest::class,
                ManageSiteSettings::class,
                MarkdownEditorBrowserTest::class,
                ModalBrowserTest::class,
                NotificationBrowserTest::class,
                OneTimeCodeInputBrowserTest::class,
                OneTimeCodeInputSubmitOnCompletionBrowserTest::class,
                PartialRenderingBrowserTest::class,
                QueryBuilderTableBrowserTest::class,
                RadioBrowserTest::class,
                RepeaterBrowserTest::class,
                RichEditorBrowserTest::class,
                RichEditorMinimalControlsBrowserTest::class,
                SectionBrowserTest::class,
                SelectBrowserTest::class,
                Settings::class,
                SliderBrowserTest::class,
                StatsOverviewWidgetBrowserTest::class,
                TableActionGroupsBrowserTest::class,
                TableRenderHooksBrowserTest::class,
                TableReorderingBrowserTest::class,
                TabsBrowserTest::class,
                TagsInputBrowserTest::class,
                TextareaBrowserTest::class,
                TextInputBrowserTest::class,
                TimePickerBrowserTest::class,
                ToggleButtonsBrowserTest::class,
                ToggleBrowserTest::class,
                UnsavedChangesAlertBrowserTest::class,
                WizardBrowserTest::class,
                UserManagement::class,
                ManageAdmins::class,
                ManageStaff::class,
                GeneralSettings::class,
                WithoutSubNavigationCluster::class,
                ClusteredPageWithoutSubNavigation::class,
            ])
            ->livewireComponents([
                ChartWidgetWithAssistiveContent::class,
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
