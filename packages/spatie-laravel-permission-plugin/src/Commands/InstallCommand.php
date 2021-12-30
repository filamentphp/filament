<?php

namespace Filament\Commands;

use Filament\Facades\Filament;
use Filament\SpatieLaravelPermissionPlugin;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class InstallCommand extends Command
{
    use Concerns\CanManipulateFiles;

    protected $signature = 'filament:install-permissions';

    protected $description = 'Set up the filament permissions & GUI';

    public function __invoke(): int
    {
        if (! Schema::hasTable('permissions')) {
            $this->error('Seems, you have not run Spatie Laravel Permission migrations!');

            return static::INVALID;
        }

        $filesystem = new Filesystem();

        $baseResourcePath = app_path((string) Str::of('Filament\\Resources\\')->replace('\\', '/'), );

        $filesystem->ensureDirectoryExists($baseResourcePath);
        $filesystem->copyDirectory(__DIR__.'/../../stubs/resources', $baseResourcePath);

        $this->info('Roles GUI has been sacffoled successfully!');

        $entities = collect(Filament::getResources())
            ->reduce(function ($options, $resource) {
                $option = Str::before(Str::afterLast($resource, '\\'), 'Resource');
                $options[$option] = $option;

                return $options;
            }, collect())
            ->values()
            ->each(function ($entity) {
                $this->generatePolicies(Str::of($entity));
                SpatieLaravelPermissionPlugin::generateFor(Str::lower(Str::of($entity)));
            });

        $this->info('Successfully generated policies for '.implode(',', $entities->toArray()));

        $this->info('Successfully generated permissions for '.implode(',', $entities->toArray()));

        $this->comment('Enjoy!');

        return static::SUCCESS;
    }

    protected function generatePolicies(string $model): void
    {
        $prefixes = config('filament-spatie-laravel-permission-plugin.default_permission_prefixes');

        $basePolicyPath = app_path(
            (string) Str::of($model)
            ->prepend('Policies\\')
            ->replace('\\', '/'),
        );

        $policyPath = "{$basePolicyPath}Policy.php";
        $this->copyStubToApp('DefaultPolicy', $policyPath, [
            'modelPolicy' => "{$model}Policy",
            'view' => Str::of($prefixes[0].'_'.Str::lower($model)),
            'viewAny' => Str::of($prefixes[1].'_'.Str::lower($model)),
            'create' => Str::of($prefixes[2].'_'.Str::lower($model)),
            'delete' => Str::of($prefixes[3].'_'.Str::lower($model)),
            'deleteAny' => Str::of($prefixes[4].'_'.Str::lower($model)),
            'update' => Str::of($prefixes[5].'_'.Str::lower($model)),
        ]);
    }

    protected function copyStubToApp(string $stub, string $targetPath, array $replacements = []): void
    {
        $filesystem = new Filesystem();

        if (! $this->fileExists($stubPath = base_path("stubs/filament/{$stub}.stub"))) {
            $stubPath = __DIR__ . "/../../stubs/{$stub}.stub";
        }

        $stub = Str::of($filesystem->get($stubPath));

        foreach ($replacements as $key => $replacement) {
            $stub = $stub->replace("{{ {$key} }}", $replacement);
        }

        $stub = (string) $stub;

        $this->writeFile($targetPath, $stub);
    }
}
