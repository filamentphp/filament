<?php

namespace Filament\Support\Commands\Concerns;

use Filament\Facades\Filament;
use Illuminate\Support\Str;

use function Laravel\Prompts\text;

trait CanGeneratePanels
{
    use CanManipulateFiles;

    public function generatePanel(?string $id = null, string $default = '', string $placeholder = '', bool $force = false): bool
    {
        $id = Str::lcfirst($id ?? text(
            label: 'What is the ID?',
            placeholder: $placeholder,
            default: $default,
            required: true,
        ));

        $class = (string) str($id)
            ->studly()
            ->append('PanelProvider');

        $path = app_path(
            (string) str($class)
                ->prepend('Providers/Filament/')
                ->replace('\\', '/')
                ->append('.php'),
        );

        if (! $force && $this->checkForCollision([$path])) {
            return false;
        }

        if (empty(Filament::getPanels())) {
            $this->copyStubToApp('DefaultPanelProvider', $path, [
                'class' => $class,
                'id' => $id,
            ]);
        } else {
            $this->copyStubToApp('PanelProvider', $path, [
                'class' => $class,
                'directory' => str($id)->studly(),
                'id' => $id,
            ]);
        }

        $appConfig = file_get_contents(config_path('app.php'));

        if (! Str::contains($appConfig, "App\\Providers\\Filament\\{$class}::class")) {
            file_put_contents(config_path('app.php'), str_replace(
                'App\\Providers\\RouteServiceProvider::class,',
                "App\\Providers\\Filament\\{$class}::class," . PHP_EOL . '        App\\Providers\\RouteServiceProvider::class,',
                $appConfig,
            ));
        }

        $this->components->info("Filament panel [{$path}] created successfully.");
        $this->components->warn("We've attempted to register the {$class} in your [config/app.php] file as a service provider.  If you get an error while trying to access your panel then this process has probably failed. You can manually register the service provider by adding it to the [providers] array.");

        return true;
    }
}
