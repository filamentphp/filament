<?php

namespace Filament;

use Filament\Facades\Filament;
use Filament\Http\Livewire\Auth\Login;
use Filament\Http\Livewire\GlobalSearch;
use Filament\Http\Livewire\Notifications;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Http\Middleware\MirrorConfigToSubpackages;
use Filament\Http\Middleware\SetUpContext;
use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;
use Filament\Http\Responses\Auth\LoginResponse;
use Filament\Http\Responses\Auth\LogoutResponse;
use Filament\Pages\Page;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Resource;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\PluginServiceProvider;
use Filament\Widgets\Widget;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Livewire;
use ReflectionClass;
use Symfony\Component\Finder\SplFileInfo;

class FilamentServiceProvider extends PluginServiceProvider
{
    public static string $name = 'filament';

    public function packageRegistered(): void
    {
        parent::packageRegistered();

        $this->app->scoped('filament', function (): FilamentManager {
            return new FilamentManager();
        });

        $this->app->bind(LoginResponseContract::class, LoginResponse::class);
        $this->app->bind(LogoutResponseContract::class, LogoutResponse::class);

        $this->mergeConfigFrom(__DIR__ . '/../config/filament.php', 'filament');

        app(Router::class)->aliasMiddleware('context', SetUpContext::class);
    }

    public function packageBooted(): void
    {
        parent::packageBooted();

        Livewire::addPersistentMiddleware([
            Authenticate::class,
            DispatchServingFilamentEvent::class,
            MirrorConfigToSubpackages::class,
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

    protected function getAssetPackage(): ?string
    {
        return null;
    }

    protected function getAssets(): array
    {
        return [
            Css::make('app', __DIR__ . '/../dist/index.css')->html(Filament::getTheme()),
            Js::make('app', __DIR__ . '/../dist/index.js')->core(),
            Js::make('echo', __DIR__ . '/../dist/echo.js')->core(),
        ];
    }

    protected function getRoutes(): array
    {
        return ['web'];
    }

    protected function getCommands(): array
    {
        $commands = [
            Commands\MakeContextCommand::class,
            Commands\MakePageCommand::class,
            Commands\MakeRelationManagerCommand::class,
            Commands\MakeResourceCommand::class,
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

    protected function mergeConfig(array $original, array $merging): array
    {
        $array = array_merge($original, $merging);

        foreach ($original as $key => $value) {
            if (! is_array($value)) {
                continue;
            }

            if (! Arr::exists($merging, $key)) {
                continue;
            }

            if (is_numeric($key)) {
                continue;
            }

            if ($key === 'middleware' || $key === 'register') {
                continue;
            }

            $array[$key] = $this->mergeConfig($value, $merging[$key]);
        }

        return $array;
    }

    protected function mergeConfigFrom($path, $key): void
    {
        $config = $this->app['config']->get($key) ?? [];

        $this->app['config']->set($key, $this->mergeConfig(require $path, $config));
    }
}
