<?php

namespace Filament\Commands;

use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Filament\SpatieLaravelPermissionPlugin;

class MakePermissionCommand extends MakeResourceCommand
{
    use Concerns\CanManipulateFiles;
    use Concerns\CanValidateInput;

    protected $description = 'Creates Permissions for the given Resource';

    public function __construct()
    {
        $this->signature .= "
            {--P|permissions : Generate Permissions for the given Resource.}
        ";
        parent::__construct();
    }

    public function handle(): int
    {
        $prefixes = config('filament-spatie-laravel-permission-plugin.default_permission_prefixes');

        $model = (string) Str::of($this->argument('name') ?? $this->askRequired('Model (e.g. `BlogPost`)', 'name'))
            ->studly()
            ->beforeLast('Resource')
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->studly()
            ->replace('/', '\\');

        $choice = $this->choice('What would you like to Generate for the Resource?',[
            'Permissions & Policy',
            'Only Permissions',
            'Just the Resource, Thanks!'
        ], 2, null, false);
        
        $confirmation = $this->option('permissions') ?: $this->confirm('Generate Permissions for '.$model.'Resource ?');

        if ($confirmation) {

            // parent::handle();

            $basePolicyPath = app_path(
            (string) Str::of($model)
                ->prepend('Policies\\')
                ->replace('\\', '/'),
            );

            $policyPath = "{$basePolicyPath}Policy.php";

            $this->copyStubToApp('DefaultPolicy', $policyPath, [
                'modelPolicy' => "{$model}Policy",
                'viewAny' => Str::of($prefixes[0].Str::lower($model)),
                'view' => Str::of($prefixes[1].Str::lower($model)),
                'create' => Str::of($prefixes[2].Str::lower($model)),
                'update' => Str::of($prefixes[3].Str::lower($model)),
                'delete' => Str::of($prefixes[4].Str::lower($model)),
                'deleteAny' => Str::of($prefixes[5].Str::lower($model)),
            ]);

            // $output = $this->call('permission:cache-reset');
            SpatieLaravelPermissionPlugin::generateFor(Str::lower($model));
            $this->info("Successfully created permissions for ".$model."Resource");

        }
            parent::handle(); //uncomment to run the actual command after the extra added stuff is taken care of.

        return static::SUCCESS;
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
