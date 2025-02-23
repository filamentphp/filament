<?php

namespace Filament\Support\Commands\Concerns;

use Filament\Commands\FileGenerators\PanelProviderClassGenerator;
use Filament\Facades\Filament;
use Filament\Support\Commands\Exceptions\FailureCommandOutput;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

use function Laravel\Prompts\text;

trait CanGeneratePanels
{
    use CanManipulateFiles;

    public function generatePanel(?string $id = null, string $defaultId = '', string $placeholderId = '', bool $isForced = false): void
    {
        $id = Str::lcfirst($id ?? text(
            label: 'What is the panel\'s ID?',
            placeholder: $placeholderId,
            default: $defaultId,
            required: true,
            hint: 'It must be unique to any others you have, and is used to reference the panel in your code.',
        ));

        $basename = (string) str($id)
            ->studly()
            ->append('PanelProvider');

        $path = app_path(
            (string) str($basename)
                ->prepend('Providers/Filament/')
                ->replace('\\', '/')
                ->append('.php'),
        );

        if (! $isForced && $this->checkForCollision([$path])) {
            throw new FailureCommandOutput;
        }

        $fqn = "App\\Providers\\Filament\\{$basename}";

        if (empty(Filament::getPanels())) {
            $this->writeFile($path, app(PanelProviderClassGenerator::class, [
                'fqn' => $fqn,
                'id' => $id,
                'isDefault' => true,
            ]));
        } else {
            $this->writeFile($path, app(PanelProviderClassGenerator::class, [
                'fqn' => $fqn,
                'id' => $id,
            ]));
        }

        $hasBootstrapProvidersFile = file_exists($bootstrapProvidersPath = App::getBootstrapProvidersPath());

        if ($hasBootstrapProvidersFile) {
            ServiceProvider::addProviderToBootstrapFile(
                $fqn,
                $bootstrapProvidersPath,
            );
        } else {
            $appConfig = file_get_contents(config_path('app.php'));

            if (! Str::contains($appConfig, "{$fqn}::class")) {
                file_put_contents(config_path('app.php'), str_replace(
                    'App\\Providers\\RouteServiceProvider::class,',
                    "{$fqn}::class," . PHP_EOL . '        App\\Providers\\RouteServiceProvider::class,',
                    $appConfig,
                ));
            }
        }

        $this->components->info("Filament panel [{$path}] created successfully.");

        if ($hasBootstrapProvidersFile) {
            $this->components->warn("We've attempted to register the {$basename} in your [bootstrap/providers.php] file. If you get an error while trying to access your panel then this process has probably failed. You can manually register the service provider by adding it to the array.");
        } else {
            $this->components->warn("We've attempted to register the {$basename} in your [config/app.php] file as a service provider.  If you get an error while trying to access your panel then this process has probably failed. You can manually register the service provider by adding it to the [providers] array.");
        }
    }
}
