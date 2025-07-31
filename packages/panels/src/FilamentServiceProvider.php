<?php

namespace Filament;

use Filament\Auth\Http\Responses\BlockEmailChangeVerificationResponse;
use Filament\Auth\Http\Responses\Contracts\BlockEmailChangeVerificationResponse as BlockEmailChangeVerificationResponseContract;
use Filament\Auth\Http\Responses\Contracts\EmailChangeVerificationResponse as EmailChangeVerificationResponseContract;
use Filament\Auth\Http\Responses\Contracts\EmailVerificationResponse as EmailVerificationResponseContract;
use Filament\Auth\Http\Responses\Contracts\LoginResponse as LoginResponseContract;
use Filament\Auth\Http\Responses\Contracts\LogoutResponse as LogoutResponseContract;
use Filament\Auth\Http\Responses\Contracts\PasswordResetResponse as PasswordResetResponseContract;
use Filament\Auth\Http\Responses\Contracts\RegistrationResponse as RegistrationResponseContract;
use Filament\Auth\Http\Responses\EmailChangeVerificationResponse;
use Filament\Auth\Http\Responses\EmailVerificationResponse;
use Filament\Auth\Http\Responses\LoginResponse;
use Filament\Auth\Http\Responses\LogoutResponse;
use Filament\Auth\Http\Responses\PasswordResetResponse;
use Filament\Auth\Http\Responses\RegistrationResponse;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Http\Middleware\IdentifyTenant;
use Filament\Http\Middleware\SetUpPanel;
use Filament\Navigation\NavigationManager;
use Filament\Support\Assets\Font;
use Filament\Support\Assets\Js;
use Filament\Support\Assets\Theme;
use Filament\Support\Facades\FilamentAsset;
use Filament\View\LegacyComponents;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-panels')
            ->hasCommands([
                Commands\CacheComponentsCommand::class,
                Commands\ClearCachedComponentsCommand::class,
                Commands\MakeClusterCommand::class,
                Commands\MakePageCommand::class,
                Commands\MakePanelCommand::class,
                Commands\MakeRelationManagerCommand::class,
                Commands\MakeResourceCommand::class,
                Commands\MakeThemeCommand::class,
                Commands\MakeUserCommand::class,
            ])
            ->hasRoutes('web')
            ->hasTranslations()
            ->hasViews();
    }

    public function packageRegistered(): void
    {
        $this->registerCoreServices();
        $this->registerResponseContracts();
        $this->registerMiddleware();
    }

    public function packageBooted(): void
    {
        $this->registerBladeComponents();
        $this->registerAssets();
        $this->registerLivewireMiddleware();
        $this->registerServingCallback();
        $this->publishStubs();
    }

    protected function registerCoreServices(): void
    {
        $this->app->scoped('filament', function (): FilamentManager {
            return new FilamentManager;
        });

        $this->app->alias('filament', FilamentManager::class);

        $this->app->singleton(PanelRegistry::class, function (): PanelRegistry {
            return new PanelRegistry;
        });

        $this->app->scoped(NavigationManager::class, function (): NavigationManager {
            return new NavigationManager;
        });
    }

    protected function registerResponseContracts(): void
    {
        $this->app->bind(BlockEmailChangeVerificationResponseContract::class, BlockEmailChangeVerificationResponse::class);
        $this->app->bind(EmailChangeVerificationResponseContract::class, EmailChangeVerificationResponse::class);
        $this->app->bind(EmailVerificationResponseContract::class, EmailVerificationResponse::class);
        $this->app->bind(LoginResponseContract::class, LoginResponse::class);
        $this->app->bind(LogoutResponseContract::class, LogoutResponse::class);
        $this->app->bind(PasswordResetResponseContract::class, PasswordResetResponse::class);
        $this->app->bind(RegistrationResponseContract::class, RegistrationResponse::class);
    }

    protected function registerMiddleware(): void
    {
        app(Router::class)->aliasMiddleware('panel', SetUpPanel::class);
    }

    protected function registerBladeComponents(): void
    {
        Blade::components([
            LegacyComponents\PageComponent::class => 'filament::page',
            LegacyComponents\WidgetComponent::class => 'filament::widget',
        ]);
    }

    protected function registerAssets(): void
    {
        FilamentAsset::register([
            Font::make('inter', __DIR__ . '/../dist/fonts/inter'),
            Js::make('app', __DIR__ . '/../dist/index.js')->core(),
            Js::make('echo', __DIR__ . '/../dist/echo.js')->core(),
            Theme::make('app', __DIR__ . '/../dist/theme.css'),
        ], 'filament/filament');
    }

    protected function registerLivewireMiddleware(): void
    {
        Livewire::addPersistentMiddleware([
            Authenticate::class,
            DisableBladeIconComponents::class,
            DispatchServingFilamentEvent::class,
            IdentifyTenant::class,
            SetUpPanel::class,
        ]);
    }

    protected function registerServingCallback(): void
    {
        Filament::serving(function (): void {
            Filament::setServingStatus();
        });
    }

    protected function publishStubs(): void
    {
        if ($this->app->runningInConsole()) {
            $filesystem = app(Filesystem::class);
            $stubsPath = __DIR__ . '/../stubs/';

            if ($filesystem->exists($stubsPath)) {
                foreach ($filesystem->files($stubsPath) as $file) {
                    $this->publishes([
                        $file->getRealPath() => base_path("stubs/filament/{$file->getFilename()}"),
                    ], 'filament-stubs');
                }
            }
        }
    }
}
