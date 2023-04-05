<?php

namespace Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Http\Middleware\SetUpContext;
use Filament\Http\Responses\Auth\Contracts\EmailVerificationResponse as EmailVerificationResponseContract;
use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;
use Filament\Http\Responses\Auth\Contracts\PasswordResetResponse as PasswordResetResponseContract;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse as RegistrationResponseContract;
use Filament\Http\Responses\Auth\EmailVerificationResponse;
use Filament\Http\Responses\Auth\LoginResponse;
use Filament\Http\Responses\Auth\LogoutResponse;
use Filament\Http\Responses\Auth\PasswordResetResponse;
use Filament\Http\Responses\Auth\RegistrationResponse;
use Filament\Support\Assets\AssetManager;
use Filament\Support\Assets\Js;
use Filament\Support\Assets\Theme;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Routing\Router;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament')
            ->hasCommands($this->getCommands())
            ->hasRoutes('web')
            ->hasTranslations()
            ->hasViews();
    }

    public function packageRegistered(): void
    {
        $this->app->scoped('filament', function (): FilamentManager {
            return new FilamentManager();
        });

        $this->app->bind(EmailVerificationResponseContract::class, EmailVerificationResponse::class);
        $this->app->bind(LoginResponseContract::class, LoginResponse::class);
        $this->app->bind(LogoutResponseContract::class, LogoutResponse::class);
        $this->app->bind(PasswordResetResponseContract::class, PasswordResetResponse::class);
        $this->app->bind(RegistrationResponseContract::class, RegistrationResponse::class);

        $this->app->resolving(AssetManager::class, function () {
            FilamentAsset::register([
                Js::make('app', __DIR__ . '/../dist/index.js')->core(),
                Js::make('echo', __DIR__ . '/../dist/echo.js')->core(),
                Theme::make('app', __DIR__ . '/../dist/theme.css'),
            ], 'filament/filament');
        });

        app(Router::class)->aliasMiddleware('context', SetUpContext::class);
    }

    public function packageBooted(): void
    {
        Livewire::addPersistentMiddleware([
            Authenticate::class,
            DisableBladeIconComponents::class,
            DispatchServingFilamentEvent::class,
            SetUpContext::class,
        ]);

        if ($this->app->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/filament/{$file->getFilename()}"),
                ], 'filament-stubs');
            }
        }
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        $commands = [
            Commands\CompileThemeCommand::class,
            Commands\MakeContextCommand::class,
            Commands\MakePageCommand::class,
            Commands\MakeRelationManagerCommand::class,
            Commands\MakeResourceCommand::class,
            Commands\MakeThemeCommand::class,
            Commands\MakeUserCommand::class,
        ];

        $aliases = [];

        foreach ($commands as $command) {
            $class = 'Filament\\Commands\\Aliases\\' . class_basename($command);

            if (! class_exists($class)) {
                continue;
            }

            $aliases[] = $class;
        }

        return array_merge($commands, $aliases);
    }
}
