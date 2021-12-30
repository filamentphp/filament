<?php

namespace Filament\Commands;

use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Filament\SpatieLaravelPermissionPlugin;

class ExtendsMakeResourceCommand extends MakeResourceCommand
{
    use Concerns\CanManipulateFiles;
    use Concerns\CanValidateInput;

    public function __construct()
    {
        $this->signature .= "
            {--P|permissions : Generate Permissions & Policy for Resource}
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

        $choice = $this->option('permissions')?:$this->choice('What would you like to Generate for the Resource?',[
            "With Permissions & Policy",
            "Only Permissions & Policy",
            "With Permissions",
            "Only Permissions",
            "Just the Resource, Thanks!",
        ], 4, null, false);

        $basePolicyPath = app_path(
            (string) Str::of($model)
            ->prepend('Policies\\')
            ->replace('\\', '/'),
        );

        $policyPath = "{$basePolicyPath}Policy.php";

        if ($this->option('permissions') || $choice === "With Permissions & Policy") { // generate Resource + Permission + Policy

            parent::handle();

            if ($this->checkForCollision([$policyPath])) {
                    return static::INVALID;
            }

            $this->copyStubToApp('DefaultPolicy', $policyPath, [
                'modelPolicy' => "{$model}Policy",
                'view' => Str::of($prefixes[0].'_'.Str::lower($model)),
                'viewAny' => Str::of($prefixes[1].'_'.Str::lower($model)),
                'create' => Str::of($prefixes[2].'_'.Str::lower($model)),
                'delete' => Str::of($prefixes[3].'_'.Str::lower($model)),
                'deleteAny' => Str::of($prefixes[4].'_'.Str::lower($model)),
                'update' => Str::of($prefixes[5].'_'.Str::lower($model)),
            ]);

            $this->info("Successfully generated {$model}Policy for {$model}Resource");

            SpatieLaravelPermissionPlugin::generateFor(Str::lower($model));

            $this->info("Successfully generated Permissions for ".$model."Resource");

        }
        else if ($choice === "Only Permissions & Policy") { // generate Permissions + Policy in case Resource already exists

            if ($this->checkForCollision([$policyPath])) {
                    return static::INVALID;
            }

            $this->copyStubToApp('DefaultPolicy', $policyPath, [
                'modelPolicy' => "{$model}Policy",
                'viewAny' => Str::of($prefixes[0].'_'.Str::lower($model)),
                'view' => Str::of($prefixes[1].'_'.Str::lower($model)),
                'create' => Str::of($prefixes[2].'_'.Str::lower($model)),
                'update' => Str::of($prefixes[3].'_'.Str::lower($model)),
                'delete' => Str::of($prefixes[4].'_'.Str::lower($model)),
                'deleteAny' => Str::of($prefixes[5].'_'.Str::lower($model)),
            ]);

            $this->info("Successfully generated {$model}Policy for {$model}Resource");

            SpatieLaravelPermissionPlugin::generateFor(Str::lower($model));

            $this->info("Successfully generated Permissions for ".$model."Resource");
        }
        else if ($choice === "With Permissions") { // generate Resouce + Permissions in case Policy already exists
            parent::handle();

            SpatieLaravelPermissionPlugin::generateFor(Str::lower($model));

            $this->info("Successfully generated Permissions for ".$model."Resource");
        }
        else if ($choice === "Only Permissions") { // generate only permissions in case Resource & Policy already exists

            SpatieLaravelPermissionPlugin::generateFor(Str::lower($model));

            $this->info("Successfully generated Permissions for ".$model."Resource");
        } else {
            parent::handle();
        }

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
